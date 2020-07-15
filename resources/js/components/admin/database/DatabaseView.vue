<style lang="scss">
    @import '~@/abstracts/_variables.scss';
    .pma-database-view {
        float: left;
        width: 96%;

        .database-inner {
            form {
                margin-bottom: 10px;
            }
            p {
                margin-bottom: 0;
                padding-left: 1px;

                input {
                    line-height: 1.65;
                    margin-top: 1px;
                    vertical-align: top ;
                }

                .button {
                    margin: 0;
                    padding: 0.5em 1em;
                }
            }
            p.drop {
                padding-left: 5px;

                label {
                    display: inline-block;
                }

                span {
                    vertical-align: middle;
                }

                input {
                    margin-right: 0.1rem;
                    vertical-align: sub;
                }

                .pma-link {
                    font-size: 1.1rem;
                    margin-right: 20px;
                    vertical-align: sub;
                }

                button {
                    padding: 0.3em 0.5em;
                }
            }
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
            table td {
                background-color: $infoBgColor;
                padding: 4px 4px 4px 8px;

                input {
                    margin: 3px 0 0 0;
                }
            }
            table.bordered td.tr, table.collection td.tr {
                text-align: right;
                width: 50%;
            }
            table.collection td {
                p {
                    padding-left: 25%;
                }
            }
            table.bordered {
                th.bb {
                    border-bottom: 1px solid $infoColor;
                }
                th.rb {
                    border-right: 1px solid $infoColor;
                }
                td {
                    border-bottom: 1px solid $infoColor;
                    text-align: left;
                }
                td.rb, table.collection td.rb {
                    border-right: 1px solid $infoColor;
                }
                td.text-center {
                    text-align: center !important;
                }
                td.vat {
                    vertical-align: top;
                }
            }
        }
    }
</style>

<template>
    <div id="pma-databases-view" class="pma-database-view align-left" v-show="show">
        <database-top-view></database-top-view>
        <database-card v-bind:db="getDatabase"></database-card>
    </div>
</template>

<script>
    /*
    *   Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    /*
    *   Import components for the Databases View
    */
    import DatabaseTopView from "./top/DatabaseTopView";
    import DatabaseCard from "./DatabaseCard";

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            DatabaseTopView,
            DatabaseCard
        },

        /*
        *   Data required for this component
        */
        data() {
            return {
                show: false,
                database: {}
            }
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            /*
            *  fetch the databases for iteration in the template
            */
            getDatabase() {
                return this.$store.getters.getDatabase;
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
                // return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Display the DB creation field and button
            */
            showCreateField() {
                this.createField = !this.createField;
            },

            /*
            *   Handle ethe Create Database function
            */
            createDatabase(event) {
                event.preventDefault();
                if (this.newDb) {
                    let db = this.newDb;
                    this.$store.dispatch( 'createDatabase', db );
                    this.checkNewDatabase();
                }
            },

            /*
            *   Handles the monitoring of the DB creation process
            */
            checkNewDatabase() {
                let status = this.$store.getters.getCreateDatabaseStatus;
                if (status === 1) {
                    let self = this;
                    setTimeout(function() {
                        self.checkNewDatabase();
                    }, 100);
                }
                if (status === 2) {
                    this.newDb = null;
                    this.createField = false;
                }
                if (status === 3) {
                    // error
                    this.errorText = "Error creating database: " + this.newDb;
                }
            },

            /*
            *   Handles the database deletions - one or many
            */
            deleteDatabases(event) {
                event.preventDefault();
                this.$store.dispatch( 'deleteDatabase', this.checked );
                this.checkDeleteDatabase();
            },

            /*
            *   Handles the monitoring of the DB creation process
            */
            checkDeleteDatabase() {
                let status = this.$store.getters.getDeleteDatabaseStatus;
                if (status === 1) {
                    let self = this;
                    setTimeout(function() {
                        self.checkDeleteDatabase();
                    }, 100);
                }
                if (status === 2) {
                    this.checked = [];
                    EventBus.$emit('uncheck-all-databases');
                }
                if (status === 3) {
                    // error
                    this.errorText = "Error deleting database: " + this.checked;
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
            }
        },

        /*
        *    get on ur bikes and ride !!
        */
        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', function( ) {
                this.hideComponent();
            }.bind(this));

            /*
            *    Show this component
            */
            EventBus.$on('show-database', function(name) {
                this.showComponent();
            }.bind(this));
        },

        watch: {
            getDatabase() {
                this.showComponent();
            }
        }
    }
</script>
