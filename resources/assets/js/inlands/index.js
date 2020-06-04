import '../bootstrap';
import '../../sass/custom_app.scss';


import Vue from 'vue';


import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import VueRouter from 'vue-router';

/* Config files */
import App from './views/App';  // Main Component
import Api from '../api.js'; // Api calls controller

// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [],
});

window.api = new Api();

const app = new Vue({
    el: '#app',
    render: h => h(App),
    router: router,
});