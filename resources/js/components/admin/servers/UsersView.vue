<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ServersView.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   ServersView.vue
  - @link         https://github.com/php-mongo/admin PHP MongoDB Admin
  - @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
  - @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
  - @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
  -  php-mongo-admin - License conditions:
  -  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
  -  This web application is available as Free Software and has no implied warranty or guarantee of usability.
  -  See licence.txt for the complete licensing outline.
  -  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
  -  See COPYRIGHT.php for copyright notices and further details.
  -->

<style lang="scss">
    /* @import '~@/abstracts/_variables.scss'; */
    #pma-users-view {
        .radio {
            width: 20px;
            margin: 0 5px 0 0;
        }
        .form-error {
            display: inline-block;
        }
    }
</style>

<template>
    <div id="pma-users-view" class="pma-servers-panel align-left" v-show="show">
        <div class="servers-inner">
            <div class="servers-head">
                <h3 v-text="showLanguage('users', 'title')"></h3>
                <p>
                    <button class="button" v-on:click="setDisplay('login')" v-text="showLanguage('users', 'loginUsersShow')"></button>
                    <button class="button" v-on:click="setDisplay('db')" v-text="showLanguage('users', 'databaseUsersShow')"></button>
                    <button class="button" v-on:click="addUser" v-text="showLanguage('users', 'createNew')"></button>
                </p>
                <p v-show="message">{{ message }}</p>
            </div>
            <div v-show="createNew || editing" class="server-create">
                <p class="form-error" v-show="error">{{ error }}</p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'name')"></span>: <input v-model="form.name" type="text" >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'username')"></span>: <input v-model="form.username" type="text" >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'password')"></span>: <input v-model="form.password" type="password" >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'confirm')"></span>: <input v-model="form.password2" type="password" >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'active')"></span>: <input class="checkbox u-pull-left" v-model="form.active" type="checkbox" >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input"><strong v-text="showLanguage('users', 'type')"></strong></label>
                    <label class="input-group-label input">
                        <input class="radio u-pull-left" v-model="form.type" type="radio" value="login">
                        <span v-text="showLanguage('users', 'typeLogin')"></span>:
                    </label>
                    <label class="input-group-label input">
                        <input class="radio u-pull-left" v-model="form.type" type="radio" value="database">
                        <span v-text="showLanguage('users', 'typeDatabase')"></span>
                    </label>
                    <label class="input-group-label input">
                        <input class="radio u-pull-left" v-model="form.type" type="radio" value="both">
                        <span v-text="showLanguage('users', 'typeBoth')"></span>
                    </label>
                </p>
                <p v-if="this.form.type === 'database' || this.form.type === 'both' || this.form.roles.length >= 1">
                    <label class="input-group-label input"><strong v-text="showLanguage('users', 'roles')"></strong></label>
                    <i><span v-text="showLanguage('users', 'dbUser')"></span></i>
                    <role-selector @updateRole="updateRole" @removeRole="removeRole" @addRole="addRole" v-for="index in roleCount" :key="index"></role-selector>
                </p>
                <p>
                    <button class="button success" @click="saveUser" v-text="showLanguage('global', 'save')"></button>
                    <button class="button warning" @click="reset" v-text="showLanguage('global', 'reset')"></button>
                    <button class="button warning" @click="closeForm" v-text="showLanguage('global', 'cancel')"></button>
                </p>
            </div>
            <div v-if="display === 'login'">
                <h5 v-text="showLanguage('users', 'loginUsers')"></h5>
                <login-user @edit="edit" @remove-user="deleteUser" @activate="activate" v-for="(user, index) in loginUsers" :key="index" v-bind:user="user"></login-user>
            </div>
            <div v-if="display === 'db'">
                <h5 v-text="showLanguage('users', 'databaseUsers')"></h5>
                <database-user @edit="edit" @remove-user="deleteUser" v-for="(user, index) in databaseUsers" :key="index" v-bind:user="user"></database-user>
            </div>
        </div>
    </div>
</template>

