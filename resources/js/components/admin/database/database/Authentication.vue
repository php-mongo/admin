<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Authenticate.vue 1001 28/9/20, 10:26 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Authenticate.vue
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
    @import '~@/abstracts/_variables.scss';
    #pma-authentication {
        .result {
            border: 1px solid $cccColor;
            margin: 5px;
            padding: 5px;

            span.delete {
                &:hover {
                    color: $red;
                }
            }
        }
    }
</style>

<template>
    <div id="pma-authentication" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('dbauth', 'title')"></h3>
        </div>
        <p>
            <button class="button" @click="addUser" v-text="showLanguage('dbauth', 'add')"></button>
        </p>
        <div class="header">
            <p class="msg" v-show="errorMessage || actionMessage">
                <span class="error">{{ errorMessage }}</span>
                <span class="action">{{ actionMessage }}</span>
            </p>
        </div>
        <div v-if="add || edit">
            <form class="panel-form">
                <label for="username" v-text="showLanguage('dbauth', 'username')"></label>
                <input id="username" type="text" v-model="form.username" :readonly="form.update === true">
                <span v-if="form.update === false">
                    <label for="password" v-text="showLanguage('dbauth', 'password')"></label>
                <input id="password" type="password" v-model="form.password">
                <label for="pwd2" v-text="showLanguage('dbauth', 'confirm')"></label>
                <input id="pwd2" type="password" v-model="form.password2">
                </span>
                <label for="readonly"><span v-text="showLanguage('dbauth', 'readOnly')"></span>?
                    <input id="readonly" type="checkbox" v-model="form.readonly" value="1">
                </label>
                <p>
                    <button class="button warning" @click="hide" v-text="showLanguage('dbauth', 'cancel')"></button>
                    <button class="button" @click="sendUser($event)" v-text="showLanguage('dbauth', 'save')"></button>
                </p>
            </form>
        </div>
        <div v-if="auth">
            <div v-show="auth.length >= 1" class="auth-results">
                <div class="result" v-for="(user, index) in auth" :key="index" :user="user">
                    <p>
                        <span class="pma-link" @click="editUser(index)" :title="showLanguage('title', 'editTitle')" v-text="showLanguage('dbauth', 'edit')"></span>
                        <span>||</span>
                        <span class="pma-link delete" @click="deleteUser(index)" :title="showLanguage('title', 'deleteTitle')" v-text="showLanguage('dbauth', 'delete')"></span>
                    </p>
                    <p v-html="prepUser( JSON.stringify(user) )"></p>
                    <p>&nbsp;</p>
                </div>
            </div>
            <div v-show="auth.length === 0" class="auth-results">
                <div class="result">
                    <p><strong v-text="showLanguage('dbauth', 'empty')"></strong></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        name: "Authenticate",

        /*
         *  Component data container
         */
        data() {
            return {
                actionMessage: null,
                add: false,
                auth: null,
                database: 'n/a',
                edit: false,
                errorMessage: null,
                form: {
                    database: null,
                    username: null,
                    password: null,
                    password2: null,
                    readonly: false,
                    update: false
                },
                index: 0,
                limit: 75, // limit the status check iterations
                show: false
            }
        },

        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key, str ) {
                if (str) {
                    return this.$store.getters.getLanguageString( context, key ).replace("%s", str);
                }
                return this.$store.getters.getLanguageString( context, key );
            },

            prepUser( user ) {
                return this.$convObj().jsonH(user);
            },

            clear() {
                this.actionMessage = '';
                this.errorMessage  = '';
            },

            addUser() {
                this.add = true;
            },

            editUser(index) {
                this.edit = true;
                this.form.update = true;
                this.form.database = this.database;
                let user = this.auth[index];
                this.form.username = user.user;
                let role = user.roles[0].role;
                if (role === 'read') {
                    this.form.readonly = true;
                }
                this.form.password = 'fake-password';
                this.form.password2 = 'fake-password';
            },

            hide() {
                this.form = {
                    database: null,
                    username: null,
                    password: null,
                    password2: null,
                    readonly: false,
                    update: false
                };
                this.add = false;
                this.edit = false;
            },

            /*
            *   The database will already be loaded, therefore we should be able to retrieve data when 'show' is triggered'
            */
            getDatabase() {
                this.data = this.$store.getters.getDatabase;
                if (this.data) {
                    this.database = this.data.db.databaseName;
                }
            },

            getDbAuth() {
                this.clear();
                this.$store.dispatch('getDatabaseAuth', { database: this.database });
                this.handleAuth();
            },

            handleAuth() {
                let status = this.$store.getters.getDbAuthStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleAuth();
                    }, 250);
                }
                if (status === 2) {
                    // gtg
                    this.auth = this.$store.getters.getDbAuth;
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('dbauth', 'error');
                }
            },

            validate() {
                if (this.form.username) {
                  if (this.form.password) {
                      if (this.form.password2 === this.form.password) {
                          if (this.database) {
                              return true;
                          }
                      }
                  }
                }
                this.errorMessage = this.showLanguage('dbauth', 'validation');
                return false;
            },

            sendUser(event) {
                if (this.validate()) {
                    event.preventDefault();
                    this.clear();
                    this.form.database = this.database;
                    this.$store.dispatch('saveDbUser', { database: this.database, params: this.form });
                    this.handleUser();
                }
            },

            handleUser() {
                let status = this.$store.getters.getDbUserStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleUser();
                    }, 250);
                }
                if (status === 2) {
                    // gtg
                    this.getDbAuth();
                    this.hide();
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('dbauth', 'error');
                }
            },

            deleteUser(index) {
                let user = this.auth[index];
                EventBus.$emit('delete-confirmation', { notification: this.showLanguage('dbauth', 'confirmDelete', user.user), id: user._id, element: 'dbauth' });
            },

            runDelete(id) {
                this.$store.dispatch('deleteDbUser', { database: this.database, user: id });
                this.handleDelete();
            },

            handleDelete() {
                let status = this.$store.getters.getDeleteDbUserStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleDelete();
                    }, 250);
                }
                if (status === 2) {
                    // gtg
                    this.getDbAuth();
                    this.actionMessage = this.showLanguage('dbauth', 'success');
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('dbauth', 'error');
                }
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
            },
        },

        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels',() => {
                this.hideComponent();
            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels',() => {
                this.hideComponent();
            });

            EventBus.$on('confirm-delete-dbauth',(id) => {
                this.runDelete(id);
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-authentication',() => {
                this.showComponent();
                this.getDatabase();
                this.getDbAuth();
            });
        },
    }
</script>
