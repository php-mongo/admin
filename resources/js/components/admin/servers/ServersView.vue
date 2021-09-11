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
    @import '~@/abstracts/_variables.scss';
    .servers-inner {
        p.field {
            span.info-text {
                display: inline-block;
                text-align: left;
            }
        }
        .form-error {
            display: inline-block;
        }
        .bordered {
            tr, th, td {
                &:hover {
                    background-color: $black;
                    color: $lightGrey;
                }
            }
        }
        table {
            th.server-uri {
                padding: 7px 4px !important;
                font-size: 0.8rem !important;
            }
        }
    }

</style>

<template>
    <div id="pma-servers-view" class="pma-servers-panel align-left" v-if="show">
        <div class="servers-inner">
            <div class="servers-head">
                <h5 v-text="showLanguage('servers', 'demo')"></h5>
                <p v-show="getServersCount">
                    <span v-text="showLanguage('servers', 'none')"></span>
                    <span class="pma-link" v-on:click="setupServer"
                          v-text="showLanguage('servers', 'createFirst')"></span>
                </p>
                <p v-show="getServersCount">
                    <span v-text="showLanguage('servers', 'add')"></span>
                    <span class="pma-link" v-on:click="setupServer"
                          v-text="showLanguage('servers', 'addNew')"></span>
                </p>
                <p v-show="message">{{ message }}</p>
            </div>
            <div v-show="createNew || editing" class="server-create">
                <p class="form-error" v-show="error">{{ error }}</p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'mongoCloud')"></span>:
                        <input
                            type="checkbox"
                            class="checkbox u-pull-left"
                            v-model="form.mongo_cloud"
                            v-on:click="updateConnection"
                        >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'host')"></span>:
                        <input
                            type="text"
                            v-model="form.host"
                            :placeholder="getHostPlaceholder"
                            v-on:focus="hostHelp = true"
                            v-on:blur="hostHelp = false"
                            v-on:keyup="updateConnection"
                        >
                    </label>
                    <span class="text-info info-text" v-show="hostHelp" v-text="showLanguage('servers', 'hostHelp')"></span>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'port')"></span>:
                        <input
                            type="text"
                            v-model="form.port"
                            :placeholder="showLanguage('servers', 'portPlaceholder')"
                            v-on:keyup="updateConnection"
                        >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'username')"></span>:
                        <input
                            type="text"
                            v-model="form.username"
                            :placeholder="showLanguage('servers', 'usernamePlaceholder')"
                            v-on:keyup="updateConnection"
                        >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'password')"></span>:
                        <input
                            type="password"
                            v-model="form.password"
                            :placeholder="showLanguage('servers', 'passwordPlaceholder')"
                            v-on:keyup="updateConnection"
                        >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'confirm')"></span>:
                        <input
                            type="password"
                            v-model="form.password2"
                            :placeholder="showLanguage('servers', 'confirmPlaceholder')"
                        >
                    </label>
                </p>
                <p class="field" v-show="this.form.mongo_cloud === true">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'mongoCloudDb')"></span>:
                        <input
                            type="text"
                            v-model="form.mongo_cloud_database"
                            :placeholder="showLanguage('servers', 'mongoCloudDbPlaceholder')"
                            v-on:keyup="updateConnection"
                        >

                    </label>
                    <span class="text-info info-text"
                          v-text="showLanguage('servers', 'mongoCloudDbInfo')"
                    ></span>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'active')"></span>:
                        <input
                            type="checkbox"
                            class="checkbox u-pull-left"
                            v-model="form.active"
                        >
                    </label>
                </p>
                <p class="field">
                    <span v-text="showConnection"></span>
                </p>
                <p class="field">
                    <button class="button" type="submit" v-on:click="createServer"
                            v-text="showLanguage('servers', 'createButton')"
                            v-show="createNew"></button>
                    <button class="button" type="submit" v-on:click="updateServer"
                            v-text="showLanguage('servers', 'updateButton')"
                            v-show="editing"></button>
                    <button class="button warning" @click="reset"
                            v-text="showLanguage('global', 'reset')"></button>
                    <button class="button warning" @click="cancel"
                            v-text="showLanguage('global', 'cancel')"></button>
                </p>
            </div>
            <server-config
                @activateServer="activateServer($event)"
                @edit="edit($event)"
                @delete="deleteConfig($event)"
                v-for="(server, index) in getServers"
                :key="index"
                v-show="!createNew && !editing"
                v-bind:server="server"
                v-bind:total="getTotal"
            ></server-config>
        </div>
    </div>
