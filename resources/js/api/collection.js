/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      collection.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   collection.js
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
    *   Get a single collection
    *   GET /api/vi/collection/{name}
    */
    getCollection: ( data ) => {
        return axios.get( MONGO_CONFIG.API_URL + '/collection/' + data.database + '/' + data.collection );
    },

    /*
    *   Create a new collection
    *   POST  /api/v1/collection/create
    */
    createCollection: ( data ) => {
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
    deleteCollection: ( names ) => {
        return axios.post( MONGO_CONFIG.API_URL + '/collection/delete',
            {
                names: names,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Create one document
    *   POST /api/v1/document/create
    */
    createDocument: ( data ) => {
        return axios.post( MONGO_CONFIG.API_URL + '/document/create',
            {
                document: data.document,
                collection: data.collection,
                database: data.database,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Update one document
    *   POST /api/v1/document/update
    */
    updateDocument: ( data ) => {
        console.log("updating: " + data);
        return axios.put( MONGO_CONFIG.API_URL + '/document/update/' + data._id,
            {
                _id: data._id,
                index: parseInt(data.index, 10),
                document: data.document,
                collection: data.collection,
                database: data.database,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Delete one document
    *   POST /api/v1/document/update
    */
    deleteDocument: ( data ) => {
        return axios.delete( MONGO_CONFIG.API_URL + '/document/' + data.db + '/' + data.collection + '/' + data._id );
    }
}
