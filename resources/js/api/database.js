/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      database.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   database.js
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
*  Imports the PHP Mongo Admin URL from the config.
*/
import { MONGO_CONFIG } from "../config";

export default {
    /*
    *   Get all databases
    *   GET /api/v1/databases
    */
    getDatabases: () => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/databases' );
    },

    /*
    *   Get a single database
    *   GET /api/vi/databases/{name}
    */
    getDatabase: ( name ) => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/databases/' + name );
    },

    /*
    *   Get a clean list of database names
    *   GET /api/vi/databases/list/all
    */
    getDatabaseList: () => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/databases/list/all' );
    },

    /*
    *   Create a new Database
    *   POST  /api/v1/databases/create
    */
    createDatabase: ( name ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/databases/create',
            {
                database: name,
                _token: window.window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Delete one or more Database(s)
    *   POST  /api/v1/databases/delete
    */
    deleteDatabase: ( names ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/databases/delete',
            {
                names: names,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Run a command against a database
    *   POST  /api/v1/databases/{database}/command
    */
    databaseCommand: ( data ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/databases/' + data.database + '/command',
            {
                database: data.database,
                params: data.params,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Transfer a database to another server
    *   POST  /api/v1/databases/{database}/transfer
    */
    transferDatabase: ( data ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/databases/' + data.params.database + '/transfer',
            {
                database: data.params.database,
                params: data.params,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Save a database logging profile
    *   POST  /api/v1/databases/{database}/profile
    */
    saveProfile: ( data ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/databases/' + data.database + '/profile',
            {
                params: data.params,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Fetch database profile logs
    *   GET  /api/v1/databases/{database}/profile
    */
    getProfile: ( data ) => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/databases/' + data.database + '/profile' );
    },

    /*
    *   Repair a database
    *   POST  /api/v1/databases/{database}/repair
    */
    repairDb: ( data ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/databases/' + data.database + '/repair',
            {
                database: data.database,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Fetch database auth users
    *   GET  /api/v1/databases/{database}/dbauth
    */
    getDbAuth: ( data ) => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/databases/' + data.database + '/dbauth' );
    },

    /*
    *   Save a database user
    *   POST  /api/v1/databases/{database}/dbauth
    */
    saveDbUser: ( data ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/databases/' + data.database + '/dbauth',
            {
                database: data.database,
                params: data.params,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Delete a database user
    *   POST  /api/v1/databases/{database}/dbauth/delete
    */
    deleteDbUser: ( data ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/databases/' + data.database + '/dbauth/delete',
            {
                database: data.database,
                user: data.user,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },
}
