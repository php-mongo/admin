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
    createCollection: function( name) {
        console.log("name: " + name);
        return axios.post( MONGO_CONFIG.API_URL + '/collection/create',
            {
                database: name,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Delete one or more collection(s)
    *   POST  /api/v1/collection/delete
    */
    deleteCollection: function( names) {
        console.log("name: " + names);
        return axios.post( MONGO_CONFIG.API_URL + '/collection/delete',
            {
                names: names,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    }
}
