import '../bootstrap';
import '../../sass/custom_app.scss';


import Vue from 'vue';
import VueRouter from 'vue-router';

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import VueCkeditor from 'vue-ckeditor5';

/* Config files */
import Quote from './views/Quote'; // Main Component
import Api from '../api.js'; // Api calls controller

// Install BootstrapVue
Vue.use(BootstrapVue)
    // Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
Vue.use(VueRouter)

const options = {
    editors: {
        classic: ClassicEditor,
    },
    name: 'ckeditor'
}

Vue.use(VueCkeditor.plugin, options);


const router = new VueRouter({
    mode: 'history',
    routes: [{ 
        path: '/api/quote/:id/edit', 
        name: 'quotes'
    }],
});

window.api = new Api();

const app = new Vue({
    el: '#app',
    render: h => h(Quote),
    router: router
});