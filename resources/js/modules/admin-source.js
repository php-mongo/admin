/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      admin-source.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   admin-source.js
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
* ----------------------------------------------------
* VUEX modules/latest.js
* ----------------------------------------------------
* The Vuex data store for public ad views
*/

/*
*   Fetch the API to handle the requests
*/
import LatestApi from '../api/admin.js'

/*
*   Imports the Event Bus to pass events on tag updates
*/
import { EventBus } from '../event-bus.js';

export const latestAds = {
    /*
    *   Defines the 'state' being monitored for the module
    */
    state: {
        latest: [],
        latestCount: 0,
        premium: [],
        premiumCount: 0,
        isaBot: null,
        adsLoadStatus: 0,
        ad: {},
        adLoadStatus: 0,
        newAd: {},
        newAdLoadStatus: 0,
        displayAd: {},
        displayAdStatus: 0,
        publicPostStatus: 0,
        publicPostId: '',
        publicPostError: ''
    },

    /*
    *   Defines the 'Public' action available for the application
    */
    actions: {
        /*
        *   Loads the ads from the API
        */
        loadAds( { commit, rootState, dispatch } ) {
            commit( 'setAdsLoadStatus', 1 );

            LatestApi.getAds()
                .then( function( response ) {
                    commit( 'setLatest', response.data.latest );
                    commit( 'setLatestCount', response.data.latest );
                    commit( 'setPremium', response.data.premium );
                    commit( 'setPremiumCount', response.data.premium );
                    commit( 'setBotStatus', response.data.isaBot );
                    commit( 'setAdsLoadStatus', 2 );
                })
                .catch( function() {
                    commit( 'setLatest', [] );
                    commit( 'setPremium', [] );
                    commit( 'setBotStatus', false );
                    commit( 'setAdsLoadStatus', 3 );
                    EventBus.$emit('no-results-found', { notification: 'No ads were returned from the database - please try again later' });
                });
        },

        /*
        *   Get a single as from the API
        */
        getAd( { commit }, data ) {
            commit( 'setAdStatus', 1);

            LatestApi.getNewAd( data.id )
                .then( function( response ) {
                    commit( 'setAd', response.data);
                    commit( 'setAdLoadStatus', 2);
                })
                .catch( function() {
                    commit( 'setAd', {} );
                    commit( 'setAdLoadStatus', 3);
                });
        },

        /*
        *   Fetch a new ad from the API
        */
        fetchNewAd( { commit }, data ) {
            commit( 'setNewAdLoadStatus', 1 );

            LatestApi.getNewAd( data.id )
                .then( function( response ) {
                    commit( 'setNewAd', response.data.ad );
                    commit( 'setNewAdLoadStatus', 2 );
                })
                .catch( function() {
                    commit( 'setNewAd', {} );
                    commit( 'setNewAdLoadStatus', 3 );
                });
        },

        setDisplayedAd( { commit }, data ) {
            // commit( 'setDisplayAd', data.} ) ;
            commit ( 'setDisplayAdStatus', data.id );
        },

        clearDisplayedAd( { commit }, data) {
            commit( 'setDisplayAd', {} );
            commit ( 'setDisplayAdStatus', 0 );
        },

        postPublicAd( { commit, dispatch }, data) {
            commit( 'setPublicPostStatus', 1);

            LatestApi.publicPost( data.formData )
                .then( (response) => {
                    if (response.data.record) {
                        console.log(response.data.record);
                        console.log(response.data.record[1].id);
                        commit( 'setPublicPostId', response.data.record[1].id );
                        dispatch( 'fetchNewAd', { id: response.data.record[1].id } );
                        commit( 'setPublicPostStatus', 2);

                    } else {
                        console.log(response.data.error)
                        console.log(response.data.error[1].message);
                        commit( 'setPublicPostError', response.data.error[1].message);
                        commit( 'setPublicPostStatus', 3);
                    }

                })
                .catch( function(e) {
                    console.log(e);
                });
        }
    },

    /*  commit( 'setDisplayAdStatus', 1);
    *   Defines the mutations used for the public module
    */
    mutations: {
        /*
        *   Set the ads load status
        */
        setAdsLoadStatus( state, status ) {
            state.adsLoadStatus = status;
        },

        /*
        *   Sets the latest ads
        */
        setLatest( state, latest ) {
            state.latest = latest;
        },

        /*
        *   Sets the latest count
        */
        setLatestCount( state, latest ) {
            state.latestCount = latest.length;
        },

        /*
        *   Sets the premium ads
        */
        setPremium( state, premium ) {
            state.premium = premium;
        },

        /*
        *   Sets the premium count
        */
        setPremiumCount( state, premium ) {
            state.premiumCount = premium.length;
        },

        /*
        *   Sets the bot status - google and friends
        */
        setBotStatus( state, status) {
            state.isaBot = status;
        },

        /*
        *   Set the ads load status
        */
        setAdLoadStatus( state, status ) {
            state.adLoadStatus = status;
        },

        /*
        *   Set the ads load status
        */
        setAd( state, ad ) {
            state.ad = ad;
        },

        /*
        *   Set the ad load status
        */
        setNewAdLoadStatus( state, status) {
            state.newAdLoadStatus = status;
        },

        /*
        *   Sets the ad
        */
        setNewAd( state, ad ) {
            state.ad =ad;//latest.push(ad);
        },

        /*
        *   Set the display ad
        */
        setDisplayAd( state, ad) {
            state.displayAd = ad;
        },

        /*
        *   Set the display ad status
        */
        setDisplayAdStatus( state, status) {
            state.displayAdStatus = status;
        },

        /*
        *   Set the public post status
        */
        setPublicPostStatus( state, status) {
            state.publicPostStatus = status;
        },

        /*
        *   Set the public post ad id
        */
        setPublicPostId( state, id) {
            state.publicPostId = id;
        },

        /*
        *   Set the public post error
        */
        setPublicPostError( state, error) {
            state.publicPostError = error;
        },
    },

    /*
    *   Define the getters used by the public module
    */
    getters: {
        /*
        *   Return the ads load status
        */
        getAdsLoadStatus( state ) {
            return state.adsLoadStatus;
        },

        /*
        *   Return the latest ads
        */
        getLatest( state ) {
            return state.latest;
        },

        /*
        *   Return the latest count
        */
        getLatestCount( state ) {
            return state.latestCount;
        },

        /*
        *   Return the premium ads
        */
        getPremium( state ) {
            return state.premium;
        },

        /*
        *   Return the premium count
        */
        getPremiumCount( state ) {
            return state.premiumCount;
        },

        /*
       *   Return the bot status
       */
        getBotStatus( state ) {
            return state.isaBot;
        },

        /*
        *   Return a new ad
        */
        getNewAd( state ) {
            return state.newAd;
        },

        /*
        *   Return the newAd load status
        */
        getNewAdLoadStatus( state ) {
            return state.newAdLoadStatus;
        },

        /*
        *   Return the public post status
        */
        getPublicPostStatus( state ) {
            return state.publicPostStatus;
        },

        getPublicPostId( state ) {
            return state.publicPostId;
        },

        getPublicPostError( state ) {
            return state.publicPostError;
        },

        /*
        *   Return the ad
        */
        getDisplayAd: (state) => (id) => {
            console.log("getDisplayAd: " + id);
            if (state.ad && state.ad.id !== '') {
                console.log("you are here!!");
                return state.ad;

            } else {
                let ad = state.latest.find(ad => ad.id === id);
                if (ad) {
                    console.log("setting display ad state: " + id);
                    // this.store.commit('setDisplayAdStatus', id);
                    // this.store.commit('setDisplayAd', ad);
                    state.displayAdStatus = id;
                    state.displayAd = ad;
                    return ad;
                }
            }

        },

        /*
        *   Return the ad
        */
        getDisplayAdStatus( state ) {
            return state.displayAdStatus;
        },

    }
};
