/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      user.js 1002 8/8/21, 2:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   user.js
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
*   Imports the PHP Mongo Admin URL from the config.
*/
import { MONGO_CONFIG } from '../config.js'

export default {
    /*
    *  GET   /api/v1/user - get the current authenticated user
    */
    getUser: () => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/user' )
    },

    /*
    *   GET  /api/v1/user/all - gets all users visible by current user
    */
    getUsers: () => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/user/all' )
    },

    /*
    *  PUT  /api/v1/user - create or update a user
    */
    putUpdateUser: ( dataObj ) => {
        return window.axios.put( MONGO_CONFIG.API_URL + '/user',
            {
                ...dataObj,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            })
    },

    /*
    *  POST  /api/v1/user - register a new user
    */
    postUser: ( data ) => {
        let dataObj = {
            active: data.active,
            database: data.database,
            isAdmin: data.isAdmin,
            password: data.password,
            password2: data.password2,
            roles: data.roles,
            type: data.type,
            user: data.user,
        }
        // only added these if they exist so they dont get validated as empty vars
        if (data.email) {
            dataObj.email = data.email
        }
        if (data.name) {
            dataObj.name = data.name
        }
        return window.axios.post( MONGO_CONFIG.API_URL + '/user',
            {
                ...dataObj,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            }
        )
    },

    /*
    *   DELETE /api/v1/user - delete a single user
    */
    deleteUser: ( data ) => {
        return window.axios.delete( MONGO_CONFIG.API_URL + '/user/' + data.id + '/' + data.type + '/' + data.user + '?uid=' + data.id )
    },

    /*
    *   POST  /api/v1/user/login - login a user
    */
    loginUser: ( data ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/user/login',
            {
                user: data.user,
                password: data.password,
                active: data.active,
                remember: data.remember,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            })
    },

    /*
    *   GET /api/v2/user/logout/{uid}  - logs out the user
    */
    logoutUser: ( uid ) => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/user/logout/' + uid)
    },

    /*
    *   GET  /api/v1/user/fetch/{uid} - get the current authenticated user
    */
    fetchUser: ( uid ) => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/user/fetch/' + uid )
    },

    /*
    *   GET /api/v1/user/email/{email} - Check | verify an email address
    */
    checkEmail: ( email ) => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/user/email/' + email )
    },

    /*
    *   GET /api/v1/user/location - Get the users location from IpInfo
    */
    getUserLocation: () => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/user/location' )
    },

    /*
    *   GET /api/v1/user/states/{country} - Get the users states based on country code
    */
    getUserStates: (country) => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/user/states/' + country)
    },
}
