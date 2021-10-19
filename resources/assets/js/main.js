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
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';


// Views
import Rates from "../views/pages/PriceLevels/Rates.vue";
import PriceLevels from "../views/pages/PriceLevels/Index.vue";

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
  "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImE5OTkyMzZhY2EyMmUwMjgxYzBlYTM3ZmJiYWY4YWVjNmMyNjdhYmRiNTliMzBhNTI5NGU0YzgwMGVlMDVhYzZiMmFlMzM2MzdiYjE0OGExIn0.eyJhdWQiOiIxIiwianRpIjoiYTk5OTIzNmFjYTIyZTAyODFjMGVhMzdmYmJhZjhhZWM2YzI2N2FiZGI1OWIzMGE1Mjk0ZTRjODAwZWUwNWFjNmIyYWUzMzYzN2JiMTQ4YTEiLCJpYXQiOjE2MzQwMzM1NzYsIm5iZiI6MTYzNDAzMzU3NiwiZXhwIjoxNjY1NTY5NTc2LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.sT_wHYBCBcyeeXoyxNL5ljRWc3_hxZkjwnp87yOWL933Fc_d3NfauTp9YECU9c3m6Rntz0glVJ4RnyFePBn5XTxElfS-HmwVRLn_ctMZRgIpo8YJyoPowJe6RWrPvzmv0C7JENNUZXt6NHkNwDWMEazrqPxrtRI-OrF8R5t4aJ4PALetsiXffcWH5MZW9hkOPwE8lS-KUm6mnmnGIaW9XvFS5PDUtF-h19nl3oAscM0m7grQ0ce5HUCSsafg-kni5FJQNFXkrj664L8bGxW2SA6qWXPdnr8L2xcSqCLQ_q2eNvrcydbKDlT8e4lxiA0Y3GeJ42WWaNpOTRqAhmvFQybb97GQrhXZKJZP8oGO-LmdmRELdDEpqpgMXp6FDstgL0XcDOaqz3p8BI0ifIXsu29GTh85OyNenex240-uR9S7dlb-0rZfNekuVLEGlDLQDxYep-1v9NvC7JCpfVzy7dlpxKVpqX1D5A7lbAWAzIKSbU_AuO-RT8EP-w3iCws25gnT8NzRnJDwIKYskfWnDxL8E13wOLB97sgkvxD4whipSNQCNKIcacosHRfbq5mh6m32V2TcXtnBZ5Qw6PUbN9B8ouSLxIgxQxWHlaAVEVTAfp4drnedcxpwnTWZzxJLiWgCF5AShFUF8yqUgjFwC8a2smkaUsdSEYVmNjdSrVg";

// USE
Vue.use(VueRouter);
Vue.use(TablePlugin);
Vue.use(Vuelidate);
Vue.use(VueCkeditor.plugin, options);
Vue.use(BootstrapVue);
Vue.component('v-select', vSelect)


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
