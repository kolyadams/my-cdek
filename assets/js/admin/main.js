import {JSONViewer} from "./modules/json-viewer";
import "./main.scss";

let AppCdekAdmin = {
    state: {
        jsonPvz: null,
        jsonViewerPvz: null,
        jsonOrder: null,
        jsonViewerOrder: null,
    },

    init(){
        this.open();
    },

    open(){
        if(document.querySelector(".app-pvz") != null){
            this.state.jsonPvz = JSON.parse(document.querySelector(".app-pvz").textContent);
            this.state.jsonViewerPvz = new JSONViewer();
            document.querySelector(".app-pvz").replaceWith(this.state.jsonViewerPvz.getContainer());
            this.state.jsonViewerPvz.showJSON(this.state.jsonPvz, -1, 0);
        }
        if(document.querySelector(".app-order") != null){
            this.state.jsonOrder = JSON.parse(document.querySelector(".app-order").textContent);
            this.state.jsonViewerOrder = new JSONViewer();
            document.querySelector(".app-order").replaceWith(this.state.jsonViewerOrder.getContainer());
            this.state.jsonViewerOrder.showJSON(this.state.jsonOrder, -1, 0);
        }
        this.event();
    },

    event(){
        document.querySelector(".app-cdek__track").addEventListener("click", () => {
            this.regCdekOrder();
        })
    },

    async regCdekOrder(){
        let orderId = document.querySelector("#post_ID").value;
        let response = await fetch(
            `/?action=reg-order&order_id=${orderId}`
        );
        let data = await response.text();
        if(data == "true"){
            location.reload();
        }
    }
}

AppCdekAdmin.init();