</template>

<script>
    /*
     * Import the Event bus
     */
    import { EventBus } from '../../../event-bus.js';

    /*
     *   Import components for the Server View
     */
    import ServerConfig from "./ServerConfig";

    export default {
        name: "ServersView",

        /*
         *   Register the components to be used by the home page.
         */
        components: {
           ServerConfig
        },

        /*
         *   Data required for this component
         */
        data() {
            return {
                connection: null,
                createNew: false,
                deleteId: null,
                editing: false,
                error: null,
                form: {
                    active: null,
                    host: null,
                    mongo_cloud: false,
                    mongo_cloud_database: '',
                    password: null,
                    password2: null,
                    port: 27017,
                    username: '',
                },
                hostConfigs: [],
                hostHelp: null,
                index: 0,
                limit: 55,
                message: null,
                show: false,
                server: {},
                servers: null,
                setup: {
                    active: 0,
                    host: null,
                    mongo_cloud: false,
                    mongo_cloud_database: '',
                    password: null,
                    password2: null,
                    username: '',
                    port: 27017,
                },
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
                return this.$store.getters.getServers
            },

            /*
             *  Track the total for this user
             */
            getTotal() {
                return this.$store.getters.getServersCount
            },

            /*
             *  If no servers are configured for the user (control user), let them know
             */
            getServersCount() {
                return (this.$store.getters.getServersCount >= 1)
            },

            /*
             *  Dynamic placeholder for host field
             */
            getHostPlaceholder(){
                return this.form.mongo_cloud === true ?
                    this.showLanguage('servers', 'hostMongoPlaceholder') :
                    this.showLanguage('servers', 'hostPlaceholder')
            },

            /*
             *  Show the pre configured URI
             */
            showConnection() {
                return this.connection
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
                return this.$store.getters.getLanguageString( context, key )
            },

            /*
             *  Display the server creation form
             */
            setupServer() {
                this.form = this.setup;
                this.createNew = !this.createNew
            },

            updateConnection() {
                this.error = null;
                setTimeout(() => {
                    let form = this.form, host = form.host;
                    if (!host) {
                        this.connection = '';
                        return
                    }
                    if (host === 'localhost' || host === '127.0.0.1') {
                        if (form.mongo_cloud === true) {
                            this.error = this.showLanguage('errors', 'servers.mongoCloudHost', host);
                            return
                        }
                        this.connection = this.configLocalhost(host);
                        return
                    }
                    if (form.mongo_cloud === true) {
                        this.connection = this.configMongoCloud(host);
                        return
                    }
                    this.connection = host + ":" + form.port
                },250)
            },

            configLocalhost(host) {
                  return "mongodb://" + host + ":" + this.form.port
            },

            configMongoCloud(host) {
                let password = '', p = 0, form = this.form, tail;
                if (form.password) {
                    for (p = 0; p <= form.password.length; p++) {
                        password += "*"
                    }
                }
                tail = "?retryWrites=true&w=majority";
                if (form.mongo_cloud_database) {
                    tail = "/" + form.mongo_cloud_database + tail
                }
                return "mongodb+srv://" + form.username + ":" + password + "@" + host + tail
            },

            /*
             *  Validate the server data before sending
             */
            validateServer() {
                this.error = null;
                if (this.createNew) {
                    if (!this.form.host) {
                        this.error = this.showLanguage('errors', 'servers.hostRequired');
                        return false
                    }
                    if (!this.form.username) {
                        this.error = this.showLanguage('errors', 'global.userRequired');
                        return false
                    }
                    if (!this.form.password) {
                        this.error = this.showLanguage('errors', 'global.passwordRequired');
                        return false
                    }
                }
                if ((this.editing && this.form.password) || this.createNew) {
                    if (this.form.password.length < this.$store.getters.getMinPwdLength) {
                        this.error = this.showLanguage('errors', 'global.passwordLength', this.$store.getters.getMinPwdLength);
                        return false
                    }
                    if (this.form.password !== this.form.password2) {
                        this.error = this.showLanguage('errors', 'global.passwordMatch');
                        return false
                    }
                }
                if (this.form.mongo_cloud === true && (this.form.host === 'localhost' || this.form.host === '127.0.0.1')) {
                    this.error = this.showLanguage('errors', 'servers.mongoCloudHost', this.form.host);
                    return false
                }
                return true
            },

            /*
             *  Submit server configuration
             */
            createServer() {
                if (this.validateServer()) {
                    this.$store.dispatch('saveServer', this.form);
                    this.completeCreateServer()
                }
            },

            /*
             *  Handle the aftermath of the new server addition
             */
            completeCreateServer() {
                let status = this.$store.getters.getServerSaveStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.completeCreateServer()
                    }, 200)
                }
                if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('servers', 'createSuccess'), timer: 5000 });
                    this.createNew = false
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('errors', 'servers.createError'), timer: 7000 })
                }
            },

            updateServer() {
                if (this.validateServer()) {
                    this.form.update = 1;
                    this.$store.dispatch('saveServer', this.form);
                }
            },

            /*
             *  Handle the aftermath of the new server addition
             */
            completeUpdateServer() {
                let status = this.$store.getters.getServerSaveStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.completeUpdateServer()
                    }, 200)
                }
                if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('servers', 'updateSuccess'), timer: 5000 });
                    this.editing = false
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('errors', 'servers.updateError'), timer: 7000 })
                }
            },

            /*
             *  Activate a server - any active server will be set to inactive
             */
            activateServer( id ) {
                if (id) {
                    this.$store.dispatch('activateServer', id)
                }
            },

            /*
             *  Edit the server configuration
             */
            edit(id) {
                this.form = this.$store.getters.getServerConfiguration(id);
                this.editing = !this.editing
            },

            /*
             *  Delete the server configuration
             */
            deleteConfig(id) {
                this.deleteId = id;
                EventBus.$emit('delete-confirmation', {id: id, element: 'server', notification: this.showLanguage('servers', 'deleteConfirm') })
            },

            /*
             *  Confirm the deletion
             */
            deleteConfirmed(id) {
                if (id === this.deleteId) {
                    this.$store.dispatch('deleteServer', id);
                    this.handleDeletion()
                }
            },

            /*
             *  Handle the delete results
             */
            handleDeletion() {
                let status = this.$store.getters.getServerDeleteStatus;
                if (status === 1) {
                    this.handleDeletion()
                }
                if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('servers', 'deleteSuccess'), timer: 5000 })
                    this.deleteId = null;
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('errors', 'servers.deleteFailed'), timer: 7000 })
                }
            },

            /*
             *  Cancel the deletion
             */
            deleteCancelled() {
                this.deleteId = null
            },

            reset() {
                this.form = this.setup;
            },

            cancel() {
                this.reset();
                if (this.editing) {
                    this.createNew = !this.editing
                }
                if (this.createNew) {
                    this.createNew = !this.createNew
                }
            },

            /*
             *   Show component
             */
            showComponent() {
                // load all servers allocated to the current user
                this.$store.dispatch( 'loadServers' );
                this.show = true
            },

            /*
             *   Hide component
             */
            hideComponent() {
                this.show = false
            },
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
            EventBus.$on('show-servers', () => {
                this.showComponent();
            });

            /*
            *    Confirmed deletion
            */
            EventBus.$on('confirm-delete-server', (id) => {
                this.deleteConfirmed(id);
            });

            /*
            *    Confirmed deletion
            */
            EventBus.$on('cancel-delete-server', (id) => {
                this.deleteCancelled(id);
            });
        }
    }
</script>
