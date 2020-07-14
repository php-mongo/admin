/*
*   Imports the PHP Mongo Admin URL from the config.
*/
import { MONGO_CONFIG } from '../config.js';

/*
* Import the Event bus
*/
import { EventBus } from '../event-bus.js';

export default {
    /*
    *  GET   /api/v1/user - get the current authenticated user
    */
    getUser: function() {
        return axios.get( MONGO_CONFIG.API_URL + '/user' );
    },

    /*
    *  PUT  /api/v1/user - create or update a user
    */
    putUpdateUser: function( name, email, password ) {
        return axios.put( MONGO_CONFIG.API_URL + '/user',
            {
                name: name,
                email: email,
                password: password
            });
    },

    /*
    *  POST  /api/v1/user - register a new user
    */
    postUser: function( name, email, password ) {
        return axios.post( MONGO_CONFIG.API_URL + '/user',
            {
                name: name,
                email: email,
                password: password,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *  POST  /api/v1/user/login - login a user
    */
    loginUser: function( user, password ) {
        return axios.post( MONGO_CONFIG.API_URL + '/user/login',
            {
                user: user,
                password: password,
                _token: window.axios.defaults.headers.common['X-CSRF-TOKEN']
            });
    },

    /*
    *   GET /api/v2/user/logout/{uid}  - logs out the user
    */
    logoutUser: function( uid ) {
        return axios.get( MONGO_CONFIG.API_URL + '/user/logout/' + uid);
    },

    /*
    *   GET  /api/v1/user/fetch/{uid} - get the current authenticated user
    */
    fetchUser: function( uid ) {
        return axios.get( MONGO_CONFIG.API_URL + '/user/fetch/' + uid );
    },

    /*
    *   GET /api/v1/user/email/{email} - Check | verify an email address
    */
    checkEmail: function( email ) {
        return axios.get( MONGO_CONFIG.API_URL + '/user/email/' + email );
    },

    /*
    *   GET /api/v1/user/location - Get the users location from IpInfo
    */
    getUserLocation: function() {
        return axios.get( MONGO_CONFIG.API_URL + '/user/location' );
    },

    /*
    *   GET /api/v1/user/states/{country} - Get the users states based on country code
    */
    getUserStates: function(country) {
        return axios.get( MONGO_CONFIG.API_URL + '/user/states/' + country);
    },
}
