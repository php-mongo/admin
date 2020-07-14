/*
* --------------------------------------------
* VUEX store.js
* --------------------------------------------
* Builds the data store from all of the modules fro the application
*/

/*
*   Adds the promise polyfill for IE 11
*/
require( 'es6-promise' ).polyfill();

/*
*   Imports Vue & Vuex
*/
import Vue from 'vue';
import Vuex from 'vuex';

/*
*   Initialise Vuex on Vue
*/
Vue.use( Vuex );

/*
*   Imports all of the modules used in the application  to build the data store
*/
import { application } from './modules/application';

import { display } from './modules/display.js';

import { filters } from './modules/filters.js';

import { database } from "./modules/database";

import { server } from './modules/server.js';

import { users } from './modules/users.js';

const vuexStore = new Vuex.Store({
    modules: {
        application,
        display,
        filters,
        database,
        server,
        users
    }
});

/*
*   Export the data store
*/
export default vuexStore;
