// IMPORTS
import Vue from "vue";
import VueRouter from "vue-router";
import TablePlugin from "bootstrap-vue";
import Vuelidate from "vuelidate";
import Main from "../views/Main.vue";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import VueCkeditor from 'vue-ckeditor5';
import Api from '../../../resources/js/api'; // Api calls controller
import '../../../resources/sass/custom_app.scss'




// Routes
//import PriceLevels from "../views/pages/PriceLevels/index.vue";
//import Rates from "../views/pages/PriceLevels/Rates.vue";






// Const
const options = {
  editors: {
    classic: ClassicEditor,
  },
  name: "ckeditor",
};

const router = new VueRouter({
  mode: "history",
  routes: [/**
    {
      path: "/prices/v2",
      name: "price-levels",
      component: PriceLevels,
    },
    {
      path: "/prices/rates",
      name: "price-rates",
      component: Rates,
    },**/
  ],
});




// USE
Vue.use(VueRouter);
Vue.use(TablePlugin);
Vue.use(Vuelidate);
Vue.use(VueCkeditor.plugin, options);

window.api = new Api();

const app = new Vue({
  el: "#main",
  router: router,
  render: (h) => h(Main),
});
