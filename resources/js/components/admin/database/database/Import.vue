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
    .database-inner {
        .panel-form {
            h4 {
                margin: 10px 0 0 10px;
            }
            input[type="radio"], input[type="checkbox"] {
                vertical-align: middle !important;
                margin-right: 5px !important;
            }
            .file-select {
                margin-right: 5px !important;
            }
        }
    }
</style>

<template>
    <div id="pma-database-import" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('import', 'title')"></h3>
        </div>
        <div class="header">
            <p class="msg" v-show="errorMessage || actionMessage">
                <span class="action">{{ actionMessage }}</span>
                <br><span class="error">{{ errorMessage }}</span>
            </p>
        </div>
        <form class="panel-form">
            <h4 v-text="showLanguage('import', 'file')"></h4>
            <p class="file-select">
                <label>
                    <input type="radio" disabled aria-disabled="true" v-model="form.type" value="admin">
                    <span v-text="showLanguage('import', 'fileAdmin')"></span>
                    <input type="file" name="admin" v-on:change="setFile($event)">
                </label>
            </p>
            <p class="file-select">
                <label>
                    <input type="radio" disabled aria-disabled="true" v-model="form.type" value="mongo">
                    <span v-text="showLanguage('import', 'fileMongo')"></span>
                    <input type="file" name="mongo" v-on:change="setFile($event)" >
                </label>
            </p>
            <hr />
            <p>
                <label>
                    <input type="checkbox" v-model="form.gzip" >
                    <span v-text="showLanguage('import', 'compressed')"></span>
                </label>
            </p>
            <p v-show="form.type !== 'mongo'">
                <label>
                    <input type="checkbox" v-model="form.replace" >
                    <span v-text="showLanguage('import', 'replace')"></span>?
                </label>
            </p>
            <p v-show="form.type !== 'mongo'">
                <label>
                    <input type="checkbox" v-model="form.useImportCollection">
                    <span v-text="showLanguage('import', 'useImportCollection')"></span>
                </label>
            </p>
            <p v-show="form.type === 'mongo'">
                <label>
                    <span v-text="showLanguage('import', 'useSelectedCollection')"></span>
                    <select v-model="form.collection">
                        <option v-for="collection in data.collections" :value="collection.collection.name">{{ collection.collection.name }}</option>
                    </select>
                </label>
            </p>
            <p>
                <span v-text="showLanguage('import', 'current', this.database)"></span>
            </p>
            <p>
                <span v-text="showLanguage('import', 'default', this.form.collection)"></span>
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
                database: null,
                data: null,
                errorMessage: null,
                exportData: null,
                index: 0,
                limit: 55, // limit the status check iterations
                show: false,
                form: {
                    collection: null,
                    file: null,
                    gzip: false,
                    replace: false,
                    selected: null,
                    type: null,
                    /*type: {
                        admin: false,
                        mongo: false
                    },*/
                    useImportCollection: true,
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
                    return this.$store.getters.getLanguageString( context, key ).replace("%s", str)
                }
                return this.$store.getters.getLanguageString( context, key )
            },

            /*
            *   The database will already be loaded, therefore we should be able to retrieve data when 'show' is triggered'
            */
            getDatabase() {
                this.data = this.$store.getters.getDatabase;
                if (this.data) {
                    this.database = this.data.db.databaseName;
                    this.form.collection = this.data.collections[0].collection.name
                }
            },

            /*
             *  Set the component data on call
             */
            setData(data) {
                this.database   = data.db;
                this.collection = data.coll
            },

            /*
             *  Handle the file change event
             */
            setFile(event) {
                let name            = event.target.name;
                this.form.type      = name;
                this.form.selected  = name;
                this.form.file      = event.target.files[0]
            },

            /*
             *  Simple validation
             */
            validate() {
                if (!this.form.file) {
                    this.errorMessage = this.showLanguage('errors', 'import.file');
                    return false
                }
                if (!this.form.type) {
                    this.errorMessage = this.showLanguage('errors', 'import.type');
                    return false
                }
                if (!this.database) {
                    // try and get the active DB
                    this.database = this.$store.getters.getActiveDatabase;
                    if (!this.database) {
                        // give up!!
                        this.errorMessage = this.showLanguage('errors', 'import.database');
                        return false
                    }
                }
                return true
            },

            /*
             *  Send to API
             */
            runImport(event) {
                event.preventDefault();
                if (this.validate()) {
                    this.actionMessage = null;
                    this.errorMessage  = null;
                    let data = { database: this.data.db.databaseName, collection: this.form.collection, params: this.form };
                    this.$store .dispatch('importCollection', data);
                    this.handleImport()
                }
            },

            handleImport() {
                let status = this.$store.getters.getImportCollectionStatus;
                if (status === 1 && this.index < this.limit) {
                    setTimeout(() => {
                        this.handleImport()
                    },100)
                }
                else if (status === 2) {
                    this.actionMessage = this.showLanguage('import', 'actionSuccess');
                    if (this.form.useImportCollection === false) {
                        // this is less relevant here than in the collection import view
                        let data = { database: this.data.db.databaseName, collection: this.form.collection };
                        this.$store.dispatch('loadCollection', data)
                    }
                }
                else if (status === 3) {
                    this.actionMessage = this.showLanguage('import', 'actionDefault');
                    let error = this.$store.getters.getCollectionErrorData;
                    this.errorMessage = error ? error : this.showLanguage('errors', 'import.default')
                }
            },

            clearForm() {
                this.errorMessage = '';
                this.actionMessage = '';
                this.form = {
                    collection: null,
                    file: null,
                    gzip: false,
                    replace: false,
                    selected: null,
                    type: null,
                    useImportCollection: true,
                    useCurrentDatabase: true
                }
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
            },
        },

        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent()
            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels', () => {
                this.hideComponent()
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-import', () => {
                this.clearForm();
                this.showComponent();
                this.getDatabase()
            });
        },
    }
</script>
