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
                <h3>Servers Configuration Panel</h3>
                <p v-show="getServersCount">No server have been created yet. <span class="pma-link" v-on:click="setupServer">Setup Here</span></p>
                <p v-show="!getServersCount">Add another server configuration. <span class="pma-link" v-on:click="setupServer">Add New</span></p>
                <p v-show="message">{{ message }}</p>
            </div>
            <div v-show="createNew || editing" class="server-create">
                <p v-show="error">{{ error }}</p>
                <p class="field"><label class="input-group-label input">Host: <input v-model="form.host" type="text" ></label></p>
                <p class="field"><label class="input-group-label input">Port: <input v-model="form.port" type="text" ></label></p>
                <p class="field"><label class="input-group-label input">Username: <input v-model="form.username" type="text" ></label></p>
                <p class="field"><label class="input-group-label input">Password: <input v-model="form.password" type="password" ></label></p>
                <p class="field"><label class="input-group-label input">Confirm: <input v-model="form.password2" type="password" ></label></p>
                <p class="field"><label class="input-group-label input">Active: <input class="checkbox u-pull-left" v-model="form.active" type="checkbox" ></label></p>
                <p class="field" v-show="createNew"><button class="button" type="submit" v-on:click="createServer">Create</button></p>
                <p class="field" v-show="editing"><button class="button" type="submit" v-on:click="createServer">Update</button></p>
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
                        this.error = 'Passwords do not match';
                        return false;
                    }
                }
                return true;
            },

            /*
             *  Submit server configuration
             */
            createServer() {
                console.log(this.form);
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
                    EventBus.$emit('show-success', { notification: 'Server configuration created successfully', timer: 5000 });
                    this.createNew = false;
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: 'Server configuration was created - please try again', timer: 7000 })
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

            edit(id) {
                console.log("edit: " + id);
                let server = this.$store.getters.getServerConfiguration(id);
                this.form = server;
                this.editing = !this.editing;
            },

            deleteConfig(id) {
                this.deleteId = id;
                EventBus.$emit('delete-confirmation', {id: id, element: 'server', notification: 'Deleting this server configuration can not be reveresed. Continue?'});
            },

            deleteConfirmed(id) {
                console.log("deleteConfirmed id", id);
                console.log("deleteConfirmed deleteId", this.deleteId);
                if (id === this.deleteId) {
                    this.$store.dispatch('deleteServer', id);
                    this.handleDeletion();
                }
            },

            handleDeletion() {
                let status = this.$store.getters.getServerDeleteStatus;
                if (status === 1) {
                    this.handleDeletion();
                }
                if (status == 2) {
                    EventBus.$emit('show-success', { notification: 'Server configuration deleted succesfully', timer: 5000 });
                    this.deleteId = null;
                }
                if (status == 3) {
                    EventBus.$emit('show-error', { notification: 'Server configuration was not deleted - please try again', timer: 7000 });
                }
            },

            deletCancelled(id) {
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
                console.log("confirm-delete-server", id);
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
