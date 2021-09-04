<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DatabaseView.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DatabaseView.vue
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
    .pma-database-view {
        float: left;
        width: 96%;

        .msg {
            padding: 7px;
            .error {
                color: $errorBorder;
            }
        }

        .database-inner, .new-collection-inner {
            .title {
                background-color: $tableHeaderBg;
                border-bottom: 1px solid $cccColor;
                color: $white;
                margin: 0;
                text-align: center;

                h3 {
                    font-size: 1.2rem;
                    font-weight: 600;
                    padding-top: 0.5rem;
                }
            }

            .top-link {
                margin-top: 15px;
            }

            .panel-form {
                max-width: 40rem;

                textarea {
                    min-height: 90px;
                }

                textarea.export-data {
                    min-height: 150px;
                    width: 100%;
                }

                .has-error {
                    border: 1px solid $red;
                }

                .success {
                    border: 1px solid $green;
                }

                ul {
                    margin: 0;
                    list-style: none;

                    label {
                        padding: 2px 10px;
                    }
                }

                label {
                    display: inline-block;
                    padding-right: 0;
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
            }

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
                border-top: 1px solid $tableHeaderBg;
                border-radius: 5px;
                box-shadow: 2px 2px 5px $cccColor;
            }
            table th {
                background-color: $tableHeaderBg;
                color: $white;
                font-size: 1.2rem;
                padding: 4px;
            }
            table th.title {
                padding: 10px 0;
            }
            table td {
                background-color: $infoBgColor;
                padding: 4px 4px 4px 8px;
                vertical-align: top;

                input {
                    margin: 3px 0 0 0;
                }
            }
            table.bordered td.tr, table.collection td.tr {
                text-align: right;
                width: 50%;
            }
            table.bordered {
                th.bb {
                    border-bottom: 1px solid $cccColor;
                }
                th.rb {
                    border-right: 1px solid $cccColor;
                }
                td {
                    border-bottom: 1px solid $cccColor;
                    text-align: left;
                }
                td.rb, table.collection td.rb {
                    border-right: 1px solid $cccColor;
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

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 768px) and (max-width: 992px) {
        .pma-database-view {
            float: none;
            width: calc(97vw - 260px);
        }
        .database-inner {
            td.vat {
                width: 75% !important;
            }
        }
    }
</style>

<template>
    <div ref="pmaDatabaseView" id="pma-database-view" class="pma-database-view align-left" v-show="show">
        <database-top-view></database-top-view>
        <database-card v-bind:db="getDatabase"></database-card>
        <new-collection></new-collection>
        <commands></commands>
        <execute></execute>
        <transfer></transfer>
        <import></import>
        <export></export>
        <profile></profile>
        <repair></repair>
        <authentication></authentication>
        <drop></drop>
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
    import NewCollection from "./collection/NewCollection";
    import Commands from "./database/Command";
    import Execute from "./database/Execute";
    import Transfer from "./database/Transfer";
    import Import from "./database/Import";
    import Export from "./database/Export";
    import Profile from "./database/Profile";
    import Repair from "./database/Repair";
    import Authentication from "./database/Authentication";
    import Drop from "./database/Drop";

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            DatabaseTopView,
            DatabaseCard,
            NewCollection,
            Commands,
            Execute,
            Transfer,
            Import,
            Export,
            Profile,
            Repair,
            Authentication,
            Drop
        },

        /*
        *   Data required for this component
        */
        data() {
            return {
                show: false,
                database: {},
                expanded: false
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
                if (status === 1  && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.checkNewDatabase();
                    }, 150);
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
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.checkDeleteDatabase();
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

            refreshDb(db) {
                if (db) {
                    this.$store.dispatch( 'loadCachedDatabase', db )
                }
            },

            watchLeftNav() {
                this.expanded = !this.expanded;
                if (this.expanded === true) {
                    this.$jqf(this.$refs.pmaDatabaseView).css('width', '94vw');
                }
                if (this.expanded === false) {
                    this.$jqf(this.$refs.pmaDatabaseView).css('width', 'calc(97vw - 260px)');
                }
            },

            /*
            *   Show component
            */
            showComponent() {
                this.$store.dispatch("setActiveCollection", null);
                EventBus.$emit('show-database-nav');
                this.show = true;
            },

            /*
            *   Hide component
            */
            hideComponent() {
                this.show = false;
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
                this.hideComponent()
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database', (db) =>{
                this.showComponent();
                if (db) {
                    this.refreshDb(db)
                }
            });

            /*
            *   Change panel behavior based on left panel size
            */
            EventBus.$on('collapse-left-nav', () => {
                this.watchLeftNav()
            });
            EventBus.$on('expand-left-nav', () => {
                this.watchLeftNav()
            });
        }
    }
</script>
