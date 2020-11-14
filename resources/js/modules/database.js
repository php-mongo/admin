/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      database.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   database.js
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
* VUEX modules/database.js
* ----------------------------------------------------
* The Vuex data store for database(s) component views
*/

/*
*   Fetch the API to handle the requests
*/
import DatabaseApi from '../api/database.js'

/*
*   Imports the Event Bus to pass events on tag updates
*/
import { EventBus } from '../event-bus.js';

export const database = {
    /*
    *   Defines the 'state' being monitored for the module
    */
    state: {
        activeDatabase: null,
        commandLoadStatus: 0,
        commandResults: [],
        databases: [],
        databasesLoadStatus: 0,
        database: {},
        databaseLoadStatus: 0,
        displayDatabase: {},
        displayDatabaseStatus: 0,
        dbCollection: {},
        dbCollectionStatus: 0,
        createDatabaseStatus: 0,
        deleteDatabaseStatus: 0,
        transferDatabaseStatus: 0,
        inserted: 0,
        saveProfileStatus: 0,
        profileStatus: 0,
        profile: [],
        level: null,
        repairStatus: 0,
        dbAuthStatus: 0,
        getDbAuth: [],
        dbUserStatus: 0,
        dbDeleteUserStatus: 0,
        errorData: {}
    },

    /*
    *   Defines the actions available for the database module
    */
    actions: {
        /*
        *   Loads the database from the API
        */
        loadDatabases( { commit } ) {
            commit( 'setDatabasesLoadStatus', 1 );

            DatabaseApi.getDatabases()
                .then( ( response ) => {
                    if (response.data.success === true) {
                        commit( 'setDatabases', response.data.data.databases );
                        commit( 'setDatabasesLoadStatus', 2 );
                    } else {
                        console.log(response.data.errors);
                        commit( 'setErrorData', response.data.errors );
                        commit( 'setDatabasesLoadStatus', 3 );
                    }
                })
                .catch( (error) => {
                    console.log(error);
                    commit( 'setDatabases', [] );
                    commit( 'setDatabasesLoadStatus', 3 );
                    commit( 'setErrorData', error.errors );
                    EventBus.$emit('no-results-found', { notification: 'No databases were returned from the api - please try again later' });
                });
        },

        /*
        *   Loads a database from the API
        */
        loadDatabase( { commit, dispatch }, data ) {
            commit( 'setDatabaseLoadStatus', 1 );
            commit( 'setDatabase', {} );

            DatabaseApi.getDatabase( data )
                .then( ( response ) => {
                    commit( 'setActiveDatabase', data );
                    commit( 'setDatabase', response.data.data.database );
                    commit( 'setDatabaseLoadStatus', 2 );

                    let collections = response.data.data.database.collections;
                    dispatch('setDbCollections', collections);
                })
                .catch( (error) => {
                    commit( 'setDatabase', {} );
                    commit( 'setDatabaseLoadStatus', 3 );
                    commit( 'setErrorData', error.errors );
                    console.log(error);
                    EventBus.$emit('no-results-found', { notification: 'No database was returned from the api - please try again later' });
                });
        },

        /*
        *   Because we set the 'admin' database on site - we can clear the stored DB before rendering the single DB view
        */
        clearDatabase( { commit }) {
            commit ( 'clearDatabaseObject' );
        },

        /*
        *   Create a new database - add result to database array
        */
        createDatabase( { commit }, data ) {
            commit( 'setCreateDatabaseStatus', 1);

            DatabaseApi.createDatabase( data )
                .then( ( response ) => {
                    commit( 'setCreatedDatabase', response.data.data.database );
                    commit( 'setCreateDatabaseStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setCreateDatabaseStatus', 3 );
                    commit( 'setErrorData', error);
                    console.log(error);
                });
        },

        /*
        *   Delete one or more databases - remove database from array
        */
        deleteDatabase( { commit }, data ) {
            commit( 'setDeleteDatabaseStatus', 1);

            DatabaseApi.deleteDatabase( data )
                .then( () => {
                    commit( 'setDeletedDatabase', data );
                    commit( 'setDeleteDatabaseStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDeleteDatabaseStatus', 3 );
                    commit( 'setErrorData', error);
                    console.log(error);
                });
        },

        databaseCommand( { commit }, data ) {
            commit( 'setCommandLoadStatus', 1);

            DatabaseApi.databaseCommand( data )
                .then( ( response ) => {
                    if (response.data.success === true) {
                        commit( 'setCommandResults', response.data.data.results );
                        commit( 'setCommandLoadStatus', 2 );

                    } else {
                        commit( 'setErrorData', response.data.errors);
                        commit( 'setCommandLoadStatus', 3 );
                    }
                })
                .catch( (error) => {
                    commit( 'setCommandLoadStatus', 3 );
                    commit( 'setCommandResults', [] );
                    commit( 'setErrorData', error);
                    console.log(error);
                });
        },

        transferDatabase( { commit }, data ) {
            commit('setTransferStatus', 1);

            DatabaseApi.transferDatabase(data)
                .then((response) => {
                    commit( 'setTransferStatus', 2 );
                    commit( 'setInserted', response.data.data.inserted );
                })
                .catch( (error) => {
                    commit( 'setTransferStatus', 3 );
                    commit( 'setErrorData', error );
                    commit( 'setInserted', 0 );
                    console.log(error);
                });
        },

        saveDbProfile( { commit }, data ) {
            commit('setSaveProfileStatus', 1);

            DatabaseApi.saveProfile(data)
                .then(() => {
                    commit( 'setSaveProfileStatus', 2 );
                    // ToDo: the result return does not contain the updated level
                })
                .catch( (error) => {
                    commit( 'setSaveProfileStatus', 3 );
                    commit( 'setErrorData', error );
                    commit( 'setProfile', 0 );
                    console.log(error);
                });
        },

        getDbProfile( { commit }, data) {
            commit('setProfileStatus', 1);

            DatabaseApi.getProfile(data)
                .then((response) => {
                    commit( 'setProfileStatus', 2 );
                    commit( 'setProfile', response.data.data.profile );
                    commit( 'setLevel', response.data.data.level );
                })
                .catch( (error) => {
                    commit( 'setProfileStatus', 3 );
                    commit( 'setErrorData', error );
                    commit( 'setProfile', 0 );
                    console.log(error);
                });
        },

        repairDb( { commit }, data) {
            commit('setRepairStatus', 1);

            DatabaseApi.repairDb(data)
                .then(() => {
                    commit( 'setRepairStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setRepairStatus', 3 );
                    console.log(error);
                });
        },

        getDatabaseAuth( { commit }, data) {
            commit('setDbAuthStatus', 1);

            DatabaseApi.getDbAuth(data)
                .then((response) => {
                    commit( 'setDbAuth', response.data.data.results);
                    commit( 'setDbAuthStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDbAuthStatus', 3 );
                    console.log(error);
                });
        },

        saveDbUser( { commit }, data) {
            commit('setDbUserStatus', 1);

            DatabaseApi.saveDbUser(data)
                .then(() => {
                    commit( 'setDbUserStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDbUserStatus', 3 );
                    console.log(error);
                });
        },

        deleteDbUser( {commit}, data) {
            commit('setDeleteDbUserStatus', 1);

            DatabaseApi.deleteDbUser(data)
                .then(() => {
                    commit( 'setDeleteDbUserStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDeleteDbUserStatus', 3 );
                    console.log(error);
                });
        },

        /*
        *   Get a collection from the stored single database object
        */
        getDbCollection( { commit }, collection ) {
            commit('setDbCollectionStatus', 1);
            commit( 'findDbCollection', collection);
        },

        setCollection( { commit }, data) {
            commit( 'setCollectionToDatabase', data);
        },

        dropCollection( { commit }, data) {
            commit( 'setDropCollectionFromDatabase', data);
        },

        /*
        *   Set the active database - useful for database tracking
        *   ToDo: this also gets set when the DB is fetched - this may be redundant
        */
        setActiveDatabase( { commit }, data ) {
            commit( 'setActiveDatabase', data );
        }
    },

    /*
    *   Defines the mutations used for the database module
    */
    mutations: {
        /*
        *   Set the database load status
        */
        setDatabasesLoadStatus( state, status ) {
            state.databaseLoadStatus = status;
        },

        /*
        *   Sets the databases
        *   ToDo: !! to prevent loading errors we need to add a 'default' Database to the database object
        *   We can use the admin db as default
        */
        setDatabases( state, databases ) {
            state.databases = databases;
            state.database  = databases.find(db => db.db.name === 'admin');
        },

        /*
        *   Set the database load status
        */
        setDatabaseLoadStatus( state, status ) {
            state.databaseLoadStatus = status;
        },

        /*
        *   Sets the database
        */
        setDatabase( state, database ) {
            state.database = database;
        },

        /*
        * Clears the DB so that its data wont show on initial component rendering
        */
        clearDatabaseObject( state ) {
            state.database = {};
        },

        /*
        *   Set the display database
        */
        setDisplayDatabase( state, database) {
            state.displayDatabase = database;
        },

        /*
        *   Set the display database status
        */
        setDisplayDatabaseStatus( state, status) {
            state.displayDatabaseStatus = status;
        },

        /*
        *   Set the create database status
        */
        setCreateDatabaseStatus( state, status) {
            state.createDatabaseStatus = status;
        },

        /*
        *   Add the new database into the existing array
        */
        setCreatedDatabase( state, database ) {
            state.databases.push( database );
        },

        /*
        *   Set the delete database status
        */
        setDeleteDatabaseStatus( state, status) {
            state.deleteDatabaseStatus = status;
        },

        /*
        *   Set (remove) the deleted database(s) from the existing array
        */
        setDeletedDatabase( state, databases ) {
            let arr;
            databases.forEach( (value) => {
                /*state.databases = state.databases.map( db => {
                    return db.db.name !== value;
                });*/
                arr = [];
                state.databases.forEach( (db) => {
                    if (db.db.name !== value) {
                        arr.push(db);
                    }
                });
                state.databases = arr;
            });
        },

        /*
        *   Save the error data for reference
        */
        setErrorData( state, error ) {
            state.errorData = error;
        },

        /*
         * With luck - save the new collection into the current exhibited database
         */
        setCollectionToDatabase( state, collection ) {
            if (state.database) {
                if (state.database.collections) {
                    state.database.collections.push(collection);
                }
            }
            if (state.databases) {
                state.databases.forEach( (database, index ) => {
                    if (database.db.name === collection.collection.databaseName) {
                        state.databases[index].collections.push(collection);
                        return true;
                    }
                });
            }
        },

        /*
        *   Set the active database
        */
        setActiveDatabase(state, database) {
            state.activeDatabase = database;
        },

        /*
         *  The method removes a collection for both database instances (database & databases)
         */
        setDropCollectionFromDatabase(state, data) {
            let db   = data.database;
            let coll = data.collection;
            let arr = [];
            // handle the single database object
            let collections = state.database.collections;
            collections.forEach( (collection) => {
                if (collection.collection.name !== coll && collection.collection.collectionName !== coll) {
                    arr.push(collection);
                }
            });
            state.database.collections = arr;

            // handle the databases array
            state.databases.forEach( ( database, index) => {
                if (database.db.name === db) {
                    collections = database.collections;
                    arr = [];
                    collections.forEach( (collection) => {
                        if (collection.collection.name !== coll && collection.collection.collectionName !== coll) {
                            arr.push(collection);
                        }
                    });
                    database.collections = arr;
                    state.databases[index] = database;
                    return true;
                }
            });
        },

        setDbCollectionStatus( state, status ) {
            state.dbCollectionStatus = status;
        },

        findDbCollection(state, c ) {
            let collection = state.database.collections.find(coll => coll.collection.name === c);
            if (collection) {
                state.dbCollection = collection;
                state.dbCollectionStatus = 2;
            } else {
                state.dbCollectionStatus = 3;
            }
        },

        setCommandLoadStatus( state, status ) {
            state.commandLoadStatus = status;
        },

        setCommandResults( state, results ) {
            state.commandResults = results;
        },

        setTransferStatus( state, status ) {
            state.transferDatabaseStatus = status;
        },

        setInserted( state, inserted) {
            state.inserted = inserted;
        },

        setSaveProfileStatus( state, status) {
            state.saveProfileStatus = status;
        },

        setProfileStatus( state, status) {
            state.profileStatus = status;
        },

        setProfile( state, profile) {
            state.profile = profile;
        },

        setLevel( state, level) {
            state.level = level;
        },

        setRepairStatus( state, status ) {
            state.repairStatus = status;
        },

        setDbAuthStatus( state, status ) {
            state.dbAuthStatus = status;
        },

        setDbAuth( state, auth ) {
            state.getDbAuth = auth;
        },

        setDbUserStatus( state, status ) {
            state.dbUserStatus = status;
        },

        setDeleteDbUserStatus( state, status ) {
            state.dbDeleteUserStatus = status;
        }
    },

    /*
    *   Define the getters used by the database module
    */
    getters: {
        /*
        *   Return the databases load status
        */
        getDatabasesLoadStatus( state ) {
            return state.databasesLoadStatus;
        },

        /*
        *   Return the databases
        */
        getDatabases( state ) {
            return state.databases;
        },

        /*
        *   Return the database load status
        */
        getDatabaseLoadStatus( state ) {
            return state.databaseLoadStatus;
        },

        /*
        *   Return the database
        */
        getDatabase( state ) {
            return state.database;
        },

        /*
        *   Return the display database status
        */
        getDisplayDatabaseStatus( state ) {
            return state.displayDatabaseStatus;
        },

        /*
        *   Return the display database
        */
        getDisplayDatabase: (state) => (id) => {
            if (state.database && state.database.id !== '') {
                return state.database;

            } else {
                let database = state.database.find(database => database.id === id);
                if (database) {
                    state.displayDatabaseStatus = id;
                    state.displayDatabase = database;
                    return database;
                }
            }
        },

        /*
        *   Get the create database status
        */
        getCreateDatabaseStatus( state ) {
            return state.createDatabaseStatus;
        },

        /*
        *   Get the delete database status
        */
        getDeleteDatabaseStatus( state ) {
            return state.deleteDatabaseStatus;
        },

        /*
        *   Get the stats array (object) from the database object
        */
        getStats( state ) {
            if (state.database) {
                return state.database.stats;
            }
        },

        /*
        *   Fetch any active error
        */
        getErrorData( state ) {
            return state.errorData;
        },

        /*
        *   Get the active database
        */
        getActiveDatabase( state) {
            return state.activeDatabase;
        },

        getDbCollectionStatus( state ) {
            return state.dbCollectionStatus;
        },

        getDbCollection( state ) {
            return state.dbCollection;
        },

        getCommandLoadStatus( state ) {
            return state.commandLoadStatus;
        },

        getCommandResults( state ) {
            return state.commandResults;
        },

        getTransferStatus(state) {
            return state.transferDatabaseStatus;
        },

        getInserted( state ) {
            return state.inserted;
        },

        getSaveProfileStatus( state ) {
           return state.saveProfileStatus;
        },

        getProfileStatus( state ) {
            return state.profileStatus;
        },

        getProfile( state ) {
            return state.profile;
        },

        getLevel( state ) {
            return state.level;
        },

        getRepairStatus( state ) {
            return state.repairStatus;
        },

        getDbAuthStatus( state ) {
            return state.dbAuthStatus;
        },

        getDbAuth( state ) {
            return state.getDbAuth;
        },

        getDbUserStatus( state ) {
            return state.dbUserStatus;
        }
    }
};
