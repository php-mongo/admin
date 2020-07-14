import {MONGO_CONFIG} from "./config";

/**
*   First we will load all of this project's JavaScript dependencies which
*   includes Vue and other libraries. It is a great starting point when
*   building robust, powerful web applications using Vue and Laravel.
*/
window._ = require('lodash');

/**
*   We'll load jQuery and the Foundation-Sites library.
*/
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('foundation-sites');

} catch (e) {}


/**
*   We'll load the axios HTTP library which allows us to easily issue requests
*   to our Laravel back-end. This library automatically handles sending the
*   CSRF token as a header based on the value of the "XSRF" token cookie.
*/
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
*   Next we will register the CSRF Token as a common header with Axios so that all
*   outgoing HTTP requests automatically have it attached. This just a simple
*   convenience so we don't have to attach every token manually
*/
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;

} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
*   Load Vue
*
*   @type {{PluginObject: PluginObject; VNode: VNode; PropType: PropType; DirectiveOptions: DirectiveOptions;
*   Component: Component; VNodeData: VNodeData; FunctionalComponentOptions: FunctionalComponentOptions;
*   VNodeChildrenArrayContents: VNodeChildrenArrayContents; VueConstructor: VueConstructor; DirectiveFunction: DirectiveFunction;
*   PluginFunction: PluginFunction; PropOptions: PropOptions; VNodeChildren: VNodeChildren; RenderContext: RenderContext;
*   VNodeDirective: VNodeDirective; ComputedOptions: ComputedOptions; WatchOptions: WatchOptions; WatchHandler: WatchHandler;
*   CreateElement: CreateElement; ComponentOptions: ComponentOptions; VNodeComponentOptions: VNodeComponentOptions;
*   WatchOptionsWithHandler: WatchOptionsWithHandler; AsyncComponent: AsyncComponent}}
*/
import Vue from 'vue';

/**
 * Load & use the Vue cookie monster
 */
import VueCookies from 'vue-cookie';
Vue.use(VueCookies);

/**
 *   Add axios as a prototype to $http and add the token if its set
 */
Vue.prototype.$http = window.axios;
const passport = JSON.parse( localStorage.getItem('token') ) || '';
if (passport) {
    Vue.prototype.$http.defaults.headers.common['Authorization'] = passport;
}

/**
*   Load the Vue Router
*/
import router from './routes.js';

/**
*   Load the Vuex store (module data storage)
*/
import store from './store.js';

/**
*   Load the translate service and define in the Vue creator
*/
import makeTrans from './services/translate.js';
let userLang = navigator.language || navigator.userLanguage,
    locale = 'en',
    currentLang = JSON.parse(localStorage.getItem('language'));
if (currentLang) {
    locale = currentLang;
}
if (!currentLang) {
    axios.get( MONGO_CONFIG.WEB_URL + '/js/acceptLang.js' )
    .then( (response) => {
        locale = userLang === response.data ? userLang : response.data;
    });
}
const trans = makeTrans( locale );

import makeCountries from './services/countries.js';
const countries = makeCountries();

/**
 *   Load the JQF methods to mock jQuery methods
 */
import makeJqf from './services/jqf.js';
const jqf = makeJqf( 'en' );

/**
*   Next, we will create a fresh Vue application instance and attach it to
*   the page. Then, you may begin adding components to this application
*   or customize the JavaScript scaffolding to fit your unique needs.
*/
new Vue({
    router,
    store,
    trans,
    jqf

}).$mount('#app');
