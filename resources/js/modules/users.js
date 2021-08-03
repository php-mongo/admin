/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      users.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   users.js
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
|-------------------------------------------------------------------------------
| VUEX modules/users.js
|-------------------------------------------------------------------------------
| The Vuex data store for the users
*/

/*
*   This store requires the user api
*/
import UserAPI from '../api/user.js';

export const users = {
    /*
     *   Defines the state being monitored for the module.
     */
    state: {
        user: {},
        users: [],
        userLoadStatus: 0,
        usersLoadStatus: 0,
        userSaveStatus: 0,
        userLoginStatus: 0,
        userLogoutStatus: 0,
        userAuthStatus: 0,
        emailCheck: {},
        emailCheckStatus: 0,
        token: JSON.parse( localStorage.getItem('token') ) || ''
    },

    /*
     *Defines the actions used to retrieve the data.
     */
    actions: {
        /*
         *  Create a user
         */
        createUser( { commit, dispatch }, data ) {
            commit( 'setUserSaveStatus', 1 );

            UserAPI.postUser( data )
                .then( (response) => {
                    commit( 'setUserSaveStatus', 2 );
                    dispatch( 'setInUsers', [data, response.data.data.user] );
                })
                .catch( (error) => {
                    commit( 'setUserSaveStatus', 3 );
                    console.log(error);
                });
        },

        /*
         *   Login a user via the API
         */
        loginUser( { commit, dispatch }, data ) {
          commit( 'setUserLoginStatus', 1);

          UserAPI.loginUser( data.data.user, data.data.password )
              .then( ( response ) => {
                  if (response.data.success === true && response.data.uid >= 1) {
                      const token = 'Bearer ' + response.data.token;
                      window.axios.defaults.headers.common['Authorization'] = token;
                      localStorage.setItem('token', JSON.stringify(token));
                      commit( 'setUserLoginStatus', 2);
                      commit( 'setUserAuthStatus', response.data.uid);
                      commit( 'setUserToken', { token } );
                      dispatch( 'loadUser' );

                  } else {
                      commit( 'setUserLoginStatus', 3);
                      console.log("login error - secondary");
                      console.log(response.data.success);
                  }
              })
              .catch( (error) => {
                  commit( 'setUserLoginStatus', 3);
                  localStorage.removeItem('token');
                  console.log("login error - primary");
                  console.log(error);
                }
              )
              .finally(() => {
                  console.log("Login process completed");
              });
        },

        /*
         * Load the current user
         */
        loadUser( { commit } ) {
            commit( 'setUserLoadStatus', 1 );

            UserAPI.getUser()
                .then( (response) => {
                    const user = response.data;
                    if (user.id) {
                        commit( 'setUser', { user } );
                        commit( 'setUserAuthStatus', user.id );
                        commit( 'setUserLoadStatus', 2 );

                    } else {
                        // user response was empty
                        commit( 'setUser', {} );
                        commit( 'setUserLoadStatus', 3 );
                    }
                })
                .catch( (error) => {
                    commit( 'setUser', {} );
                    commit( 'setUserLoadStatus', 3 );
                    console.log(error);
                });
        },

        /*
         * Load the all the users
         */
        loadUsers( { commit } ) {
            commit( 'setUsersLoadStatus', 1 );

            UserAPI.getUsers()
                .then( (response) => {
                    commit( 'setUsers', response.data.data.users );
                    commit( 'setUsersLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setUsers', [] );
                    commit( 'setUsersLoadStatus', 3 );
                    console.log(error);
                });
        },

        /*
         * Adds or Edits a user
         */
        editUser( { commit, dispatch }, data ) {
            commit( 'setUserUpdateStatus', 1 );

            UserAPI.putUpdateUser( data.name, data.email, data.password )
                .then(() => {
                    commit( 'setUserUpdateStatus', 2 );
                    dispatch( 'loadUser' );
                })
                .catch( (error) => {
                    commit( 'setUserUpdateStatus', 3 );
                    console.log(error);
                });
        },

        /*
         *   Logs out a user and clears the status and user pieces of state.
         */
        logoutUser( { commit }, data ) {
            commit( 'setUserLogoutStatus', 1 );

            UserAPI.logoutUser( data.uid )
                .then( (response) => {
                    if (response.data.success === true && response.data.message === 'success') {
                        localStorage.removeItem('token');
                        commit( 'setUserLogoutStatus', 2 );
                        commit( 'setUserLoadStatus', 0 );
                        commit( 'setUserLoginStatus', 0 );
                        commit( 'setUserAuthStatus', 0 );
                        commit( 'setUserToken', '' );
                        commit( 'setUser', {} );
                    }
                })
                .catch( (error) => {
                    commit( 'setUserLogoutStatus', 3 );
                    console.log(error);
                });
        },

        /*
         *   Check an email address for uniqueness and validity
         */
        checkEmail( { commit }, data ) {
            commit( 'setEmailCheckStatus', 1);

            UserAPI.checkEmail( data.email )
                .then( (response) => {
                    commit('setEmailCheck', response.data );
                    commit('setEmailCheckStatus', 2);
                })
                .catch( (error) => {
                    commit('setEmailCheckStatus', 3);
                    commit('setEmailCheck', {});
                    console.log(error);
                });
        }
    },

    /*
     *  Defines the mutations used
     */
    mutations: {
        /*
         * Sets the user load status
         */
        setUserLoadStatus( state, status ) {
            state.userLoadStatus = status;
        },

        /*
         * Sets the users load status
         */
        setUsersLoadStatus( state, status ) {
            state.usersLoadStatus = status;
        },

        /*
         * Sets the user
         */
        setUser( state, user ) {
            state.user = user;
        },

        /*
         * Sets the users array
         */
        setUsers( state, users ) {
            state.users = users;
        },

        /*
         * Sets the user into the users array
         */
        setInUsers( state, arr ) {
            let post = arr[0];
            if (post.type === 'login') {
                state.users.loginUsers.push(arr[1]);
            }
            if (post.type === 'database') {
                state.users.databaseUsers.push(arr[1]);
            }
            state.users.push(arr[1]);
        },

        /*
         * Sets the user token
         */
        setUserToken( state, token ) {
            state.token = token;
        },

        /*
         * Sets the user POST status
         */
        setUserSaveStatus( state, status ) {
            state.userSaveStatus = status;
        },

        /*
         * Set the user logging in process status
         */
        setUserLoginStatus( state, status ) {
            state.userLoginStatus = status;
        },

        /*
         * Set the user log out in process status
         */
        setUserLogoutStatus( state, status ) {
            state.userLogoutStatus = status;
        },

        /*
         *   Set and track the logged in user bu UID
         */
        setUserAuthStatus( state, uid ) {
            state.userAuthStatus = uid;
        },

        /*
         *   Set the email check status
         */
        setEmailCheckStatus( state, status ) {
            state.emailCheckStatus = status;
        },

        /*
         *   Set the email check result
         */
        setEmailCheck( state, result ) {
            state.emailCheck = result;
        }

    },

    /*
     * Defines the getters used by the module.
     */
    getters: {
        /*
         *   Returns the user load status.
         */
        getUserLoadStatus( state ) {
            return state.userLoadStatus;
        },

        /*
         *   Returns the users load status.
         */
        getUsersLoadStatus( state ) {
            return state.usersLoadStatus;
        },

        /*
         *   Returns the user.
         */
        getUser( state ) {
            return state.user;
        },

        /*
         *   Returns the users array.
         */
        getUsers( state ) {
            return state.users;
        },

        /*
         * Gets the user auth status - returns the users ID
         */
        getUserAuthStatus( state ) {
            return state.userAuthStatus;
        },

        /*
         *   Gets the user save status
         */
        getUserSaveStatus( state ) {
            return state.userSaveStatus;
        },

        /*
         * Get the email check status
         */
        getEmailCheckStatus( state ) {
            return state.emailCheckStatus;
        },

        /*
         *   Get the email check result
         */
        getEmailCheck( state ) {
            return state.emailCheck;
        },

        /*
         *   Returns the login process status
         */
        getUserLoginStatus( state ) {
            return state.userLoginStatus;
        },

        /*
         *   Returns the logout process status
         */
        getUserLogoutStatus( state ) {
            return state.userLogoutStatus;
        },

        /*
         *   Checks if the token is set - toke indicate that the user has an API token
         */
        isLoggedIn( state ) {
            return !!state.token;
        },

        /*
         *   Return the logged in users name
         */
        getUsersName( state ) {
            return state.user.name;
        }
    }
};
