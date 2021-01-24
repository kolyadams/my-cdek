<?php

class MyCdek
{
  protected static $_instance = null;

  private $cdek_api = null;
  private $woocommerce = null;

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  private function __construct()
  {
    $this->define_constants();
    //$this->define_tables();
    $this->includes();
    $this->init_hooks();
    $this->init_adminhooks();
  }

  private function define_constants()
  {
    define("MYCDEK_PLUGIN_DIR", plugin_dir_path(__DIR__)); //"D:\myproj\domains\rus.local\wp-content\plugins\my-cdek/"
    define("MYCDEK_PLUGIN_URL", plugin_dir_url(__DIR__)); //"http://rus.local/wp-content/plugins/my-cdek/"
  }

  private function includes()
  {
    include "shipping/filter-shipping.php";
  }

  private function init_hooks()
  {
    add_action("wp_loaded", [$this, "init_ajaxpoints"]);
    add_action("activated_plugin", [$this, "activated_plugin"]);
    add_action("deactivated_plugin", [$this, "deactivated_plugin"]);

    //woocommerce_shipping_init нужен, потому что иначе не доступен класс WC_Shipping_Method
    add_action("woocommerce_shipping_init", function () {
      include "shipping/AbstractMyCdek.php";
      include "shipping/MyCdek136.php";
      include "shipping/MyCdek137.php";
      include "shipping/MyCdek368.php";
      include "shipping/MyCdek233.php";
      include "shipping/MyCdek234.php";
      include "shipping/MyCdek378.php";
      include "shipping/MyCdek15.php";
      include "shipping/MyCdek16.php";
    });
    add_action("wp_enqueue_scripts", function () {
      if (is_checkout()) {
        wp_enqueue_script(
          "ymap",
          "https://api-maps.yandex.ru/2.1/?apikey=005ab5a2-91c7-4f2b-9996-896e73a0ec63&lang=ru_RU",
          ["jquery"]
        );
        wp_enqueue_script("appcdek", MYCDEK_PLUGIN_URL . "assets/js/AppCdek.js", ["ymap"]);
        //wp_enqueue_style('cdek', MYCDEK_PLUGIN_URL . 'assets/css/style.css');
        //<script src="https://api-maps.yandex.ru/2.1/?apikey=005ab5a2-91c7-4f2b-9996-896e73a0ec63&lang=ru_RU" type="text/javascript"></script>
      }
    });
    add_filter("woocommerce_shipping_methods", function ($methods) {
      $methods["mycdek_pvz_136"] = "MyCdek136";
      $methods["mycdek_137"] = "MyCdek137";
      $methods["mycdek_pvz_postamat_368"] = "MyCdek368";
      $methods["mycdek_233"] = "MyCdek233";
      $methods["mycdek_pvz_234"] = "MyCdek234";
      $methods["mycdek_pvz_postamat_378"] = "MyCdek378";
      $methods["mycdek_pvz_15"] = "MyCdek15";
      $methods["mycdek_16"] = "MyCdek16";
      return $methods;
    });
    add_action(
      "woocommerce_checkout_before_customer_details",
      function () {
        include MYCDEK_PLUGIN_DIR . "includes/view.php";
      },
      12
    );
    add_action("woocommerce_checkout_process", function () {
      // Show an error message if the field is not set.
      $pos = strpos($_POST["shipping_method"][0], "mycdek"); //любой пункт mycdek
      $pospvz = strpos($_POST["shipping_method"][0], "pvz"); //pvz
      $pospostamat = strpos($_POST["shipping_method"][0], "postamat"); //postamat
      if (($pospvz !== false || $pospostamat !== false) && empty($_POST["pvz"])) {
        wc_add_notice(__("Вам необходимо выбрать пункт выдачи в приложении Яндекс Карты"), "error");
      }
      if (
        $pos !== false &&
        $pospvz === false &&
        $pospostamat === false &&
        (!$_POST["billing_address_1"] || !$_POST["billing_house"])
      ) {
        wc_add_notice(
          __("При доставке через ТК СДЭК в режиме Курьером, поля Улица и Дом являются обязательными"),
          "error"
        );
      }
    });
    /*
    Добавляем метаданные в метод доставки после покупки товаров
    */
    add_action(
      "woocommerce_checkout_update_order_meta",
      function ($order_id, $data) {
        $pos = strpos($data["shipping_method"][0], "mycdek");
        if ($pos !== false) {
          //Получаем заказ
          $order = wc_get_order($order_id);
          //Получаем из заказа метод доставки
          $key = key($order->get_items("shipping"));
          $shipping_method = $order->get_shipping_methods()[$key];

          //Определяем есть ли среди товаров в корзине, товары у которых в админке не указан вес
          $miss_prod = [];
          $items = $order->get_items();

          foreach ($items as $value) {
            $product = wc_get_product($value->get_product_id());
            if ($product->get_weight() == "") {
              $miss_prod[] = $product->get_name();
            }
          }

          /*
        $delivery = json_decode(stripslashes($_POST["delivery"]), true);
        $w1 = stripslashes($_POST["delivery"]);
        $w2 = '"test"';
        */
          //Записываем в метод доставки информацию о товарах без веса
          if (!empty($miss_prod)) {
            $shipping_method->update_meta_data(
              "Товары без веса",
              "<span class=cdek-warning>НЕЛЬЗЯ ОТПРАВИТЬ ЗАЯВКУ В СДЭК, т.к. для следующих товаров вес не указан, стоимость доставки была рассчитана без учета веса этих товаров <br>" .
                implode(", ", $miss_prod) .
                "</span>"
            );
          } else {
            $shipping_method->update_meta_data(
              "Товары без веса",
              "<span class=cdek-success>Товаров без веса не обнаружено. Можно отправлять заявку в СДЭК</span>"
            );
          }

          $shipping_method->update_meta_data("Общий вес товара", sanitize_text_field($_POST["weight"] . " гр"));

          //Записываем в метод доставки информацию о данных для типа доставки Самовывоз
          if (isset($_POST["pvz"]) && !empty($_POST["pvz"])) {
            $shipping_method->update_meta_data("Тип доставки", "Самовывоз");
            $shipping_method->update_meta_data(
              "Данные пункта выдачи",
              "<span class=app-pvz>" . stripslashes($_POST["pvz"]) . "</span>"
            );
            $shipping_method->save();
          } else {
            $shipping_method->update_meta_data("Тип доставки", "Курьером");
            $shipping_method->save();
          }
        }
      },
      10,
      2
    );
    add_action("updateCdekPvzHook", "updateCdekPvz");
    function updateCdekPvz()
    {
      $cdekapi = CdekApi::instance();
      $cdekapi->get_pvz();
    }
  }

