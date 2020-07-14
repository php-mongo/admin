/*
*  Imports the PHP Mongo Admin URL from the config.
*/
import { MONGO_CONFIG } from "../config";

export default {
    /*
    *   Get all databases
    *   GET /api/v1/databases
    */
    getDatabases: function() {
        // console.log("fetching databases: " + MONGO_CONFIG.API_URL + '/databases');
        return axios.get( MONGO_CONFIG.API_URL + '/databases' );
    },

    /*
    *   Get a single database
    *   GET /api/vi/database/{name}
    */
    getDatabase: function( name ) {
        return axios.get( MONGO_CONFIG.API_URL + '/databases/' + name );
    },

    /*
    *   Create a new Database
    *   POST  /api/v1/database/create
    */
    createDatabase: function( name) {
        console.log("name: " + name);
        return axios.post( MONGO_CONFIG.API_URL + '/databases/create',
            {
                database: name,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   Delete one or more Database(s)
    *   POST  /api/v1/database/delete
    */
    deleteDatabase: function( names) {
        console.log("name: " + names);
        return axios.post( MONGO_CONFIG.API_URL + '/databases/delete',
            {
                names: names,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },
}
