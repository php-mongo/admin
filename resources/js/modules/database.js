/*
* ----------------------------------------------------
* VUEX modules/server.js
* ----------------------------------------------------
* The Vuex data store for server component views
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
        databases: [],
        databasesLoadStatus: 0,
        database: {},
        activeDatabase: null,
        databaseLoadStatus: 0,
        displayDatabase: {},
        displayDatabaseStatus: 0,
        createDatabaseStatus: 0,
        deleteDatabaseStatus: 0,
        errorData: {}
    },

    /*
    *   Defines the actions available for the database module
    */
    actions: {
        /*
        *   Loads the database from the API
        */
        loadDatabases( { commit, rootState, dispatch } ) {
            commit( 'setDatabasesLoadStatus', 1 );

            DatabaseApi.getDatabases()
                .then( ( response ) => {
                    console.log(response.data.data.databases);
                    commit( 'setDatabases', response.data.data.databases );
                    commit( 'setDatabasesLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDatabases', [] );
                    commit( 'setDatabasesLoadStatus', 3 );
                    console.log(error);
                    EventBus.$emit('no-results-found', { notification: 'No databases were returned from the api - please try again later' });
                });
        },

        /*
        *   Loads a database from the API
        */
        loadDatabase( { commit, rootState, dispatch }, data ) {
            commit( 'setDatabaseLoadStatus', 1 );

            DatabaseApi.getDatabase( data )
                .then( ( response ) => {
                    console.log(response.data.data.database);
                    commit( 'setActiveDatabase', data );
                    commit( 'setDatabase', response.data.data.database );
                    commit( 'setDatabaseLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDatabase', {} );
                    commit( 'setDatabaseLoadStatus', 3 );
                    console.log(error);
                    EventBus.$emit('no-results-found', { notification: 'No database was returned from the api - please try again later' });
                });
        },

        /*
        *   Create a new database - add result to database array
        */
        createDatabase( { commit, rootState, dispatch }, data ) {
            commit( 'setCreateDatabaseStatus', 1);

            DatabaseApi.createDatabase( data )
                .then( ( response ) => {
                    console.log(response.data.data);
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
        deleteDatabase( { commit, rootState, dispatch }, data ) {
            commit( 'setDeleteDatabaseStatus', 1);

            DatabaseApi.deleteDatabase( data )
                .then( ( response ) => {
                    console.log(response.data.data);
                    commit( 'setDeletedDatabase', data );
                    commit( 'setDeleteDatabaseStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDeleteDatabaseStatus', 3 );
                    commit( 'setErrorData', error);
                    console.log(error);
                });
        },

        /*
        *   Set the active database - useful for database loading child component actions
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
            databases.forEach(function(value, index) {
                let arr = [];
                state.databases.forEach(function(db, index) {
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
        *   Set the active database
        */
        setActiveDatabase(state, database) {
            state.activeDatabase = database;
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
            console.log("getDisplayDatabase: " + id);
            if (state.database && state.database.id !== '') {
                console.log("database found!!");
                return state.database;

            } else {
                let database = state.database.find(database => database.id === id);
                if (database) {
                    console.log("setting display database state: " + id);
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
        }
    }
};
