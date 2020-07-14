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

        console.log("fetching dbs: " + MONGO_CONFIG.API_URL + '/dbs');

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