  private function init_adminhooks()
  {
    if (is_admin()) {
      /**
       * Admin скрипты Admin CDEK инициализация
       */
      add_action("admin_enqueue_scripts", function ($hook) {
        if ("post.php" != $hook && "post-new.php" != $hook) {
          return;
        }
        wp_enqueue_script("cdek-admin-init", MYCDEK_PLUGIN_URL . "assets/js/AppCdekAdmin.js");
      });

      /**
       * Правка параметров подключения для скрипта CDEK widget
       */
      add_filter(
        "script_loader_tag",
        function ($tag, $handle, $src) {
          if ($handle == "cdek-admin-init") {
            $tag = sprintf('<script type="text/javascript" src="%s" defer></script>', $src);
          }
          return $tag;
        },
        10,
        3
      );

      /**
       * Кнопка запускающая отправку данных по заказу в CDEK (через аякс функцию) //JS скрипт для кнопки описан в /js/main-admin.js
       */
      add_action("woocommerce_order_item_add_action_buttons", function () {
        echo '<button type="button" class="button app-cdek__track" data-editor="content"><span class="my-buttons-icon"></span>Сформировать заявку в CDEK</button>';
      });
    }
  }

  public function init_ajaxpoints()
  {
    $cdekapi = CdekApi::instance();
    if (isset($_GET["action"]) && $_GET["action"] == "reg-order") {
      $cdekapi->reg_order();
    }
  }

  public function activated_plugin()
  {
    //при активации плагина
    //загружаем список пвз
    $cdekapi = CdekApi::instance();
    $cdekapi->get_pvz();

    //создаем задачу в планировщике на загрузку пвз по расписанию
    if (!wp_next_scheduled("updateCdekPvzHook")) {
      wp_schedule_event(time(), "daily", "updateCdekPvzHook");
    }
  }

  public function deactivated_plugin()
  {
    //при деактивации плагина
    //удаляем задачу из планировщика
    wp_clear_scheduled_hook("updateCdekPvzHook");
  }
}