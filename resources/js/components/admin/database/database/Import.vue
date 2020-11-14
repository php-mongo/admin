<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Import.vue 1001 28/9/20, 10:24 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Import.vue
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
    h4 {
        margin: 10px 0 0 10px;
    }
</style>

<template>
    <div id="pma-database-import" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('import', 'title')"></h3>
        </div>
        <div class="header">
            <p class="msg" v-show="errorMessage || actionMessage">
                <span class="error">{{ errorMessage }}</span>
                <span class="action">{{ actionMessage }}</span>
            </p>
        </div>
        <form class="panel-form">
            <h4 v-text="showLanguage('import', 'file')"></h4>
            <p class="file-select">
                <label>
                    <span v-text="showLanguage('import', 'fileAdmin')"></span>
                    <input type="file" name="admin" v-on:change="setFile($event)">
                    <input type="radio" readonly aria-readonly="true" v-model="form.type" value="admin" >
                </label>
            </p>
            <p class="file-select">
                <label>
                    <span v-text="showLanguage('import', 'fileMongo')"></span>
                    <input type="file" name="mongo" v-on:change="setFile($event)" >
                    <input type="radio" readonly aria-readonly="true" v-model="form.type" value="mongo">
                </label>
            </p>
            <hr />
            <p>
                <label>
                    <input type="checkbox" v-model="form.gzip" >
                    <span v-text="showLanguage('import', 'compressed')"></span>
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" v-model="form.replace" >
                    <span v-text="showLanguage('import', 'replace')"></span>?
                </label>
            </p>
            <p>
                <span v-text="showLanguage('import', 'current', this.database)"></span>
            </p>
            <p>&nbsp;</p>
            <p>
                <button class="button" v-text="showLanguage('import', 'import')" v-on:click="runImport($event)"></button>
            </p>
        </form>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        name: "Import",

        /*
         *  Component data container
         */
        data() {
            return {
                actionMessage: null,
                collection: null,
                database: null,
                errorMessage: null,
                exportData: null,
                index: 0,
                limit: 75, // limit the status check iterations
                show: false,
                form: {
                    file: null,
                    gzip: false,
                    replace: false,
                    selected: null,
                    type: null,
                    /*type: {
                        admin: false,
                        mongo: false
                    },*/
                    useCurrentCollection: false,
                    useCurrentDatabase: true
                }
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
            *   The database will already be loaded, therefore we should be able to retrieve data when 'show' is triggered'
            */
            getDatabase() {
                this.data = this.$store.getters.getDatabase;
                if (this.data) {
                    this.collection = this.data.collections[0].collection.name;
                    this.database = this.data.db.databaseName;
                }
            },

            /*
             *  Set the component data on call
             */
            setData(data) {
                this.database    = data.db;
                this.collection  = data.coll;
            },

            setFile(event) {
                let name             = event.target.name;
                this.form.type = name; //[name] = true;
                this.form.selected   = name;
                this.form.file       = event.target.files[0];
            },

            /*
             *  Send to API
             */
            runImport(event) {
                event.preventDefault();
                this.actionMessage = null;
                this.errorMessage  = null;
                let data = {database: this.database, collection: this.collection, params: this.form };
                this.$store .dispatch('importCollection', data);
                this.handleImport();
            },

            handleImport() {
                let status = this.$store.getters.getImportCollectionStatus;
                if (status === 1 && this.index < this.limit) {
                    let self = this;
                    setTimeout(function() {
                        self.handleImport();
                    },100);
                }
                else if (status === 2) {
                    this.actionMessage = "Import success";
                    if (this.form.useCurrentCollection === true) {
                        let data = {database: this.database, collection: this.collection};
                        this.$store.dispatch('loadCollection', data);
                    }
                }
                else if (status === 3) {
                    let error = this.$store.getters.getCollErrorData;
                    this.errorMessage = error ? error : "An error occurred during import";
                }
            },

            clearForm() {
                this.form = {
                    file: null,
                    gzip: false,
                    selected: null,
                    type: null,
                    /*type: {
                        admin: false,
                        mongo: false
                    },*/
                    useCurrentCollection: true
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
            EventBus.$on('hide-panels', () => {
                this.hideComponent();
            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels', () => {
                this.hideComponent();
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-import', () => {
                this.showComponent();
                this.getDatabase();
            });
        },
    }
</script>
