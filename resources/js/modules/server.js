/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      server.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   server.js
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
* VUEX modules/server.js
* ----------------------------------------------------
* The Vuex data store for server component views
*/

/*
*   Fetch the API to handle the requests
*/
import ServerApi from '../api/server.js'

export const server = {
    /*
    *   Defines the 'state' being monitored for the module
    */
    state: {
        servers: [],
        serversLoadStatus: 0,
        server: {},
        serverLoadStatus: 0,
        serverConfig: {},
        serverSaveStatus: 0,
        serverActivateStatus: 0,
        serverDeleteStatus: 0,
        displayServer: {},
        displayServerStatus: 0,
        loadServerStatus: 0,
        serverStatus: {},
        loadServerProcesses: 0,
        serverProcesses: []
    },

    /*
    *   Defines the actions available for the server module
    */
    actions: {
        /*
        *   Loads the servers from the API
        */
        loadServers( { commit } ) {
            commit( 'setServersLoadStatus', 1 );

            ServerApi.getServers()
                .then( ( response ) => {
                    commit( 'setServers', response.data.data.servers );
                    commit( 'setServersLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setServers', [] );
                    commit( 'setServersLoadStatus', 3 );
                    console.log(error);
                });
        },

        /*
        *   Loads the server from the API
        */
        loadServer( { commit } ) {
            commit( 'setServerLoadStatus', 1 );

            ServerApi.getServer()
                .then( ( response ) => {
                    commit( 'setServer', response.data.data.server );
                    commit( 'setServerLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setServer', {} );
                    commit( 'setServerLoadStatus', 3 );
                    console.log(error);
                });
        },

        /*
         * Save a server configuration
         */
        saveServer( { commit }, data) {
            commit( 'setServerSaveStatus', 1 );

            ServerApi.saveServer(data)
                .then( ( response ) => {
                    commit( 'setServerConfig', response.data.data.server );
                    commit( 'setServerSaveStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setServerConfig', {} );
                    commit( 'setServerSaveStatus', 3 );
                    console.log(error);
                });
        },

        /*
         * Activate a server configuration
         */
        activateServer( { commit }, data) {
            commit( 'setServerActivateStatus', 1 );

            ServerApi.activateServer(data)
                .then( (response ) => {
                    commit( 'setServerConfig', response.data.data.server );
                    commit( 'setServerActivateStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setServerConfig', {} );
                    commit( 'setServerActivateStatus', 3 );
                    console.log(error);
                });
        },

        /*
         * Delete a server configuration
         */
        deleteServer( { commit }, data)  {
            commit( 'setServerDeleteStatus', 1 );

            ServerApi.deleteServer(data)
                .then( () => {
                    commit( 'setServerDelete', data );
                    commit( 'setServerDeleteStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setServerDelete', {} );
                    commit( 'setServerDeleteStatus', 3 );
                    console.log(error);
                });
        },

        /*
        *   Loads the server status from the API
        */
        getServerStatus( { commit }, database ) {
            commit( 'setLoadServerStatus', 1 );

            ServerApi.getServerStatus(database)
                .then( ( response ) => {
                    commit( 'setServerStatus', response.data.data.status );
                    commit( 'setLoadServerStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setServerStatus', {} );
                    commit( 'setLoadServerStatus', 3 );
                    console.log(error);
                });
        },

        /*
        *   Loads the server processes from the API
        */
        getServerProcesses( { commit } ) {
            commit( 'setLoadServerProcesses', 1 );

            ServerApi.getServerProcesses()
                .then( ( response ) => {
                    if (response.data.data.processes) {
                        if (response.data.data.processes.inprog) {
                            console.log("inprog:" + response.data.data.processes.inprog);
                            commit( 'setServerProcesses', response.data.data.processes.inprog );
                        }
                    }
                    commit( 'setLoadServerProcesses', 2 );
                })
                .catch( (error) => {
                    commit( 'setServerProcesses', [] );
                    commit( 'setLoadServerProcesses', 3 );
                    console.log(error);
                });
        },
    },

    /*
    *   Defines the mutations used for the server module
    */
    mutations: {
        /*
        *   Set the servers load status
        */
        setServersLoadStatus( state, status ) {
            state.serversLoadStatus = status;
        },

        /*
        *   Sets the servers
        */
        setServers( state, servers ) {
            state.servers = servers;
        },

        /*
        *   Set the server load status
        */
        setServerLoadStatus( state, status ) {
            state.serverLoadStatus = status;
        },

        /*
        *   Sets the server
        */
        setServer( state, server ) {
            state.server = server;
        },

        /*
        *   Set the server save status
        */
        setServerSaveStatus( state, status ) {
            state.serverSaveStatus = status;
        },

        /*
        *   Set the server activate status
        */
        setServerActivateStatus( state, status ) {
            state.serverActivateStatus = status;
        },

        /*
        *   Sets the server config
        */
        setServerConfig( state, server ) {
            state.serverConfig = server;
            state.servers.push( server );
        },

        /*
        *   Set the server delete status
        */
        setServerDeleteStatus( state, status ) {
            state.serverDeleteStatus = status;
        },

        /*
        *   Removes the deleted server configuration locally
        */
        setServerDelete( state, id ) {
            state.servers = state.servers.map( server => {
                return server.id !== id;
            });
        },

        /*
        *   Set the display server
        */
        setDisplayServer( state, server) {
            state.displayServer = server;
        },

        /*
        *   Set the display server status
        */
        setDisplayServerStatus( state, status) {
            state.displayServerStatus = status;
        },

        /*
        *   Set the load server status
        */
        setLoadServerStatus( state, status) {
            state.loadServerStatus = status;
        },

        /*
        *   Set the server status
        */
        setServerStatus( state, status) {
            state.serverStatus = status;
        },

        /*
        *   Set the load server processes status
        */
        setLoadServerProcesses( state, status) {
            state.loadServerProcesses = status;
        },

        /*
        *   Set the server processes
        */
        setServerProcesses( state, processes ) {
            state.serverProcesses = processes;
        }
    },

    /*
    *   Define the getters used by the server module
    */
    getters: {
        /*
        *   Return the servers load status
        */
        getServersLoadStatus( state ) {
            return state.serversLoadStatus;
        },

        /*
        *   Return the servers
        */
        getServers( state ) {
            return state.servers;
        },

        /*
        *   Return the servers count
        */
        getServersCount( state ) {
            if (state.servers) {
                return state.servers.length;
            }
            return 0;
        },

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

        /*
        *   Return the server save status
        */
        getServerSaveStatus( state ) {
            return state.serverSaveStatus;
        },

        /*
        *   Return the server activate status
        */
        getServerActivateStatus( state ) {
            return state.serverActivateStatus;
        },

        /*
        *   Return the server
        */
        getServerConfig( state ) {
            return state.serverConfig;
        },

        /*
        *   Return a server configuration for editing
        */
        getServerConfiguration: ( state ) => (id) => {
            return state.servers.find(server => server.id === id);
        },

        getServerDeleteStatus( state ) {
            return state.serverDeleteStatus;
        },

        /*
         *  Return the build info for the loaded server
         */
        getBuildInfo( state ) {
          return state.server.buildinfo;
        },

        /*
         *  Return the command line info for the loaded server
         */
        getCommandLine( state ) {
            return state.server.commandline;
        },

        /*
         *  Return the server connection details for the loaded server
         */
        getConnection( state ) {
            return state.server.connection;
        },

        /*
         *  Return the directives for the loaded server
         */
        getDirectives( state ) {
            return state.server.directives;
        },

        /*
         *  Return the local server information
         */
        getWebServer( state ) {
            return state.server.webserver;
        },

        /*
         *  Return the application composer details
         */
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
            if (state.server && state.server.id !== '') {
                return state.server;

            } else {
                let server = state.server.find(server => server.id === id);
                if (server) {
                    // this.store.commit('setDisplayAdStatus', id);
                    // this.store.commit('setDisplayAd', ad);
                    state.displayServerStatus = id;
                    state.displayServer = server;
                    return server;
                }
            }
        },

        /*
        *   Get the load server's status status
        */
        getLoadServerStatus( state ) {
            return state.loadServerStatus;
        },

        /*
        *   Get the server's status
        */
        getServerStatus( state ) {
            return state.serverStatus;
        },

        /*
        *   Set the load server processes status
        */
        getLoadServerProcesses( state ) {
            return state.loadServerProcesses;
        },

        /*
        *   Get the server processes
        */
        getServerProcesses( state ) {
            return state.serverProcesses;
        }
    }
};
