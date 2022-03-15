import Vue from 'vue'
import App from './layouts/DefaultLayout.vue'
import router from './router'
import store from './store'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import './assets/styles/sass/app.scss'
import Api from './api'
import Vuelidate from 'vuelidate'


// Install BootstrapVue
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(Vuelidate)
Vue.config.productionTip = false
window.api = new Api();

new Vue({
  router,
  Api,
  store,
  render: h => h(App)
}).$mount('#app')