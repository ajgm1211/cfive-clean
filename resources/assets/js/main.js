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
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import 'vue-select/dist/vue-select.css';


// Views
import Rates from "../views/pages/PriceLevels/Rates.vue";
import PriceLevels from "../views/pages/PriceLevels/Index.vue";
import CompanyUsers from "../views/pages/Integrations/ApiCredentials/CompanyUsers.vue";
import ApiProviders from "../views/pages/Integrations/ApiCredentials/ApiProviders.vue";
import Segments from "../views/pages/Segments/Index.vue";

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
      path: "/pricelevels",
      name: "price-levels",
      component: PriceLevels,
    },
    {
      path: "/pricelevels/edit/:id",
      name: "price-rates",
      component: Rates,
    },
    {
      path: "/api-credentials",
      name: "api-credentials",
      component: CompanyUsers,
    },
    {
      path: "/api-credentials/company-user/:id",
      name: "api-credentials-by-company-user",
      component: ApiProviders,
    },
    {
      path: "/segment-configuration",
      name: "segment-configuration",
      component:Segments,
    },
  ],
});

// USE
Vue.use(VueRouter);
Vue.use(TablePlugin);
Vue.use(Vuelidate);
Vue.use(VueCkeditor.plugin, options);
Vue.use(BootstrapVue);
Vue.component('v-select', vSelect)
Vue.use(VueToast, {
  position: 'top-right'
});


window.api = new Api();
window.Vuex = require("vuex");
axios.defaults.baseURL = process.env.MIX_APP_URL;

const app = new Vue({
  el: "#main",
  router: router,
  store,
  render: (h) => h(Main),
});


export default router;
