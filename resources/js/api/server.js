/*
*  Imports the PHP Mongo Admin URL from the config.
*/
import { MONGO_CONFIG } from "../config";

export default {
    /*
    *   Get the default load server details
    *   GET /api/v1/server
    */
    getServer: function() {

        console.log("fetching server: " + MONGO_CONFIG.API_URL + '/server');

        return axios.get( MONGO_CONFIG.API_URL + '/server' );
    }
}
