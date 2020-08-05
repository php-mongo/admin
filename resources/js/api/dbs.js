/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      dbs.js 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      dbs.js
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
    *   Get all dbs
    *   GET /api/v1/dbs
    */
    getDbs: function() {
        return axios.get( MONGO_CONFIG.API_URL + '/dbs' );
    },

    /*
    *   Get a single db
    *   GET /api/vi/dbs/{id}
    */
    getDb: function( id ) {
        return axios.get( MONGO_CONFIG.API_URL + '/dbs/' + id );
    }
}
