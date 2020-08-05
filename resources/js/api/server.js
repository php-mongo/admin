/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      server.js 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      server.js
 * @subpackage   Id
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
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
        return axios.get( MONGO_CONFIG.API_URL + '/server' );
    },

    /*
    *   Get the servers setup by current user
    *   GET /api/v1/servers
    */
    getServers: () => {
        return axios.get( MONGO_CONFIG.API_URL + '/servers' );
    },

    /*
    *   Save the server details either created or edited on the Servers view
    *   GET /api/v1/servers
    */
    saveServer: ( data ) => {
        return axios.post( MONGO_CONFIG.API_URL + '/servers',
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
        return axios.post( MONGO_CONFIG.API_URL + '/servers/activate',
            {
                id: data,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Delete the server configuration
    *   GET /api/v1/servers/activate
    */
    deleteServer: (data) => {
        return axios.delete( MONGO_CONFIG.API_URL + '/servers/' + data );
    }
}