<script>
    /*
     * Import the Event bus
     */
    import { EventBus } from '../../../event-bus.js';
    import RoleSelector from "./RoleSelector";
    import LoginUser from "./LoginUser";
    import DatabaseUser from "./DatabaseUser";

    export default {
        name: "UsersView",

        components: {
           RoleSelector,
            LoginUser,
            DatabaseUser
        },
        /*
         *   Data required for this component
         */
        data() {
            return {
                createNew: false,
                display: 'login',
                editing: false,
                error: null,
                form: {
                    name: null,
                    username: null,
                    password: null,
                    password2: null,
                    active: false,
                    roles: [],
                    type: false,
                    database: 'admin'
                },
                index: 0,
                limit: 55,
                message: null,
                roleCount: 1,
                show: false,
                loginUsers: [],
                databaseUsers: []
            }
        },

        /*
         *   Defines the computed properties on the component.
         */
        computed: {
            /*
             *  Get the server configs for the current user
             */
            getServers() {
                //return this.$store.getters.getServers;
            }
        },

        /*
         *   Define methods for the server component
         */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            setDisplay(context) {
                this.display = context;
            },

            addUser() {
                this.createNew = !this.createNew;
            },

            closeForm() {
                this.createNew = this.editing = false;
            },

            addRole() {
                this.roleCount += 1;
            },

            removeRole(role) {
                console.log("removing role: " + role);
                // ToDo:  run test to ensure uniformity and relativity of roles being added
                let roles = this.form.roles;
                this.form.roles = roles.map((r) => {
                    return r !== role
                });
            },

            updateRole(role) {
                // ToDo:  run test to ensure uniformity and relativity of roles being added
                function  match(r) {
                    return r === role;
                }
                if (role !== this.form.roles.find(match)) {
                    this.form.roles.push(role);
                }
            },

            getAllUsers() {
                this.$store.dispatch( 'loadUsers');
                this.handleGetAll();
            },

            handleGetAll() {
                let status = this.$store.getters.getUsersLoadStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index+=1;
                    setTimeout(() => {
                        this.handleGetAll();
                    }, 200);
                }
                if (status === 2) {
                    console.log("status: " + status);
                    let users = this.$store.getters.getUsers;
                    console.log(users);
                    this.loginUsers = users['loginUsers'];
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('users', 'processError')
                }
            },

            edit(id, context = 'login') {
                console.log("edit: " + id);
                console.log("edit context: " + context);
            },

            validateUser() {
                if (!this.form.name) {
                    this.error = "Name is required";
                    return false;
                }
                if (!this.form.username) {
                    this.error = "Username is required";
                    return false;
                }
                if (!this.form.password) {
                    this.error = "Password is required";
                    return false;
                }
                if (this.form.password !== this.form.password2) {
                    this.error = "Passwords do not match";
                    return false;
                }
                let regExp = new RegExp(this.form.username,"ig");
                if (this.form.password.search(regExp) !== -1) {
                    console.log("pwd: " + this.form.password);
                    console.log("regex: " + regExp);
                    console.log("pos: " + this.form.password.search(regExp));
                    this.error = "Your password may not contain your username";
                    return false;
                }
                if (!this.form.type) {
                    this.error = "You must select a type of user account";
                    return false;
                }
                if (this.form.type === 'both' || this.form.type === 'database') {
                    // check for at least 1 role
                    if (this.form.roles.length === 0) {
                        this.error = "You must select at least one role";
                        return false;
                    }
                }
                return true;
            },

            saveUser() {
                console.log(this.form);
                this.error = '';
                if (this.validateUser()) {
                    this.$store.dispatch('createUser', this.form );
                }
            },

            handleSave() {
                let status = this.$store.getters.getUserSaveStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index+=1;
                    setTimeout(() => {
                        this.handleSave();
                    }, 200);
                }
                if (status === 2) {
                    this.message = this.showLanguage('users', '')
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('users', 'processError')
                }
            },

            reset() {
                this.form = {
                    name: null,
                    username: null,
                    password: null,
                    password2: null,
                    active: false,
                    roles: [],
                    sync: false
                }
            },

            activate(id) {
                console.log("activate: " + id);
            },

            deleteUser(id, context = 'login') {
                console.log("delete: " + id);
                console.log("delete context: " + context);
            },

            /*
             *   Show component
             */
            showComponent() {
                this.show = true;
            },

            /*
             *   Hide component
             */
            hideComponent() {
                this.show = false;
            }
        },

        /*
        *    get on ur bikes and ride !!
        */
        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent();
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-users', () => {
                this.showComponent();
                this.getAllUsers();
            });
        }
    }
</script>
