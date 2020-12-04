import '../bootstrap';
import '../../sass/custom_app.scss';


import Vue from 'vue';
import VueRouter from 'vue-router';

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';

/* Config files */
import Saleterms from './views/Saleterms'; // Main Component
import Api from '../api.js'; // Api calls controller

// Install BootstrapVue
Vue.use(BootstrapVue)
    // Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [{
        path: '/api/sale_terms/:id/edit',
        name: 'Saleterms'
    }],
});

window.api = new Api();

const app = new Vue({
    el: '#app',
    render: h => h(Saleterms),
    router: router
});