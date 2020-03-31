import '../bootstrap';
import '../../sass/custom_app.scss';

import Vue from 'vue';



/* Config files */
import Show from '../components/contracts/Show';  // Main Component
import Api from '../api.js'; // Api calls controller

// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

window.api = new Api();

const app = new Vue({
	el: '#app',
	render: h => h(Show)
});