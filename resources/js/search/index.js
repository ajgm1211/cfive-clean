import '../bootstrap';
import '../../sass/custom_app.scss';
import * as VueGoogleMaps from 'vue2-google-maps';

import Vue from 'vue';


import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import VueRouter from 'vue-router';
import VueNumericInput from 'vue-numeric-input';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import VueCkeditor from 'vue-ckeditor5';
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

/* Config files */
import App from './views/App'; // Main Component
import Api from '../api.js'; // Api calls controller
import VueMixpanel from 'vue-mixpanel';// MixPanel

// Install BootstrapVue
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);
Vue.use(VueRouter);
Vue.use(VueNumericInput);
Vue.use(VueMixpanel, {
    //token: "2e500c38f49acb5c4b9f576c63d527d7" //Development
    token: "3fddf65a971eccf21d956ba2ebbb293c"
});

const options = {
    editors: {
        classic: ClassicEditor,
    },
    name: 'ckeditor'
}

Vue.use(VueCkeditor.plugin, options);

Vue.use(VueToast, {
    position: 'top-right'
});

Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0',
        libraries: 'places',
    },
    installComponents: true
})

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


