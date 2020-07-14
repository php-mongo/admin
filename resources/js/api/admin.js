/*
*  Imports the PHP Mongo Admin URL from the config.
*/
import { MONGO_CONFIG } from "../config";

export default {
    /*
    *   Get all ads
    *   GET /api/v1/latest
    */
    getAds: function() {

        console.log("fetching ads: " + MONGO_CONFIG.API_URL + '/latest');

        return axios.get( MONGO_CONFIG.API_URL + '/latest' );
    },

    /*
    *   Get a single ad
    *   GET /api/vi/latest/{slug}
    */
    getAd: function( slug ) {
        return axios.get( MONGO_CONFIG.API_URL + '/latest/' + slug );
    },

    /*
    *   Get a single ad
    *   GET /api/vi/latest/{id}
    */
    getNewAd: function( id ) {
        return axios.get( MONGO_CONFIG.API_URL + '/latest/ad/' + id );
    },

    publicPost: function( formData ) {
        return axios.post( MONGO_CONFIG.API_URL + '/latest/post/public',
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
            }
        )
    }
}
