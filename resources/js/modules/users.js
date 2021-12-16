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
        emailCheck: {},
        emailCheckStatus: 0,
        errorData: null,
        user: {},
        users: [],
        userDeleteStatus: 0,
        userLoadStatus: 0,
        usersLoadStatus: 0,
        userSaveStatus: 0,
        userLoginStatus: 0,
        userLogoutStatus: 0,
        userAuthStatus: 0,
        updated: null,
        token: JSON.parse( localStorage.getItem('token') ) || ''
    },

    /*
     *Defines the actions used to retrieve the data.
     */
    actions: {
        /*
         *  Create a user
         */
        createUser( { commit }, data ) {
            commit( 'setUserSaveStatus', 1 );

            UserAPI.postUser( data )
                .then( (response) => {
                    commit( 'setUserSaveStatus', 2 );
                    commit( 'setInUsers', [data, response.data.data.users] )
                })
                .catch( (error) => {
                    commit( 'setUserSaveStatus', 3 );
                    commit( 'setUserErrorData', error.response);
                    console.log(error.toJSON())
                });
        },

        /*
         *   Login a user via the API
         */
        loginUser( { commit, dispatch }, data ) {
          commit( 'setUserLoginStatus', 1);

          UserAPI.loginUser( data )
              .then( ( response ) => {
                  if (response.data.success === true && response.data.uid >= 1) {
                      const token = 'Bearer ' + response.data.token;
                      // save token for reuse
                      window.axios.defaults.headers.common['Authorization'] = token;
                      localStorage.setItem('token', JSON.stringify(token));
                      commit( 'setUserLoginStatus', 2);
                      commit( 'setUserAuthStatus', response.data.uid);
                      commit( 'setUserToken', { token } );
                      dispatch( 'loadUser' )

                  } else {
                      commit( 'setUserLoginStatus', 3);
                      console.log("login error - secondary");
                      console.log(response.data)
                  }
              })
              .catch( (error) => {
                  commit( 'setUserLoginStatus', 3);
                  commit( 'setUserErrorData', error.response);
                  localStorage.removeItem('token');
                  console.log(error.response);
                  console.log(error.toJSON())
                }
              )
              .finally(() => {
                  console.log("Login process completed")
              });
        },

        /*
         * Load the current user
         */
        loadUser( { dispatch, commit } ) {
            commit( 'setUserLoadStatus', 1 );

            UserAPI.getUser()
                .then( (response) => {
                    const user = response.data.data;
                    if (user.id) {
                        commit( 'setUser', user );
                        commit( 'setUserAuthStatus', user.id );
                        commit( 'setUserLoadStatus', 2 );
                        dispatch( 'canUserReadWriteDatabases' );

                    } else {
                        // user response was empty
                        commit( 'setUser', {} );
                        commit( 'setUserLoadStatus', 3 )
                    }
                })
                .catch( (error) => {
                    commit( 'setUser', {} );
                    commit( 'setUserLoadStatus', 3 );
                    commit( 'setUserErrorData', error.response);
                    console.log(error.toJSON())
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
                    commit( 'setUsersLoadStatus', 2 )
                })
                .catch( (error) => {
                    commit( 'setUsers', [] );
                    commit( 'setUsersLoadStatus', 3 );
                    commit( 'setUserErrorData', error.response);
                    console.log(error.toJSON())
                });
        },

        /*
         * Adds or Edits a user
         */
        editUser( { commit }, data ) {
            commit( 'setUserSaveStatus', 1 );

            UserAPI.putUpdateUser( data )
                .then( (response) => {
                    commit( 'setUserSaveStatus', 2 );
                    commit( 'setUpdated', response.data.data.user.updated )
                    console.log(response.data);
                })
                .catch( (error) => {
                    commit( 'setUserSaveStatus', 3 );
                    commit( 'setUserErrorData', error.response);
                    console.log(error.toJSON())
                });
        },

        /*
         * Delete a single user
         */
        deleteUser( { commit }, data ) {
            commit( 'setUserDeleteStatus', 1 );

            UserAPI.deleteUser( data )
                .then( (response) => {
                    console.log(response);
                    commit( 'setUserDeleteStatus', 2);
                    commit( 'removeDeleted', data )
                })
                .catch( (error) => {
                    commit( 'setUserDeleteStatus', 3 );
                    commit( 'setUserErrorData', error.response);
                    console.log(error.toJSON())
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
                        // need to remove this - else the public view user check manages to fetch the user again!!
                        window.axios.defaults.headers.common['Authorization'] = null;
                        commit( 'setCommitLogoutSuccess', 2 )
                    }
                })
                .catch( (error) => {
                    commit( 'setUserLogoutStatus', 3 );
                    console.log(error.toJSON())
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
                    commit('setEmailCheckStatus', 2)
                })
                .catch( (error) => {
                    commit('setEmailCheckStatus', 3);
                    commit('setEmailCheck', {});
                    console.log(error.toJSON())
                });
        },

        /*
         *  Allow clearing error message from module after its displayed
         */
        clearUserError( { commit }) {
            commit('setErrorMessage', null)
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
            state.userLoadStatus = status
        },

        /*
         * Sets the users load status
         */
        setUsersLoadStatus( state, status ) {
            state.usersLoadStatus = status
        },

        /*
         * Sets the user
         */
        setUser( state, user ) {
            state.user = user
        },

        /*
         * Sets the users array
         */
        setUsers( state, users ) {
            state.users = users
        },

        /*
         * Sets the user into the users array
         */
        setInUsers( state, arr ) {
            let post = arr[0];
            if (post.type === 'login' || post.type === 'both') {
                state.users.loginUsers.push(arr[1].loginUsers)
            }
            if (post.type === 'database' || post.type === 'both') {
                state.users.databaseUsers.push(arr[1].databaseUsers)
            }
        },

        /*
         * Sets the user token
         */
        setUserToken( state, token ) {
            state.token = token
        },

        /*
         * Sets the user POST status
         */
        setUserSaveStatus( state, status ) {
            state.userSaveStatus = status
        },

        /*
         *  Saves a comma separated string of keys
         */
        setUpdated( state, data ) {
            state.updated = data
        },

        /*
         * Set the user logging in process status
         */
        setUserLoginStatus( state, status ) {
            state.userLoginStatus = status
        },

        /*
         * Set the user log out in process status
         */
        setUserLogoutStatus( state, status ) {
            state.userLogoutStatus = status
        },

        /*
         *  Handle the logout cleanup
         */
        setCommitLogoutSuccess( state, status ) {
            state.userLoadStatus = 0;
            state.userAuthStatus = 0;
            state.userLoginStatus = 0;
            state.token = '';
            state.user = {};
            state.userLogoutStatus = status;
        },

        /*
         *   Set and track the logged in user bu UID
         */
        setUserAuthStatus( state, uid ) {
            state.userAuthStatus = uid
        },

        /*
         *   Set the email check status
         */
        setEmailCheckStatus( state, status ) {
            state.emailCheckStatus = status
        },

        /*
         *   Set the email check result
         */
        setEmailCheck( state, result ) {
            state.emailCheck = result
        },

        /*
         *  Set the user deletion process status
         */
        setUserDeleteStatus( state, status ) {
            state.userDeleteStatus = status
        },

        /*
         *  Remove deleted user from local cache
         */
        removeDeleted( state, data ) {
            if (data.type === 'login') {
                let users = state.users.loginUsers;
                state.users.loginUsers = users.filter(user => user.id !== data.id)
            }
            if (data.type === 'database') {
                let users = state.users.databaseUsers;
                state.users.databaseUsers = users.filter(user => user._id !== data.id)
            }
        },

        /*
         * For tracking errors passed from BE
         */
        setUserErrorData( state, error ) {
            state.errorData = error;
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
            return state.userLoadStatus
        },

        /*
         *   Returns the users load status.
         */
        getUsersLoadStatus( state ) {
            return state.usersLoadStatus
        },

        /*
         *   Returns the user.
         */
        getUser( state ) {
            return state.user
        },

        /*
         *   Returns the users array.
         */
        getUsers( state ) {
            return state.users
        },

        /*
         * Gets the user auth status - returns the users ID
         */
        getUserAuthStatus( state ) {
            return state.userAuthStatus
        },

        /*
         *   Gets the user save status
         */
        getUserSaveStatus( state ) {
            return state.userSaveStatus
        },

        /*
         *  Gets the comma separated string of keys
         */
        getUpdated( state ) {
            return state.updated
        },

        /*
         * Get the email check status
         */
        getEmailCheckStatus( state ) {
            return state.emailCheckStatus
        },

        /*
         *   Get the email check result
         */
        getEmailCheck( state ) {
            return state.emailCheck
        },

        /*
         *   Returns the login process status
         */
        getUserLoginStatus( state ) {
            return state.userLoginStatus
        },

        /*
         *   Returns the logout process status
         */
        getUserLogoutStatus( state ) {
            return state.userLogoutStatus
        },

        /*
         *  Get the user deletion process status
         */
        getUserDeleteStatus( state ) {
            return state.userDeleteStatus
        },

        /*
         *   Checks if the token is set - toke indicate that the user has an API token
         */
        isLoggedIn( state ) {
            return !!state.token
        },

        /*
         * Shortcut to check the root user account
         */
        getIsRoot( state ) {
            return state.user.user_role.hasRoot
        },

        /*
         *  Shortcut to check for an Admin User
         */
        getIsAdmin( state ) {
            return (state.user.admin_user === "1")
        },

        /*
         *  Shortcut to check if its an anonymous connection
         */
        getIsAnonymous( state ) {
            return (state.user.user_role && state.user.user_role.isAnonymous && state.user.user_role.isAnonymous === true)
        },

        /*
         *  Shortcut to check for a Control User
         *  It will be assumed that the Control USer should have most basic roles available
         */
        getIsControlUser( state ) {
            return (state.user.control_user === "1")
        },

        getUserRoles( state ) {
            return state.user.user_role ? state.user.user_role.roles : []
        },

        /*
         *  Evaluate the users MongoDb roles - if any exists
         */
        canUserAdminUsers( state ) {
            let rolesAllowed = [
                'root',
                'dbOwner',
                'userAdmin',
                'userAdminAnyDatabase',
            ];
            let roles = state.user.user_role && state.user.user_role.roles ? state.user.user_role.roles : [];
            let allowed = false;
            if (roles) {
                roles.forEach((role) => {
                    rolesAllowed.map((allow) => {
                        if (allow === role.role) {
                            allowed = true
                        }
                    })
                });
            }

            return allowed
        },

        /*
         * The dbAdmin~ roles cannot execute find on user collections
         */
        isUserDbAdmin( state ) {
            let roles = [
               'dbAdmin',
               'dbAdminAnyDatabase'
            ];

            let userRoles = state.user.user_role && state.user.user_role.roles ? state.user.user_role.roles : [];
            let isDbAdmin = false;
            if (userRoles) {
                userRoles.forEach((role) => {
                    roles.map((dbadmin) => {
                        if (dbadmin === role.role) {
                            isDbAdmin = true
                        }
                    })
                });
            }

            return isDbAdmin
        },

        /*
         *   Return the logged in users name
         */
        getUsersName( state ) {
            return state.user.name
        },

        /*
         *  Return the total count of all user account
         */
        getUsersCount( state ) {
            if (state.users && state.users.loginUsers) {
                return state.users.loginUsers.length + state.users.databaseUsers.length
            }
            return 0
        },

        /*
         * For tracking errors passed from BE
         */
        getUserErrorData( state ) {
            return state.errorData
        },

        /*
         * Return errors from .data.errors (422) or .data as an object (500 exception)
         */
        getUserErrorMessage( state ) {
            if (state.errorData) {
                return state.errorData.data.errors ?
                    state.errorData.data.errors :
                    state.errorData.data ?
                        state.errorData.data :
                        {}
            }
            return '';
        },
    }
};
