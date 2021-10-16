// IMPORTS
import Vue from "vue";
import VueRouter from "vue-router";
import { TablePlugin, BootstrapVue } from "bootstrap-vue";
import Vuelidate from "vuelidate";
import Main from "../views/Main.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import VueCkeditor from "vue-ckeditor5";
import Api from "../../../resources/js/api"; // Api calls controller
import "../../../resources/sass/custom_app.scss";
import "../../../resources/js/bootstrap";
import store from "../js/store/index";

// Views
import PriceLevels from "../views/pages/PriceLevels/index.vue";
import Rates from "../views/pages/PriceLevels/Rates.vue";

// Const
const options = {
  editors: {
    classic: ClassicEditor,
  },
  name: "ckeditor",
};

// Routes
const router = new VueRouter({
  mode: "history",
  routes: [
    {
      path: "/prices/v2",
      name: "price-levels",
      component: PriceLevels,
    },
    {
      path: "/prices/rates/:id",
      name: "price-rates",
      component: Rates,
    },
  ],
});

let token =
  "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjEyMGI4NmVkMWI5YzRiOGRiYjA1NTE3M2NmNDAzYWViYTUxOTljZTY0OWE4YzUwNTY3MWE1NjQ0NGM0MGU3ZWUzNWU4Zjk4ZDE5MzcwZTdmIn0.eyJhdWQiOiI1MiIsImp0aSI6IjEyMGI4NmVkMWI5YzRiOGRiYjA1NTE3M2NmNDAzYWViYTUxOTljZTY0OWE4YzUwNTY3MWE1NjQ0NGM0MGU3ZWUzNWU4Zjk4ZDE5MzcwZTdmIiwiaWF0IjoxNjIxNjE0MjQ4LCJuYmYiOjE2MjE2MTQyNDgsImV4cCI6MTY1MzE1MDI0OCwic3ViIjoiMSIsInNjb3BlcyI6W119.N1VCM3H8XXM4UgTX1yhw8PPQNo-kyOlUaRyeryk6Q22Jc46GnP7An4NBu6FXjbaxWUyGWmYK3KqFZoL9kv20CVfUg7-nVm5VkbxGqVqY8FJ6ygR9rgHlO32bLDn90ZubqtK3Yrcg-etW8xPw0cEAGgm-4t_fxImN3hwQWvceUnkqfd9exdxH8RW7fSItLcjhDzpS6QFnKbI-vygYj7TGW9z2Zzmq2Hl2p4JJhQheCw9PId1Fe3Ldspm3Lf9YLXkNnwlAdIi7eEvjmC4haE9sAP1mvsHMsO7l-7sob7nbCZrLdlurAguSlzDz-oIvxczdalFERlyb57uZBvqIq2gp9puZ-r2v8yFoWXgsj807La9SPkBC-GWQCWsrsRwuAn2R3V_a9AczFPSt9tGt1tOIeqxELaZmeovHqszk4_9A7eonxq0PD_KheDNMxSgfS0VBMKTTycFDqsIdi1RxxIeg7slXpOB6VfeXKXoKWuj_ho8fLFlzRFyxl9aA9pocZ81ywIr3vzJkdPP5gXbAOd36xoh9-rQl1YYCrDiewWmEzlGb5-I6TTbQQ3N8N9tyM2HdsCUSt_8346W9fLIfUOrPrneZexQecZ90xcf3ehzYWYt4WpfiT3LebVufoBIVbA9ScsvTXcOjuSl0R_latTZj8bd26XNuuvLIBULQJh3KTs8";

// USE
Vue.use(VueRouter);
Vue.use(TablePlugin);
Vue.use(Vuelidate);
Vue.use(VueCkeditor.plugin, options);
Vue.use(BootstrapVue);

window.api = new Api();
window.Vuex = require("vuex");
axios.defaults.baseURL = process.env.MIX_APP_URL;
axios.defaults.headers.common["Authorization"] = token;

const app = new Vue({
  el: "#main",
  router: router,
  store,
  render: (h) => h(Main),
});

export default router;
