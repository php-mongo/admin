<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Transfer.vue 1001 28/9/20, 10:14 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Transfer.vue
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
</style>

<template>
    <div id="pma-transfer" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('transfer', 'title')"></h3>
        </div>
        <form class="panel-form">
            <div style="padding-top: 20px;" ref="collection">
                <h3 class="collection-title"><span v-text="showLanguage('transfer', 'collections')"></span> [<label><span v-text="showLanguage('transfer', 'all')"></span> <input type="checkbox" v-on:click="checkAll()" v-model="form.all"></label>]</h3>
                <ul class="list">
                    <li v-for="(coll, index) in collections" :key="index" v-bind:coll="coll"><label><input type="checkbox" @change="checkCollection" v-model="form.collections" :value="coll.collection.name"> {{ coll.collection.name }}</label></li>
                </ul>
                <div class="clear"></div>
            </div>
            <div>
                <h3 v-text="showLanguage('transfer', 'target')"></h3>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><label for="target_atlas" v-text="showLanguage('transfer', 'atlas')"></label>?</td>
                            <td><input type="checkbox" id="target_atlas" @change="setAtlas" v-model="form.atlas"></td>
                        </tr>
                        <tr>
                            <td><label for="target_dns" v-text="showLanguage('transfer', 'dns')"></label>?</td>
                            <td><input type="checkbox" id="target_dns" v-model="form.dns"></td>
                        </tr>
                        <tr>
                            <td><label for="target_secure_tls" v-text="showLanguage('transfer', 'secure')"></label>:</td>
                            <td>
                                <label for="target_secure_none">
                                    <input id="target_secure_none" type="radio" v-model="form.secure" value="none">
                                    <span v-text="showLanguage('transfer', 'none')"></span>
                                </label>
                                <label for="target_secure_tls">
                                    <input id="target_secure_tls" type="radio" v-model="form.secure" value="tls">
                                    <span v-text="showLanguage('transfer', 'tls')"></span>
                                </label>
                                <label for="target_secure_ssl">
                                    <input id="target_secure_ssl" type="radio" v-model="form.secure" value="ssl">
                                    <span v-text="showLanguage('transfer', 'ssl')"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="target_host" ref="host" v-text="showLanguage('transfer', 'host')"></label>:</td>
                            <td><input type="text" id="target_host" @change="checkHost" v-model="form.host"></td>
                        </tr>
                        <tr>
                            <td><label for="target_port" v-text="showLanguage('transfer', 'port')"></label>:</td>
                            <td><input type="text" id="target_port" v-model="form.port"></td>
                        </tr>
                        <tr>
                            <td><label for="target_auth" v-text="showLanguage('transfer', 'authenticate')"></label>?</td>
                            <td><input type="checkbox" id="target_auth" v-model="form.authenticate"></td>
                        </tr>
                        <tr>
                            <td><label for="target_username" ref="username" v-text="showLanguage('transfer', 'username')"></label>:</td>
                            <td><input type="text" id="target_username" @change="checkUsername" v-model="form.username"></td>
                        </tr>
                        <tr>
                            <td><label for="target_password" ref="password" v-text="showLanguage('transfer', 'password')"></label>:</td>
                            <td><input type="password" id="target_password" @change="checkPassword" v-model="form.password"></td>
                        </tr>
                        <tr>
                            <td><label for="target_authdb" v-text="showLanguage('transfer', 'authDatabase')"></label>:</td>
                            <td><input type="text" id="target_authdb" v-model="form.authDatabase"></td>
                        </tr>
                        <tr>
                            <td><label for="target_remote" ref="database" v-text="showLanguage('transfer', 'remoteDatabase')"></label>:</td>
                            <td><input type="text" id="target_remote" @change="checkRemoteDb" v-model="form.remoteDatabase"></td>
                        </tr>
                    </tbody>
                </table>
                <br>
            </div>
            <div>
                <h3 v-text="showLanguage('transfer', 'indexes')"></h3>
                <label for="target_indexes"><span v-text="showLanguage('transfer', 'copyIndexes')"></span>? <input id="target_indexes" type="checkbox" v-model="form.indexes"></label>
                <br><br>
            </div>
            <div>
                <p v-show="errorMessage || message">
                    <span class="msg">
                        <span class="error">{{ errorMessage }}</span>
                        <span ref="success" class="action">{{ message }}</span>
                    </span>
                </p>
                <h3 class="u-pull-left" v-text="showLanguage('transfer', 'confirm')"></h3>
                <button class="button pl135" v-on:click="runTransfer($event)" v-text="showLanguage('transfer', 'transfer')"></button>
                <p>&nbsp;</p>
            </div>
        </form>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        name: "Transfer",

        /*
         *  Component data container
         */
        data() {
            return {
                collections: [],
                errorMessage: null,
                data: null,
                form: {
                    atlas: false,
                    authenticate: false,
                    authDatabase: null,
                    collections: [],
                    dns: false,
                    database: null,
                    host: null,
                    indexes: true,
                    password: null,
                    port: 27017,
                    remoteDatabase: null,
                    secure: 'none',
                    username: null,
                },
                index: 0,
                limit: 55,
                message: null,
                results: null,
                key: null,
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

            /*
            *   The database will already be loaded, therefore we should be able to retrieve data when 'show' is triggered'
            */
            getDatabase() {
                this.data = this.$store.getters.getDatabase;
                if (this.data) {
                    this.collections = this.data.collections;
                    this.form.database = this.data.db.databaseName;
                }
            },

            checkAll() {
                setTimeout( () => {
                    if (this.form.all === false) {
                        this.form.collections = [];
                    }
                    if (this.form.all === true) {
                        this.form.json = false;
                        this.form.collections = [];
                        this.collections.forEach( (collection) => {
                            this.form.collections.push(collection.collection.name);
                        });
                        this.checkCollection();
                    }
                }, 500);
            },

            setAtlas() {
                if (this.form.atlas === true) {
                    this.form.dns = true;
                    this.form.authDatabase = 'admin';
                    this.form.secure = 'tls';
                    this.form.authenticate = true;
                }
            },

            checkCollection() {
                if (this.form.collections.length >= 1)  {
                    this.$jqf(this.$refs.collection).replace(["has-error", "success"]);
                    this.errorMessage = '';
                }
            },

            checkHost() {
                if (this.form.host)  {
                    this.$jqf(this.$refs.host).replace(["has-error", "success"]);
                    this.errorMessage = '';
                }
            },

            checkUsername() {
                if (this.form.username)  {
                    this.$jqf(this.$refs.username).replace(["has-error", "success"]);
                    this.errorMessage = '';
                }
            },

            checkPassword() {
                if (this.form.password)  {
                    this.$jqf(this.$refs.password).replace(["has-error", "success"]);
                    this.errorMessage = '';
                }
            },

            checkRemoteDb() {
                if (this.form.remoteDatabase)  {
                    this.$jqf(this.$refs.database).replace(["has-error", "success"]);
                    this.errorMessage = '';
                }
            },

            runTransfer(event) {
                event.preventDefault();
                if (this.form.collections.length === 0) {
                    this.$jqf(this.$refs.collection).replace(["success", "has-error"]);
                    this.errorMessage = this.showLanguage('transfer', 'collectionsError');
                    return;
                }
                if (!this.form.host) {
                    this.$jqf(this.$refs.host).replace(["success", "has-error"]);
                    this.errorMessage = this.showLanguage('transfer', 'hostError');
                    return;
                }
                if (this.form.authenticate === true) {
                    if (!this.form.username) {
                        this.$jqf(this.$refs.username).replace(["success", "has-error"]);
                        this.errorMessage = this.showLanguage('transfer', 'usernameError');
                        return;
                    }
                    if (!this.form.password) {
                        this.$jqf(this.$refs.password).replace(["success", "has-error"]);
                        this.errorMessage = this.showLanguage('transfer', 'passwordError');
                        return;
                    }
                }
                if (!this.form.remoteDatabase) {
                    this.$jqf(this.$refs.database).replace(["success", "has-error"]);
                    this.errorMessage = this.showLanguage('transfer', 'databaseError');
                    return;
                }
                this.message = 'Sending transfer request...';
                this.errorMessage = '';
                this.$store.dispatch('transferDatabase', {params: this.form});
                this.handleTransfer();s
            },

            handleTransfer() {
                let status = this.$store.getters.getTransferStatus;
                if (status === 1) {
                    setTimeout(() => {
                        this.handleTransfer();
                    }, 250);
                }
                if (status === 2) {
                    // gtg
                    let inserted = this.$store.getters.getInserted;
                    this.$jqf(this.$refs.success).replace(["has-error", "success"]);
                    this.message =  this.showLanguage('transfer', 'success', inserted);
                }
                if (status === 3) {
                    this.message = '';
                    this.errorMessage = this.showLanguage('transfer', 'error');
                }
            }
        },

        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent();
            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels',() => {
                this.hideComponent();
            });

            /*
            *   Show this component
            *   Fetch the active database
            */
            EventBus.$on('show-database-transfer', () => {
                this.showComponent();
                this.getDatabase();
            });
        },
    }
</script>
