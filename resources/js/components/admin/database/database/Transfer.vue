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
    @import '~@/abstracts/_variables.scss';
    .command-form {
        max-width: 40rem;

        label {
            display: inline-block;
            padding-right: 0;
        }

        ul {
            margin: 0;
            list-style: none;
        }

        input[type="text"] {
            min-width: 100px;
        }

        input {
            margin: 0;
        }

        .pl135 {
            margin-left: 135px;
        }

        .msg {
            .error {
                color: $errorBorder;
            }
        }
    }
</style>

<template>
    <div id="pma-transfer" class="pma-transfer align-left" v-if="show">
        <form class="command-form">
            <div style="padding-top: 20px;">
                <h3 class="collection-title"><span v-text="showLanguage('transfer', 'collections')"></span> [<label><span v-text="showLanguage('transfer', 'all')"></span> <input type="checkbox" v-on:click="checkAll()"></label>]</h3>
                <ul class="list">
                    <li v-for="(coll, index) in collections" :key="index" v-bind:coll="coll"><label><input type="checkbox" class="check_collection" v-model="form.collections" :value="coll.collection.name"> {{ coll.collection.name }}</label></li>
                </ul>
                <div class="clear"></div>
            </div>
            <div>
                <h3 v-text="showLanguage('transfer', 'target')"></h3>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><label for="target_socket" v-text="showLanguage('transfer', 'socket')"></label>:</td>
                            <td><input type="text" id="target_socket" v-model="form.socket"></td>
                        </tr>
                        <tr>
                            <td><label for="target_host" v-text="showLanguage('transfer', 'host')"></label>:</td>
                            <td><input type="text" id="target_host" v-model="form.host"></td>
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
                            <td><label for="target_username" v-text="showLanguage('transfer', 'username')"></label>:</td>
                            <td><input type="text" id="target_username" v-model="form.username"></td>
                        </tr>
                        <tr>
                            <td><label for="target_password" v-text="showLanguage('transfer', 'password')"></label>:</td>
                            <td><input type="password" id="target_password" v-model="form.password"></td>
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
                <h3 class="u-pull-left" v-text="showLanguage('transfer', 'confirm')"></h3>
                <button class="button pl135" v-on:click="runTransfer($event)" v-text="showLanguage('transfer', 'transfer')"></button>
            </div>
        </form>
        <p v-show="errorMessage || message">
            <span class="msg">
                <span class="error">{{ errorMessage }}</span>
                <span class="action">{{ message }}</span>
            </span>
        </p>
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
                    authenticate: false,
                    collections: [],
                    database: null,
                    host: null,
                    indexes: true,
                    password: null,
                    port: 27017,
                    socket: null,
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
                    // console.log(this.data.db.name);
                }
            },

            checkAll() {

            },

            runTransfer(event) {
                event.preventDefault();
                console.log(this.form);
                let data = {database: this.database,  params: this.form };
                this.$store.dispatch('transferDatabase', data);
                this.handleTransfer();
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
                }
                if (status === 3) {
                    this.errorMessage = 'Unable to complete transfer - please check your settings';
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
