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
        .admin-user {
            text-align: left;
        }
        p.field {
            text-align: left !important;
        }
    }
</style>

<template>
    <div id="pma-users-view" class="pma-servers-panel align-left" v-if="show">
        <div class="servers-inner">
            <div class="servers-head" v-show="isEnabled">
                <h3 v-text="showLanguage('users', 'title')"></h3>
                <p>
                    <button class="button" v-on:click="setDisplay('login')" v-text="showLanguage('users', 'loginUsersShow')"></button>
                    <button class="button" v-on:click="setDisplay('database')" v-text="showLanguage('users', 'databaseUsersShow')"></button>
                    <button class="button" v-on:click="showForm" v-text="showLanguage('users', 'createNew')"></button>
                </p>
                <p v-show="message">{{ message }}</p>
            </div>
            <div v-show="createNew || editing" class="server-create">
                <p class="form-error" v-show="error">{{ error }}</p>
                <p class="field">
                    <label class="input-group-label input">
                        <strong v-text="showLanguage('users', 'type')"></strong>
                    </label>
                    <label
                        class="input-group-label input"
                    >
                        <input
                            class="radio u-pull-left"
                            v-model="form.type"
                            type="radio"
                            value="login"
                            :disabled="editing && this.action.type === 'database'"
                        >
                        <span v-text="showLanguage('users', 'typeLogin')"></span>:
                    </label>
                    <label
                        class="input-group-label input"
                        v-show="hideNonAdminTypes === false"
                    >
                        <input
                            class="radio u-pull-left"
                            v-model="form.type"
                            type="radio"
                            value="database"
                            :disabled="editing && this.action.type === 'login'"
                        >
                        <span v-text="showLanguage('users', 'typeDatabase')"></span>
                    </label>
                    <label
                        class="input-group-label input"
                        v-show="hideNonAdminTypes === false && editing === false"
                    >
                        <input
                            class="radio u-pull-left"
                            v-model="form.type"
                            type="radio"
                            value="both"
                            :readonly="editing"
                        >
                        <span v-text="showLanguage('users', 'typeBoth')"></span>
                    </label>
                </p>
                <p class="field" v-if="showHideFields">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'name')"></span>:
                        <input
                            v-model="form.name"
                            type="text"
                            v-on:focus="onFocus($event, 'name')"
                            v-on:blur="onBlur($event)"
                        >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'username')"></span>:
                        <input
                            v-model="form.user"
                            type="text"
                            v-on:focus="onFocus($event, 'user')"
                            v-on:blur="onBlur($event)"
                            :disabled="editing === true && form.type === 'database'"
                        >
                    </label>
                    <span
                        class="text-info"
                        style="text-align: left !important"
                        v-html="showLanguage('users', 'notEditMongoUser')"
                        v-show="editing === true && form.type === 'database'"
                    ></span>
                </p>
                <p class="field" v-if="displayPassword">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'password')"></span>:
                        <input
                            v-model="form.password"
                            type="password"
                            v-on:focus="onFocus($event, 'password')"
                            v-on:blur="onBlur($event)"
                        >
                    </label>
                </p>
                <p class="field" v-if="displayPassword">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'confirm')"></span>:
                        <input
                            v-model="form.password2"
                            type="password"
                        >
                    </label>
                </p>
                <p class="field admin-user" v-if="!displayPassword && isAdminAccount">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'editPassword')"></span>:
                        <input
                            class="checkbox u-pull-left"
                            v-model="form.editPassword"
                            type="checkbox"
                        >
                    </label>
                </p>
                <p class="field" v-if="showHideFields">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'email')"></span>:
                        <input
                            v-model="form.email"
                            type="email"
                            v-on:focus="onFocus($event, 'email')"
                            v-on:blur="onBlur($event)"
                        >
                    </label>
                </p>
                <p class="field admin-user" v-if="showHideFields">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'adminUser')"></span>:
                        <input
                            class="checkbox u-pull-left"
                            v-model="form.isAdmin"
                            type="checkbox"
                            v-on:focus="onFocus($event, 'isAdmin')"
                            v-on:blur="onBlur($event)"
                        >
                    </label>
                    <span class="help-text" v-if="form.isAdmin === true" v-html="showLanguage('users', 'isAdminMessage')"></span>
                </p>
                <p class="field" v-if="showHideFields">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('users', 'active')"></span>:
                        <input
                            class="checkbox u-pull-left"
                            v-model="form.active"
                            type="checkbox"
                            v-on:focus="onFocus($event, 'active')"
                            v-on:blur="onBlur($event)"
                        >
                    </label>
                </p>
                <p v-if="this.form.type === 'database' || this.form.type === 'both' || this.form.roles.length >= 1">
                    <label class="input-group-label input">
                        <strong v-text="showLanguage('users', 'roles')"></strong>
                    </label>
                    <span v-if="createNew === true">
                        <role-selector
                            @updateRole="updateRole($event, (index-1))"
                            @removeRole="removeRole"
                            @addRole="addRole"
                            v-show="createNew"
                            v-for="index in roleCount"
                            :key="index-1"
                        ></role-selector>
                    </span>
                    <span v-if="editing === true">
                        <role-selector-edit
                            @updateRole="updateRole($event, index)"
                            @removeRole="removeRole"
                            @addRole="addRole"
                            @onFocus="onFocus($event, 'roles')"
                            @onBlur="onBlur($event, 'roles')"
                            v-show="editing"
                            v-for="(role, index) in form.roles"
                            :key="index"
                            v-bind:dbrole="role"
                        ></role-selector-edit>
                    </span>

                </p>
                <p>
                    <button class="button success"
                            @click="saveUser"
                            v-text="showLanguage('global', 'save')"
                            v-if="createNew"
                    ></button>
                    <button class="button success"
                            @click="saveEdit"
                            v-text="showLanguage('global', 'update')"
                            v-if="editing"
                    ></button>
                    <button class="button warning"
                            @click="reset"
                            v-text="showLanguage('global', 'reset')"
                    ></button>
                    <button class="button warning"
                            @click="closeForm"
                            v-text="showLanguage('global', 'cancel')"
                    ></button>
                </p>
            </div>
            <div v-if="display.current === 'login'">
                <h5 v-text="showLanguage('users', 'loginUsers')"></h5>
                <login-user
                    @activate="setActiveStatus($event, index)"
                    @edit-user="edit($event, index)"
                    @delete-user="deleteUser($event, index)"
                    @pma-main-panel-scroll="scrollToTop"
                    v-for="(user, index) in loginUsers"
                    :key="index"
                    v-bind:user="user"
                    v-bind:account="account"
                ></login-user>
            </div>
            <div v-if="display.current === 'database'">
                <h5 v-text="showLanguage('users', 'databaseUsers')"></h5>
                <database-user
                    @edit-user="edit($event, index)"
                    @delete-user="deleteUser($event, index)"
                    @pma-main-panel-scroll="scrollToTop"
                    v-for="(user, index) in databaseUsers"
                    :key="index"
                    v-bind:user="user"
                ></database-user>
            </div>
            <div>
                <p><span class="form-error" v-if="!enable" v-text="error"></span></p>
                <p>&nbsp;</p>
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
    import RoleSelectorEdit from "./RoleSelectorEdit";
    import LoginUser from "./LoginUser";
    import DatabaseUser from "./DatabaseUser";

    export default {
        name: "UsersView",

        components: {
            RoleSelector,
            RoleSelectorEdit,
            LoginUser,
            DatabaseUser
        },

        props: ['account'],

        /*
         *   Data required for this component
         */
        data() {
            return {
                action: {
                    id: null,
                    type: null,
                    user: null,
                    key: null
                },
                createNew: false,
                databaseUsers: [],
                display: {
                    current: 'login',
                    last: null
                },
                editing: false,
                editPassword: false,
                enable: true,
                error: null,
                errorData: null,
                fieldCache: {
                    name: null,
                    value: null
                },
                form: {
                    active: false,
                    database: 'admin',
                    editPassword: false,
                    email: null,
                    id: null,
                    isAdmin: 0,
                    name: null,
                    password: null,
                    password2: null,
                    roles: [],
                    type: 'login', // this defaults to Application login
                    user: null,
                },
                hideNonAdminTypes: false,
                index: 0,
                limit: 55,
                loginUsers: [],
                message: null,
                roleCount: 1, // a database user must have at least one role
                show: false,
                showEmail: true,
                updated: [],
            }
        },

        /*
         *   Defines the computed properties on the component.
         */
        computed: {
            /*
             *  Only show password fields for create or 'edit 'password'
             */
            displayPassword() {
                return (this.createNew === true || this.form.editPassword === true)
            },

            /*
             *  Tracking the number of users
             */
            getUserCount() {
                return this.$store.getters.getUsersCount
            },

            /*
             *  We may want to limit some actions and element to Admin Users only
             */
            isAdminAccount() {
                return this.account.admin_user === "1"
            },

            /*
             *  Only valid for users with correct permissions
             */
            isEnabled() {
                return this.enable
            },

            /*
             *  Show or hide the extra form fields
             */
            showHideFields() {
                return (this.form.type === 'login' || this.form.type === 'both')
            },

            /*
             *  We want to handle the Admin user criteria magically
             */
            watchIsAdmin() {
                return this.form.isAdmin === "1" || this.form.isAdmin === true
            },

            getRoles() {
                return this.form.roles
            },
        },

        /*
         *   Define methods for the server component
         */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key, str ) {
                if (str) {
                    let string = this.$store.getters.getLanguageString( context, key );
                    return string.replace("%s", str )
                }
                return this.$jqf().nl2br(this.$store.getters.getLanguageString( context, key ))
            },

            /*
             *  Method to change user display
             */
            setDisplay(context) {
                this.display.last = this.display.current;
                this.display.current = context ? context : ''
            },

            /*
             *  Show the user form
             */
            showForm(editing) {
                if (this.editing === true) {
                    this.reset()
                }
                editing === true ? this.editing = !this.editing : this.createNew = !this.createNew;
                if (this.createNew || this.editing) {
                    this.setDisplay()
                } else {
                    this.setDisplay(this.getDisplay())
                }
            },

            /*
             *  Hide user form
             */
            closeForm() {
                this.createNew = this.editing = false;
                this.setDisplay(this.getDisplay());
                this.reset()
            },

            /*
             *  Track how many roles have been added for the DB user
             */
            addRole() {
                this.roleCount += 1;
                if (this.editing === true) {
                    // cache before saving
                    this.fieldCache = {
                        name: 'roles',
                        value: this.form.roles
                    }
                    this.form.roles.push('');
                }
            },

            /*
             *  Remove role from roles array cache
             */
            removeRole(role) {
                // ToDo:  run test to ensure uniformity and relativity of roles being added
                if (this.roleCount > 1) {
                    // one role always required
                    let roles = this.form.roles;
                    this.form.roles = roles.filter(r => r !== role);
                    this.roleCount -= 1
                }
            },

            cleanRoles() {
                let roles = this.form.roles, arr = [], i;
                // remove the empty role
                for (i = 0; i < roles.length; i++) {
                    console.log("cleaning: " + roles[i]);
                    if (roles[i] && roles[i].length >= 1) {
                        arr.push(roles[i])
                    }
                }
                // return roles
                this.form.roles = arr
            },

            validateRoles() {
                // ToDo: only global roles for now
                let roles = this.form.roles, hasRead, hasWrite, i;
                for (i = 0; i < roles.length; i++) {
                    if (roles[i] === 'readAnyDatabase') {
                        hasRead = true
                    }
                    if (roles[i] === 'readWriteAnyDatabase') {
                        hasWrite = true
                    }
                    if (hasRead && hasWrite) {
                        this.removeRole('readAnyDatabase');
                        this.error = this.showLanguage('errors', 'users.incompatibleRoles')
                    }
                }
                if (this.editing && !this.error) {
                    // assume a role has been updated
                    this.pushToUpdate('roles')
                }
            },

            /*
             *  Makes sure roles are not added twice for a new user
             */
            updateRole(role, index) {
                console.log("received index: " + index);
                // ToDo:  run test to ensure uniformity and relativity of roles being added
                function match(r) {
                    return r === role
                }
                // check that role is not duplicated
                if (role !== this.form.roles.find(match)) {
                    this.form.roles[index] = role
                }
                this.validateRoles()
            },

            /*
             *  Runs on load - get al users from DB
             */
            getAllUsers() {
                this.$store.dispatch( 'loadUsers');
                this.handleGetAll()
            },

            /*
             *  Handle the users fetch process response
             */
            handleGetAll() {
                let status = this.$store.getters.getUsersLoadStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index+=1;
                    setTimeout(() => {
                        this.handleGetAll();
                    }, 200)
                }
                if (status === 2) {
                    this.loadUsers()
                }
                if (status === 3) {
                    this.errorData = this.$store.getters.getUserErrorData;
                    if (this.errorData.data.errors.error === 'Forbidden' && this.errorData.data.message === 'failed') {
                        EventBus.$emit('show-error', { notification: this.showLanguage('errors', 'users.user-manage-permission') });
                        this.enable = false;
                        this.error = this.showLanguage('errors', 'users.user-manage-permission')
                    } else {
                        EventBus.$emit('show-error', { notification: this.showLanguage('errors', 'processing') })
                    }
                }
            },

            loadUsers() {
                let users = this.$store.getters.getUsers;
                this.loginUsers = users['loginUsers'];
                this.databaseUsers = users['databaseUsers']
            },

            /*
             *  Set/unset active status
             */
            setActiveStatus(arr, index) {
                let status = arr[3] === "0" ? "1" : "0";
                this.action = {
                    action: 'activate',
                    id: arr[0],
                    type: arr[1],
                    user: arr[2],
                    key: index,
                    active: status
                };
                this.form.id = arr[0];
                this.form.type = arr[1];
                this.form.user = arr[2];
                this.form.active = status;
                this.updated.push({ key: 'active' })
                this.saveEdit();
            },

            getBoolean(input) {
                return input === "1" || input === 1 || input === true
            },

            /*
             *  Initialise a user edit process
             */
            edit(arr, index) {
                this.action = {
                    id: arr[0],
                    type: arr[1],
                    user: arr[2],
                    key: index
                };
                let user = this.action.type === 'login' ?
                    this.loginUsers[this.action.key] :
                    this.databaseUsers[this.action.key];

                this.form.active       = user.active;
                this.form.database     = 'admin';
                this.form.editPassword = false;
                this.form.email        = this.action.type === 'login' ? user.email : null;
                this.form.id           = this.action.id;
                this.form.isAdmin      = this.getBoolean(user.admin_user);
                this.form.name         = this.action.type === 'login' ? user.name : null;
                this.form.password     = null;
                this.form.password2    = null;
                this.form.roles        = [];
                this.form.type         = this.action.type;
                this.form.user         = user.user;

                if (this.action.type === 'database') {
                    // set the roles
                    user.roles.forEach((role) => {
                        this.form.roles.push(role.role)
                    })
                }
                this.showForm(true)
            },

            saveEdit() {
                this.form.updated = this.updated;
                this.$store.dispatch('editUser', this.form);
                this.handleEdit()
            },

            handleEdit() {
                let status = this.$store.getters.getUserSaveStatus;
                if (status === 1) {
                    setTimeout(() => {
                        this.handleEdit()
                    }, 250)
                }
                if (status === 2) {
                    this.handleEditSuccess()
                }
                if (status === 3) {
                    this.setEditMessage(status)
                }
            },

            setEditMessage(status) {
                if (status === 3) {
                    if (this.action.action === 'activate') {
                        this.setUserMessage(this.showLanguage('users', 'activateFailed'));
                    }
                }
            },

            handleEditSuccess() {
                if (this.action.action === 'activate') {
                    this.loginUsers = this.loginUsers.map((user) => {
                        if (user.id === this.action.id)  {
                            user.active = this.action.active;
                            user.message = this.showLanguage('users', 'activateSuccess')
                        }
                        return user
                    })
                } else {
                    this.updated.forEach((update) => {
                        if (this.action.type === 'login') {
                            this.loginUsers[this.action.key][update.key] = this.form[update.key]
                        }
                        if (this.action.type === 'database') {
                            // ToDo: confirm this handle roles correctly
                            this.databaseUsers[this.action.key][update.key] = this.form[update.key]
                        }
                    });
                    this.setUserMessage(
                        this.showLanguage('users', 'updateSuccess', this.$store.getters.getUpdated)
                    )
                }
                this.reset(5500)
            },

            /*
             *  Validate new user data
             */
            validateUser() {
                if (!this.form.user) {
                    this.error = this.showLanguage('errors', 'global.userRequired' );
                    return false
                }
                if (!this.form.password) {
                    this.error = this.showLanguage('errors', 'global.passwordRequired' );
                    return false
                }
                if (this.form.password.length < this.$store.getters.getMinPwdLength) {
                    this.error = this.showLanguage('errors', 'global.passwordLength', this.$store.getters.getMinPwdLength );
                    return false
                }
                if (this.form.password !== this.form.password2) {
                    this.error = this.showLanguage('errors', 'global.passwordMatch' );
                    return false
                }
                let regExp = new RegExp(this.form.user,"ig");
                if (this.form.password.search(regExp) !== -1) {
                    this.error = this.showLanguage('errors', 'users.password-content' );
                    return false
                }
                if (!this.form.type) {
                    this.error = this.showLanguage('errors', 'users.type' );
                    return false
                }
                if (this.form.type === 'login' || this.form.type === 'both') {
                    // check for email
                    if (this.form.email === null) {
                        this.error = this.showLanguage('errors', 'users.email');
                        return false
                    }
                    if (!this.form.name) {
                        this.error = this.showLanguage('errors', 'users.name' );
                        return false
                    }
                }
                if (this.form.type === 'both' || this.form.type === 'database') {
                    // check for at least 1 role
                    if (this.form.roles.length === 0) {
                        this.error = this.showLanguage('errors', 'users.role');
                        return false
                    }
                }
                return true;
            },

            validateEdit() {
                if (!this.form.id) {
                    this.error = this.showLanguage('errors', 'global.idRequired' );
                    return false
                }
                if (this.updated.length === 0) {
                    this.error = this.showLanguage('errors', 'global.noFieldUpdated' );
                    return false
                }
            },

            /*
             *  Save a new user
             */
            saveUser() {
                this.clearMessages(0);
                setTimeout(() => {
                    if (this.validateUser()) {
                        this.$store.dispatch( 'createUser', this.form );
                        this.handleSave()
                    }
                }, 100)
            },

            /*
             *  Handle the user saving process response
             */
            handleSave() {
                let status = this.$store.getters.getUserSaveStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index+=1;
                    setTimeout(() => {
                        this.handleSave();
                    }, 200)
                }
                if (status === 2) {
                    this.message = this.showLanguage('users', 'saveSuccess');
                    this.reset(3000)
                }
                if (status === 3) {
                    let errors = this.$store.getters.getUserErrorMessage;
                    this.error = errors.user ?
                        errors.user[0] :
                        errors.message ?
                            errors.message :
                            errors.error ?
                                errors.error :
                                this.showLanguage('users', 'processing');

                    setTimeout( () =>{
                        this.clearMessages(0)
                    }, 5000)
                }
            },

            /*
             *  Activate a user
             */
            activate(id) {
                console.log("activate: " + id)
            },

            /*
             *  Deleting a user
             */
            deleteUser(arr, index) {
                this.clearMessages(0);
                this.action = {
                    id: arr[0],
                    type: arr[1],
                    user: arr[2],
                    key: index
                };
                EventBus.$emit('delete-confirmation', {id: arr[0], element: 'user', notification: this.showLanguage('users', 'deleteConfirm', arr[2]) })
            },

            /*
             *  Deletion confirmed
             */
            deleteConfirmed() {
                this.$store.dispatch( 'deleteUser', this.action );
                this.handleDeletion()
            },

            /*
             *  Handle deletion results
             */
            handleDeletion() {
                let status = this.$store.getters.getUserDeleteStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index+=1;
                    setTimeout(() => {
                        this.handleDeletion();
                    }, 200)
                }
                if (status === 2) {
                    this.message = this.showLanguage('users', 'deleteSuccess');
                }
                if (status === 3) {
                    let errors = this.$store.getters.getUserErrorMessage;
                    this.setUserMessage(errors.error);
                    setTimeout( () => {
                        this.clearMessages(0)
                    }, 5000)
                }
            },

            getDisplay(form) {
                if (form) {
                    return this.form.type === 'database' ? 'database' : 'login'
                }
                return this.display.current ? this.display.current : this.display.last
            },

            /*
             *  Clear action cache
             */
            clearActive() {
                this.action = {
                    id: null,
                    type: null,
                    user: null,
                    key: null
                }
            },

            /*
            *  Clear cached messages
            */
            clearMessages(timer = 0) {
                this.clearUserMessage();
                setTimeout(() => {
                    this.error = false;
                    this.message = false
                }, timer)
            },


            /*
             *  Uses the message param on each user object
             */
            setUserMessage(msg) {
                this.message = msg; // also set the top view message
                if (this.action.type === 'login') {
                    this.loginUsers[this.action.key].message = msg
                }
                if (this.action.type === 'database') {
                    this.databaseUsers[this.action.key].message = msg
                }
            },

            /*
             *  Uses the message param on each user object
             */
            clearUserMessage() {
                if (this.action.type === 'login' && this.loginUsers[this.action.key]) {
                    this.loginUsers[this.action.key].message = ''
                }
                if (this.action.type === 'database' && this.databaseUsers[this.action.key]) {
                    this.databaseUsers[this.action.key].message = ''
                }
            },

            /*
            *  Reset cached data
            */
            reset(timer) {
                this.createNew = this.editing = this.hideNonAdminTypes = false;
                this.clearMessages(timer);
                this.setDisplay(this.getDisplay(true));
                this.clearActive();
                this.updated = [];
                this.form = {
                    active: false,
                    database: 'admin',
                    editPassword: false,
                    email: null,
                    id: null,
                    isAdmin: 0,
                    name: null,
                    password: null,
                    password2: null,
                    roles: [],
                    type: 'login', // this defaults to Application login
                    user: null,
                }
            },

            handleAdminUser() {
                if (this.form.isAdmin === true) {
                    this.form.type = 'login';
                    this.hideNonAdminTypes = true;
                } else {
                    this.hideNonAdminTypes = false;
                }
            },

            /*
             *  Scroll event to Panel View
             */
            scrollToTop() {
                EventBus.$emit('pma-main-panel-scroll', {})
            },

            onFocus(event, name) {
                if (!this.editing) {
                    return
                }
                if (name === 'roles') {
                    console.log("its a role");
                    this.fieldCache = {
                        name: 'roles',
                        value: this.form.roles
                    }
                    console.log(this.fieldCache.name);
                }
                if (name !== 'roles') {
                    this.fieldCache = {
                        name: name,
                        value: event.target.value
                    }
                }
            },

            onBlur(event, name) {
                if (!this.editing) {
                    return
                }
                if (name === 'roles') {
                    console.log("blur: roles");
                    // ToDo: make this a bit more thorough
                    this.cleanRoles();
                    // handle the roles array
                    if (this.form.roles.length !== this.fieldCache.value.length) {
                        this.pushToUpdate('roles')
                    }
                    /*else {
                        let matched = false;
                        let matchedCache = 0;
                        let cache = this.fieldCache.value;
                        let roles = this.form.roles;
                        let numRoles = roles.length;
                        cache.forEach((c) => {
                            roles.forEach((r) => {
                                if (c === r) {
                                    matched = c
                                }
                            });
                            if (c === matched) {
                                matchedCache++
                            }
                        });
                        if (matchedCache !== numRoles) {
                            this.pushToUpdate('roles')
                        }
                    }*/
                }
                if (!name) {
                    if (event.target.value !== this.fieldCache.value) {
                        this.pushToUpdate(this.fieldCache.name)
                    }
                }
                this.fieldCache = {
                    name: null,
                    value: null
                }
            },

            pushToUpdate(key) {
                function match(r) {
                    return r === key;
                }
                // check that key is not duplicated
                if (key !== this.updated.find(match)) {
                    this.updated.push(key)
                }
            },

            errorTimer() {
                setTimeout(() => {
                    this.error = ''
                }, 5000)
            },

            /*
             *   Show component
             */
            showComponent() {
                this.show = true
            },

            /*
             *   Hide component
             */
            hideComponent() {
                this.show = false
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
                this.hideComponent()
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-users', () => {
                this.showComponent();
                this.getAllUsers()
            });

            /*
            *    Confirmed deletion
            */
            EventBus.$on('confirm-delete-user', (id) => {
                this.deleteConfirmed(id)
            });

            /*
            *    Cancel deletion
            */
            EventBus.$on('cancel-delete-user', () => {
                this.clearActive()
            });
        },

        watch: {
            getUserCount() {
                this.loadUsers()
            },

            watchIsAdmin() {
                this.handleAdminUser()
            },

            watchError() {
                if (this.error) {
                    this.errorTimer()
                }
            }
        }
    }
</script>
