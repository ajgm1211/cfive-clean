/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
 
require('./bootstrap');
 
window.Vue = require('vue');

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';

import Api from './api.js'; // Api calls controller
 
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
//Vue.component('ocean-freight', require('./components/contracts/Freight.vue'));
//Vue.component('restrictions', require('./components/Restrictions.vue'));
//Vue.component('remarkes', require('./components/Remarkes.vue'));
//Vue.component('files', require('./components/Files.vue'));

window.api = new Api();
 
Vue.component('show-component', require('./components/quotes/Show.vue'));
 
const appElement = document.getElementById('app');

if (appElement) {
    const app = new Vue({
        el: '#app'
    });
}

