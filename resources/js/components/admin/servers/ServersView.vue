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
    .pma-servers-view {
        float: left;
        height: 100vh;
        width: auto;

        .servers-inner {
            table {
                border: 1px solid $infoColor;
                border-radius: 5px;
                box-shadow: 2px 2px 5px $cccColor;
            }
            table th {
                background-color: $tableHeaderBg;
                color: $white;
                font-size: 1.2rem;
                padding: 4px;
            }
            table .server-info {
                background-color: $infoBgColor;
                padding: 4px;
                text-align: center;
            }
            table.bordered th.bb {
                border-bottom: 1px solid $infoColor;
            }
            table.bordered th.rb {
                border-right: 1px solid $infoColor;
            }
            table.bordered td {
                border-bottom: 1px solid $infoColor;
                min-width: 100px;
                padding-left: 20px;
                text-align: left;
            }
            table.bordered td.w50 {
                min-width: 49.9%;
                text-align: right;
            }
            table.bordered td.title {
               width: 15rem;
            }
            table.bordered td.bb {
                border-bottom: 1px solid $infoColor;
            }
            .activate-checkbox {
                padding-left: 2rem;

                input {
                    margin: 0;
                }
            }
            p.field {
                text-align: right;
                width: 350px;

                .input {
                    border-radius: 5px;
                    padding: 5px;
                    width: 100%;

                    input {
                        margin: 0 0 0 auto;
                        max-width: 266px !important;
                    }
                }
            }
            .checkbox {
                margin: 0 0 0 30px !important;
                width: 1rem;
            }
        }
    }
</style>

<template>
    <div id="pma-servers-view" class="pma-servers-view align-left" v-show="show">
        <div class="servers-inner">
            <div class="servers-head">
                <h3 v-text="showLanguage('servers', 'title')"></h3>
                <h5 v-text="showLanguage('servers', 'demo')"></h5>
                <p v-show="getServersCount">
                    <span v-text="showLanguage('servers', 'none')"></span>
                    <span class="pma-link" v-on:click="setupServer" v-text="showLanguage('servers', 'createFirst')"></span>
                </p>
                <p v-show="!getServersCount">
                    <span v-text="showLanguage('servers', 'add')"></span>
                    <span class="pma-link" v-on:click="setupServer" v-text="showLanguage('servers', 'addNew')"></span>
                </p>
                <p v-show="message">{{ message }}</p>
            </div>
            <div v-show="createNew || editing" class="server-create">
                <p v-show="error">{{ error }}</p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'host')"></span>: <input v-model="form.host" type="text" >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'port')"></span>: <input v-model="form.port" type="text" >
                    </label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'username')"></span>: <input v-model="form.username" type="text" ></label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'password')"></span>: <input v-model="form.password" type="password" ></label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'confirm')"></span>: <input v-model="form.password2" type="password" ></label>
                </p>
                <p class="field">
                    <label class="input-group-label input">
                        <span v-text="showLanguage('servers', 'active')"></span>: <input class="checkbox u-pull-left" v-model="form.active" type="checkbox" >
                    </label>
                </p>
                <p class="field" v-show="createNew">
                    <button class="button" type="submit" v-on:click="createServer" v-text="showLanguage('servers', 'createButton')"></button>
                </p>
                <p class="field" v-show="editing">
                    <button class="button" type="submit" v-on:click="createServer" v-text="showLanguage('servers', 'updateButton')"></button>
                </p>
            </div>
            <server-config @activateServer="activateServer($event)" @edit="edit($event)" @delete="deleteConfig($event)" v-for="(server, index) in getServers" :key="index" v-bind:server="server"></server-config>
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
                show: false,
                hostConfigs: [],
                server: {},
                createNew: false,
                editing: false,
                deleteId: null,
                form: {
                    host: null,
                    port: 27017,
                    username: null,
                    password: null,
                    password2: null,
                    active: null
                },
                setup: {
                    host: null,
                    port: 27017,
                    username: null,
                    password: null,
                    password2: null,
                    active: 0
                },
                message: null,
                error: null
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
                return this.$store.getters.getServers;
            },

            /*
             *  If no servers are configured for the user (control user), let them know
             */
            getServersCount() {
                return !(this.$store.getters.getServersCount >= 1);
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

            /*
             *   Show component
             */
            showComponent() {
                // load all servers allocated to the current user
                this.$store.dispatch( 'loadServers' );
                this.show = true;
            },

            /*
             *   Hide component
             */
            hideComponent() {
                this.show = false;
            },

            /*
             *  Display the server creation form
             */
            setupServer() {
                this.form = this.setup;
                this.createNew = !this.createNew;
            },

            /*
             *  Validate the server data before sending
             */
            validataServer() {
                if ((this.editing && this.form.password) || this.createNew) {
                    if (this.form.password !== this.form.password2) {
                        this.error = this.showLanguage('servers', 'passwordsError');
                        return false;
                    }
                }
                return true;
            },

            /*
             *  Submit server configuration
             */
            createServer() {
                if (this.validataServer()) {
                    this.$store.dispatch('saveServer', this.form);
                }
            },

            /*
             *  Handle the aftermath of the new server addition
             */
            completeCreateServer() {
                let status = this.$store.getters.getSaveServerStatus;
                if (status == 1) {
                    this.completeCreateServer();
                }
                if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('servers', 'success'), timer: 5000 });
                    this.createNew = false;
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('servers', 'error'), timer: 7000 })
                }
            },

            /*
             *  Activate a server - any active server will be set to inactive
             */
            activateServer( id ) {
                if (id) {
                    this.$store.dispatch('activateServer', id);
                }
            },

            /*
             *  Edit the server configuration
             */
            edit(id) {
                this.form = this.$store.getters.getServerConfiguration(id);
                this.editing = !this.editing;
            },

            /*
             *  Delete the server configuration
             */
            deleteConfig(id) {
                this.deleteId = id;
                EventBus.$emit('delete-confirmation', {id: id, element: 'server', notification: this.showLanguage('servers', 'deleteConfirm') });
            },

            /*
             *  Confirm the deletion
             */
            deleteConfirmed(id) {
                if (id === this.deleteId) {
                    this.$store.dispatch('deleteServer', id);
                    this.handleDeletion();
                }
            },

            /*
             *  Handle the delete results
             */
            handleDeletion() {
                let status = this.$store.getters.getServerDeleteStatus;
                if (status === 1) {
                    this.handleDeletion();
                }
                if (status == 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('servers', 'deleteSuccess'), timer: 5000 });
                    this.deleteId = null;
                }
                if (status == 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('servers', 'deleteFailed'), timer: 7000 });
                }
            },

            /*
             *  Cancel the deletion
             */
            deleteCancelled(id) {
                this.deleteId = null;
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
