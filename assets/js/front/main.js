import React from "react";
import ReactDOM from "react-dom";
import "./main.scss";

export let AppCdek = {
  state: {
    root: null,
    coordinates: null,
    cdekCityCode: null,
    json: null,
    currentPvz: null,
    pvzType: null,
    weight: null,
    prevPlacemark: null,
    ymapsObjs: {
      map: null,
      clusterer: null,
      placemarkArr: null,
    },
  },

  init() {
    jQuery(document).ajaxComplete(() => {
      if (
        typeof document.querySelector("#shipping_method [checked]") == "object" &&
        document.querySelector("#shipping_method [checked]").value.search(/.*mycdek.*/g) != -1
      ) {
        this.createApp();
      } else {
        this.destroyApp();
      }
    });
  },

  createApp() {
    this.destroyApp();
    if (
      typeof document.querySelector("#shipping_method [checked]") == "object" &&
      document.querySelector("#shipping_method [checked]").value.search(/.*pvz.*/g) != -1
    ) {
      this.setPvzState();
    } else {
      this.setGeneralState();
    }
  },

  destroyApp() {
    if (this.state.root !== null) {
      this.destroyHtml();
      this.resetState();
    }
  },

  resetState() {
    this.state = {
      root: null,
      coordinates: null,
      cdekCityCode: null,
      json: null,
      currentPvz: null,
      pvzType: null,
      weight: null,
      prevPlacemark: null,
      ymapsObjs: {
        map: null,
        clusterer: null,
        placemarkArr: null,
      },
    };
  },

  destroyHtml() {
    if (this.state.root !== null) {
      this.state.root.remove();
      document.querySelector(".my-cdek__pvz").value = "";
    }
  },

  async setPvzState() {
    this.createRoot();
    this.renderRaw();
    this.setGeneralState();
    await this.setAdvancedState();
    await this.createRenderObjs();
    this.renderMap();
    this.events();
  },

  createRoot() {
    if (this.state.root == null) {
      let root = document.createElement("div");
      root.className = "cdek-map__root";
      document.querySelector(".shop_table").after(root);
      this.state.root = root;
    }
  },

  renderRaw() {
    ReactDOM.render(
      <>
        <div className="map" id="map"></div>
        {this.state.currentPvz != null && (
          <div className="map-choice">
            <div className="map-choice__col">Вы выбрали пункт выдачи</div>
            <div className="map-choice__col">{this.state.currentPvz.name}</div>
            <div className="map-choice__col">Адресс пункта выдачи</div>
            <div className="map-choice__col">{this.state.currentPvz.location.address}</div>
            <div className="map-choice__col">Комментарий пункта выдачи</div>
            <div className="map-choice__col">{this.state.currentPvz.address_comment}</div>
            <div className="map-choice__col">Рабочее время пункта выдачи</div>
            <div className="map-choice__col">{this.state.currentPvz.work_time}</div>
            <div className="map-choice__col">Телефон пункта выдачи</div>
            <div className="map-choice__col">{this.state.currentPvz.phones.map((v) => v.number).join(", ")}</div>
          </div>
        )}
      </>,
      this.state.root
    );
  },

  async setGeneralState() {
    this.getPvzType();
    this.getWeight();
    this.getCdekCityCode();
  },

  async setAdvancedState() {
    this.getCoordinates();
    await this.getJson();
  },

  getPvzType() {
    if (document.querySelector("#shipping_method [checked]").value.search(/.*postamat.*/g) != -1) {
      this.state.pvzType = "postamat";
    } else {
      this.state.pvzType = "pvz";
    }
  },

  getWeight() {
    this.state.weight = document.querySelector(".my-cdek__weight").value;
  },

  getCdekCityCode() {
    this.state.cdekCityCode = document.querySelector(".my-geo__cdek_city_code").value;
  },

  getCoordinates() {
    this.state.coordinates = JSON.parse(document.querySelector(".my-geo__coordinates").value);
  },

  async getJson() {
    let response = await fetch(
      `/wp-content/plugins/my-cdek/assets/json/pvz_${this.state.cdekCityCode}.json`
    );
    this.state.json = await response.json();
  },

  async createRenderObjs() {
    if (this.state.ymapsObjs.map == null && this.state.ymapsObjs.clusterer == null) {
      await this.createMap();
      await this.createClusterer();
    }
  },

  async createMap() {
    this.state.ymapsObjs.map = await new ymaps.Map("map", {
      // Координаты центра карты.
      // Порядок по умолчанию: «широта, долгота».
      // Чтобы не определять координаты центра карты вручную,
      // воспользуйтесь инструментом Определение координат.
      center: this.state.coordinates,
      controls: ["zoomControl"],
      // Уровень масштабирования. Допустимые значения:
      // от 0 (весь мир) до 19.
      zoom: 10,
    });
  },

  async createClusterer() {
    this.state.ymapsObjs.clusterer = await new ymaps.Clusterer({
      gridSize: 50,
      preset: "islands#ClusterIcons", //'#0a8c37'
      clusterIconColor: "#0a8c37",
      hasBalloon: false,
      groupByCoordinates: false,
      clusterDisableClickZoom: false,
      //maxZoom: 11,
      zoomMargin: [45],
      clusterHideIconOnBalloonOpen: false,
      geoObjectHideIconOnBalloonOpen: false,
    });
  },

  renderMap() {
    this.createPlacemarkArr();
    this.fillCluster();
    this.addClusterToMap();
  },

  createPlacemarkArr() {
    let placemarkArr = [];
    for (let [key, value] of Object.entries(this.state.json[this.state.pvzType])) {
      if (
        (this.state.weight >= value.weight_min * 1000 && this.state.weight < value.weight_max * 1000) ||
        (value.weight_min == undefined && value.weight_max == undefined)
      ) {
        let Placemark = new window.ymaps.Placemark(
          [value.location.latitude, value.location.longitude],
          {},
          {
            iconLayout: "default#image",
            iconImageHref: "/wp-content/plugins/my-cdek/assets/img/sdekNActive.png",
            iconImageSize: [40, 43],
            iconImageOffset: [-10, -31],
          }
        );
        Placemark.link = key;
        placemarkArr.push(Placemark);
      }
    }
    this.state.ymapsObjs.placemarkArr = placemarkArr;
  },

  fillCluster() {
    this.state.ymapsObjs.clusterer.removeAll();
    this.state.ymapsObjs.clusterer.add(this.state.ymapsObjs.placemarkArr);
  },

  addClusterToMap() {
    this.state.ymapsObjs.map.geoObjects.add(this.state.ymapsObjs.clusterer);
    try {
      this.state.ymapsObjs.map.setBounds(this.state.ymapsObjs.clusterer.getBounds(), {
        checkZoomRange: true,
        duration: 500,
      });
    } catch (err) {
      console.log("В кластере нет объектов");
      console.log(err);
    }
  },

  events() {
    this.add_event_placemark_click();
  },

  add_event_placemark_click() {
    this.state.ymapsObjs.map.geoObjects.events.add("click", (e) => {
      //Получаем метку инициатора
      let target = e.get("target");

      //Если предыдущая метка существует сбрасываем ее иконку на не активную
      if (this.state.prevPlacemark !== null) {
        this.state.prevPlacemark.options.set({
          iconImageHref: "/wp-content/plugins/my-cdek//assets/img/sdekNActive.png",
        });
      }
      //Помещаем текущую метку в предыдущую
      this.state.prevPlacemark = target;
      //Устанавливаем текущей метке активную иконку
      target.options.set({
        iconImageHref: "/wp-content/plugins/my-cdek//assets/img/sdekActive.png",
      });

      //Смещаем активную метку в центр карты с учетом марджина карты
      this.state.ymapsObjs.map.panTo(target.geometry._coordinates, {
        duration: 0,
      });

      //Выбираем пункт выдачи
      this.state.currentPvz = this.state.json[this.state.pvzType][target.link];
      this.renderRaw();
      document.querySelector(".my-cdek__pvz").value = JSON.stringify(this.state.currentPvz);
    });
  },
};

jQuery(function(){
    AppCdek.init();
});