/*
* ----------------------------------------------------
* VUEX modules/application.js
* ----------------------------------------------------
* The Vuex data store for application - all global requirements go here
*/

/*
*   This store requires the user api
*/
import UserAPI from '../api/user.js';

export const application = {
    /*
    *   Defines the 'state' being monitored for the module
    * JSON.parse(sessionStorage.getItem('location'))
    */
    state: {
        language: JSON.parse(localStorage.getItem('language')) || 'en',
        languages: ['en', 'zh'],
        languageOptions: [
            { value: 'en', name: 'English' },
            { value: 'zh', name: 'Chinese' },
            { value: 'jp', name: 'Japanese' }
        ],
        languageStatus: 0,
        languageArray: {},
        location: {},
        locationStatus: 0,
        currentLocation: {},
        country: JSON.parse(sessionStorage.getItem('country')) || 'AU',
        countryName: JSON.parse(sessionStorage.getItem('country_name')) || 'Australia',
        countries: {},
        states: [],
        suburb: '',
        postcode: '',
        activePanel: null
    },

    actions: {
        /*
        *   This method is run from the Language modal for changing the current language
        */
        setLanguage( { commit, state, dispatch }, data) {
            commit( 'setLanguageStatus', 0);

            console.log("language passed to set: " + data);

            // this block simply ensures we are trying to load an existing language
            if (state.languages.find(language => language === data)) {
                console.log("language GTG!");
                commit( 'setLanguage', data);
                localStorage.setItem( 'language', JSON.stringify(data) );
                commit( 'setLanguageArray' );
                commit( 'setLanguageStatus', 2);

            } else {
                console.log("language not found in array!!");
                commit( 'setLanguageStatus', 3);
            }
        },

        /*
        *   Commit the language
        */
        commitLanguage( { commit, dispatch }, data) {
            commit( 'setLanguageStatus', 0);
            commit( 'setLanguage', data);
            commit( 'setLanguageArray' );
            commit( 'setLanguageStatus', 2);
        },

        /*
        * Set the default language - uses predefined config
        */
        setDefaultLanguage( { commit, state }, data) {
            commit( 'setLanguageStatus', 0);
            commit( 'setLanguageArray' );
            commit( 'setLanguageStatus', 2);
        },

        /*
        * Get the users location from IPINFO
        */
        getLocation( { commit, state, dispatch }) {
            commit( 'setLocationStatus', 1 );

            if (sessionStorage.getItem('location')) {
                let cache = JSON.parse(window.sessionStorage.getItem('location'));
                commit( 'setLocation', cache );
                commit( 'setLocationStatus', 2 );
                dispatch( 'applyCurrentLocation', { location: cache } );

                dispatch( 'getStates', cache.country );

            } else {
                UserAPI.getUserLocation( )
                    .then( (response ) => {
                        if (response.data.ip) {
                            let location = {};
                            location.ip         = response.data.ip;
                            location.city       = response.data.city;
                            location.country    = response.data.country;
                            location.country_name = response.data.country_name;
                            location.region     = response.data.region;
                            location.postal     = response.data.postal;

                            commit( 'setLocation', response.data );
                            commit( 'setLocationStatus', 2 );
                            dispatch('applyCurrentLocation', { location: location } );
                            sessionStorage.setItem( 'location', JSON.stringify(response.data) );

                        //    dispatch( 'getStates', response.data.country );

                        } else {
                            // location response was empty
                            commit( 'setLocation', {} );
                            commit( 'setLocationStatus', 3 );
                        }
                    })
                    .catch( (error) => {
                        console.log('error getting location: ' + error);
                        commit( 'setLocation', {} );
                        commit( 'setLocationStatus', 3 );
                    });
            }
        },

        /*
        * Apply the detected location
        */
        applyCurrentLocation( { commit, state }, data ) {
            if (data.location) {
                let current = {};
                current.city        = data.location.city;
                current.state       = data.location.region;
                current.postcode    = data.location.postal;
                current.country     = data.location.country_name;
                current.code        = data.location.country;
                // make the commitment
                commit( 'setCurrentLocation', current );

            } else {
                console.log("current location cannot be set: " + data.location);
            }
        },

        /*
        * Set country from cookie
        */
        setCountryNameFromCookie( { commit }, data) {
            commit( 'setCountryName', data);
        },

        /*
        * Set the countries array
        */
        setCountries( { commit }, data ) {
            commit( 'setCountries', data);
        },

        /*
        *   Get states for the current country (limited data at the moment)
        */
        getStates( { commit }, data ) {
            UserAPI.getUserStates( data )
                .then( ( response ) => {
                    commit( 'setStates', response.data.states );
                })
                .catch( (error) => {
                    console.log(error);
                })
        },

        /*
        *   Set the active 'main panel' - used for styling
        */
        setActivePanel( { commit }, data ) {
            commit( 'setActivePanel', data );
        }
    },

    mutations: {
        setLanguageStatus( state, status ) {
            state.languageStatus = status;
        },

        setLanguage( state, language) {
            state.language = language;
        },

        setLanguageArray( state ) {
            let language = JSON.parse(localStorage.getItem('language')) || state.language;
            let data = [];
            if (language) {
                console.log("initialising language: " + language);
                if (window.i18n[ language ]) {
                    data = window.i18n[ language ];
                }
                localStorage.setItem( 'language', JSON.stringify(language) );

            } else {
                console.log("setting default language: en");
                data = window.i18n[ 'en' ];
            }
            state.languageArray = data;
        },

        setLocationStatus( state, status ) {
            state.locationStatus = status;
        },

        setLocation( state, location ) {
            state.location = location; // Object.assign({}, location); // location;
        },

        setLocationFromCache( state, location ) {
            state.location = location;
        },

        setCurrentLocation( state, location ) {
            state.currentLocation = location; // Object.assign({}, location); // location;
        },

        setCurrentLocationFromCache( state, location ) {
            state.currentLocation = location;
        },

        setCountryName( state, countryName ) {
            state.countryName = countryName;
        },

        setCountries( state, countries ) {
            state.countries = countries;
        },

        setStates( state, states ) {
            state.states = states;
        },

        setPostcode( state, postcode ) {
            state.postcode = postcode;
        },

        setSuburb( state, suburb ) {
            state.suburb = suburb;
        },

        setActivePanel({commit}, data) {
            state.activePanel = data;
        }
    },

    getters: {
        getLanguageStatus( state ) {
            return state.languageStatus;
        },

        getLanguage( state ) {
            return state.language;
        },

        getLanguageArray( state ) {
            return state.languageArray;
        },

        getLanguageString: (state) => (context, key) => {
            return state.languageArray[context][key];
        },

        getLanguageOptions( state ) {
            return state.languageOptions;
        },

        getLocationStatus( state ) {
            return state.locationStatus;
        },

        getLocation( state) {
            return state.location;
        },

        getCurrentLocation( state ) {
            return state.currentLocation;
        },

        getCountry( state ) {
            return state.country;
        },

        getCountryName( state ) {
            return state.countryName;
        },

        getCountries( state ) {
            return state.countries;
        },

        getStates( state ) {
            return state.states;
        },

        getCurrentStateCode: ( state ) => (name) => {
            let code = state.states.find(ste => ste.name === name);
            if (code) {
                return code.code;
            }
            return name;
        },

        getState( state ) {
            return state.currentLocation.state;
        },

        getActivePanel( state ) {
            return state.activePanel;
        }
    }
};
