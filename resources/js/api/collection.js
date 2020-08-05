/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      collection.js 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      collection.js
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
    *   Get a single collection
    *   GET /api/vi/collection/{name}
    */
    getCollection: function( data ) {
        return axios.get( MONGO_CONFIG.API_URL + '/collection/' + data.database + '/' + data.collection );
    },

    /*
    *   Create a new collection
    *   POST  /api/v1/collection/create
    */
    createCollection: function( data ) {
        return axios.post( MONGO_CONFIG.API_URL + '/collection/create',
            {
                name: data.name,
                capped: data.capped,
                count: parseInt(data.count, 10),
                size: parseInt(data.size, 10),
                database: data.db,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Delete one or more collection(s)
    *   POST  /api/v1/collection/delete
    */
    deleteCollection: function( names) {
        return axios.post( MONGO_CONFIG.API_URL + '/collection/delete',
            {
                names: names,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    }
}
