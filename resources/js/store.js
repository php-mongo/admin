/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      store.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   store.js
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
 *  See COPYRIGHT.php for copyright notices and further details.
 */

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

import { display } from './modules/display';

import { filters } from './modules/filters';

import { database } from "./modules/database";

import { collection } from "./modules/collection"

import { server } from './modules/server';

import { users } from './modules/users';

const vuexStore = new Vuex.Store({
    modules: {
        application,
        display,
        filters,
        database,
        collection,
        server,
        users
    }
});

/*
*   Export the data store
*/
export default vuexStore;
