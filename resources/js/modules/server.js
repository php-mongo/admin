/*
* ----------------------------------------------------
* VUEX modules/server.js
* ----------------------------------------------------
* The Vuex data store for server component views
*/

/*
*   Fetch the API to handle the requests
*/
import ServerApi from '../api/server.js'

/*
*   Imports the Event Bus to pass events on tag updates
*/
import { EventBus } from '../event-bus.js';

export const server = {
    /*
    *   Defines the 'state' being monitored for the module
    */
    state: {
        server: {},
        serverLoadStatus: 0,
        displayServer: {},
        displayServerStatus: 0
    },

    /*
    *   Defines the actions available for the server module
    */
    actions: {
        /*
        *   Loads the server from the API
        */
        loadServer( { commit, rootState, dispatch } ) {
            commit( 'setServerLoadStatus', 1 );

            ServerApi.getServer()
                .then( ( response ) => {
                    commit( 'setServer', response.data.server );
                    commit( 'setServerLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setServer', [] );
                    commit( 'setServerLoadStatus', 3 );
                    console.log(error);
                    EventBus.$emit('no-results-found', { notification: 'No server was returned from the api - please try again later' });
                });
        }
    },

    /*  commit( 'setDisplayAdStatus', 1);
    *   Defines the mutations used for the server module
    */
    mutations: {
        /*
        *   Set the server load status
        */
        setServerLoadStatus( state, status ) {
            state.serverLoadStatus = status;
        },

        /*
        *   Sets the dds
        */
        setServer( state, server ) {
            state.server = server;
        },

        /*
        *   Set the display server
        */
        setDisplayServer( state, server) {
            state.displayDb = server;
        },

        /*
        *   Set the display server status
        */
        setDisplayServerStatus( state, status) {
            state.displayDbStatus = status;
        }
    },

    /*
    *   Define the getters used by the server module
    */
    getters: {
        /*
        *   Return the server load status
        */
        getServerLoadStatus( state ) {
            return state.serverLoadStatus;
        },

        /*
        *   Return the server
        */
        getServer( state ) {
            return state.server;
        },

        getBuildInfo( state ) {
          return state.server.buildinfo;
        },

        getCommandLine( state ) {
            return state.server.commandline;
        },

        getConnection( state ) {
            return state.server.connection;
        },

        getDirectives( state ) {
            return state.server.directives;
        },

        getWebServer( state ) {
            return state.server.webserver;
        },

        getComposerData( state ) {
            return state.server.composer;
        },

        /*
        *   Return the display server status
        */
        getDisplayServerStatus( state ) {
            return state.displayServerStatus;
        },

        /*
        *   Return the display server
        */
        getDisplayServer: (state) => (id) => {
            console.log("getDisplayServer: " + id);
            if (state.server && state.server.id !== '') {
                console.log("server found!!");
                return state.server;

            } else {
                let server = state.server.find(server => server.id === id);
                if (server) {
                    console.log("setting display server state: " + id);
                    // this.store.commit('setDisplayAdStatus', id);
                    // this.store.commit('setDisplayAd', ad);
                    state.displayServerStatus = id;
                    state.displayServer = server;
                    return server;
                }
            }
        }
    }
};
