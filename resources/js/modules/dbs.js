/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      dbs.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   dbs.js
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
import DbsApi from '../api/dbs.js'

/*
*   Imports the Event Bus to pass events on tag updates
*/
import { EventBus } from '../event-bus.js';

export const dbs = {
    /*
    *   Defines the 'state' being monitored for the module
    */
    state: {
        dbs: [],
        dbsCount: 0,
        dbsLoadStatus: 0,
        db: {},
        dbLoadStatus: 0,
        newDb: {},
        newDbLoadStatus: 0,
        displayDb: {},
        displayDbStatus: 0
    },

    /*
    *   Defines the actions available for the dbs module
    */
    actions: {
        /*
        *   Loads the dbs from the API
        */
        loadDbs( { commit, rootState, dispatch } ) {
            commit( 'setDbsLoadStatus', 1 );

            DbsApi.getDbs()
                .then( ( response ) => {
                    commit( 'setDbs', response.data.dbs );
                    commit( 'setDbsCount', response.data.dbs );
                    commit( 'setDbsLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDbs', [] );
                    commit( 'setDbsLoadStatus', 3 );
                    console.log(error);
                    EventBus.$emit('no-results-found', { notification: 'No dbs were returned from the database - please try again later' });
                });
        },

        /*
        *   Get a single db from the API
        */
        getDb( { commit }, data ) {
            commit( 'setDbStatus', 1);

            DbsApi.getNewAd( data.id )
                .then( ( response ) => {
                    commit( 'setDb', response.data);
                    commit( 'setDbLoadStatus', 2);
                })
                .catch( (error) => {
                    commit( 'setDb', {} );
                    commit( 'setDbLoadStatus', 3);
                    console.log(error);
                });
        },

        /*
        *   Fetch a new db from the API
        */
        fetchNewDb( { commit }, data ) {
            commit( 'setNewDbLoadStatus', 1 );

            DbsApi.getNewDb( data.id )
                .then( ( response ) => {
                    commit( 'setNewDb', response.data.ad );
                    commit( 'setNewDbLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setNewDb', {} );
                    commit( 'setNewDbLoadStatus', 3 );
                    console.log(error);
                });
        },

        setDisplayedDb( { commit }, data ) {
            commit ( 'setDisplayDbStatus', data.id );
        },

        clearDisplayedDb( { commit }, data) {
            commit( 'setDisplayDb', {} );
            commit ( 'setDisplayDbStatus', 0 );
        }
    },

    /*
    *   Defines the mutations used for the dbs module
    */
    mutations: {
        /*
        *   Set the dbs load status
        */
        setDbsLoadStatus( state, status ) {
            state.dbsLoadStatus = status;
        },

        /*
        *   Sets the dds
        */
        setDbs( state, dbs ) {
            state.dbs = dbs;
        },

        /*
        *   Sets the dbs count
        */
        setDbsCount( state, dbs ) {
            state.dbsCount = dbs.length;
        },

        /*
        *   Set the db load status
        */
        setDbLoadStatus( state, status ) {
            state.dbLoadStatus = status;
        },

        /*
        *   Set the db data
        */
        setDb( state, db ) {
            state.db = db;
        },

        /*
        *   Set the NEW db load status
        */
        setNewDbLoadStatus( state, status) {
            state.newDbLoadStatus = status;
        },

        /*
        *   Sets the NEW db
        */
        setNewDb( state, db ) {
            state.db = db;//latest.push(ad);
        },

        /*
        *   Set the display db
        */
        setDisplayDb( state, db) {
            state.displayDb = db;
        },

        /*
        *   Set the display db status
        */
        setDisplayDbStatus( state, status) {
            state.displayDbStatus = status;
        }
    },

    /*
    *   Define the getters used by the dbs module
    */
    getters: {
        /*
        *   Return the dbs load status
        */
        getDbsLoadStatus( state ) {
            return state.dbsLoadStatus;
        },

        /*
        *   Return the dbs
        */
        getDbs( state ) {
            return state.dbs;
        },

        /*
        *   Return the dbs count
        */
        getDbsCount( state ) {
            return state.dbsCount;
        },

        /*
        *   Return a new db
        */
        getNewDb( state ) {
            return state.newDb;
        },

        /*
        *   Return the newDb load status
        */
        getNewDbLoadStatus( state ) {
            return state.newDbLoadStatus;
        },

        /*
        *   Return the display db
        */
        getDisplayDb: (state) => (id) => {
            if (state.db && state.db.id !== '') {
                return state.db;

            } else {
                let db = state.dbs.find(db => db.id === id);
                if (db) {
                    state.displayDbStatus = id;
                    state.displayDb = db;
                    return db;
                }
            }

        },

        /*
        *   Return the display db status
        */
        getDisplayDbStatus( state ) {
            return state.displayDbStatus;
        }

    }
};
