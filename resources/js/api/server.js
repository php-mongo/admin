/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      server.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   server.js
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
    *   Get the server details displayed on the dashboard
    *   !! This is handled by a specific ServerController !!
    *   GET /api/v1/server
    */
    getServer: () => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/server' );
    },

    /*
    *   Get the servers setup by current user
    *   GET /api/v1/servers
    */
    getServers: () => {
        return window.axios.get( MONGO_CONFIG.API_URL + '/servers' );
    },

    /*
    *   Save the server details either created or edited on the Servers view
    *   GET /api/v1/servers
    */
    saveServer: ( data ) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/servers',
            {
                id: data.id,
                host: data.host,
                port: data.port,
                username: data.username,
                password: data.password,
                active: data.active,
                user_id: data.user_id,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Activate the server configuration
    *   GET /api/v1/servers/activate
    */
    activateServer: (data) => {
        return window.axios.post( MONGO_CONFIG.API_URL + '/servers/activate',
            {
                id: data,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Delete the server configurations
    *   GET /api/v1/servers/{id}
    */
    deleteServer: (data) => {
        return window.axios.delete( MONGO_CONFIG.API_URL + '/servers/' + data );
    }
}
