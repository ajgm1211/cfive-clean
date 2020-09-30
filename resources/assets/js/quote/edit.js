import '../bootstrap';
import '../../sass/custom_app.scss';


import Vue from 'vue';
import VueRouter from 'vue-router';

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import VueCkeditor from 'vue-ckeditor5';
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';

/* Config files */
import Quote from './views/Quote'; // Main Component
import Api from '../api.js'; // Api calls controller

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VueRouter)
Vue.use(VueToast, {
    position: 'top-right'
});

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