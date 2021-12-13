<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Export.vue 1001 28/9/20, 10:14 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Export.vue
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
   .panel-form {
       max-width: 767px;

       .collection-title {
           font-size: 1.2rem;
           padding-left: 10px;

           .collection-all {
               padding-right: 10px !important;
           }
       }
   }
</style>

<template>
    <div id="pma-export" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('export', 'title')"></h3>
        </div>
        <div class="header">
            <p class="msg" v-show="errorMessage || actionMessage">
                <span class="error">{{ errorMessage }}</span>
                <span class="action">{{ actionMessage }}</span>
            </p>
        </div>
        <form class="panel-form">
            <div style="padding-top: 20px;" ref="collection">
                <h3 class="collection-title">
                    <span v-text="showLanguage('export', 'collections')"></span>
                    [<label class="collection-all">
                        <span v-text="showLanguage('export', 'all')"></span> <input type="checkbox" v-on:click="checkAll()" v-model="form.all">
                    </label>]
                </h3>
                <ul class="list">
                    <li v-for="(coll, index) in collections" :key="index" v-bind:coll="coll">
                        <label :for="'index_' + index">
                            <input :id="'index_' + index" type="checkbox" @change="checkCollection" v-model="form.collections" :value="coll.collection.name">
                            {{ coll.collection.name }}
                        </label>
                    </li>
                </ul>
                <div class="clear"></div>
            </div>
            <div>
                <hr />
                <p>
                    <label>
                        <input type="checkbox" v-model="form.download" >
                        <span v-text="showLanguage('export', 'download')"></span>
                    </label>
                </p>
                <p v-if="form.all === false && form.collections.length <= 1">
                    <label>
                        <input type="checkbox" v-model="form.json" >
                        <span v-text="showLanguage('export', 'exportJson')"></span>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="checkbox" v-model="form.gzip" >
                        <span v-text="showLanguage('export', 'compress')"></span>
                    </label>
                </p>
                <br>
            </div>
            <div>
                <button class="button pl135" v-on:click="runExport($event)" v-text="showLanguage('export', 'export')"></button>
                <p>&nbsp;</p>
            </div>
        </form>
        <div v-if="exportData" style="margin-top: 20px;">
            <textarea ref="export" v-model="exportData" class="export-data"></textarea>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        name: "Export",

        /*
         *  Component data container
         */
        data() {
            return {
                actionMessage: null,
                collections: null,
                collection: null,
                data: null,
                database: null,
                errorMessage: null,
                exportData: null,
                index: 0,
                limit: 55, // limit the status check iterations
                show: false,
                form: {
                    all: false,
                    collections: [],
                    download: true,
                    gzip: false,
                    json: false
                },
                results: null,
                key: null,
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
             *  Check all or reset to default
             */
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
                    }
                }, 500);
            },

            /*
             *  Clear the fuel injectors
             */
            clearData() {
                this.collections = [];
                this.exportData  = null;
                this.form = {
                    all: false,
                    collections: [],
                    download: true,
                    gzip: false,
                    json: false
                }
            },

            /*
             *  Set the component data on call
             *  This method is from the Component / Export view - not required here
             */
            /*setData(data) {
                this.clearData();
                this.database    = data.db;
                this.collection  = data.coll;
                this.collections = this.$store.getters.getCollections;
                this.form.collections.push(data.coll);
            },*/

            checkCollection() {
                if (this.form.collections.length >= 1)  {
                    this.$jqf(this.$refs.collection).replace(["has-error", "success"]);
                    this.errorMessage = '';
                }
            },

            /*
            *   The database will already be loaded, therefore we should be able to retrieve data when 'show' is triggered'
            */
            getDatabase() {
                this.clearData();
                this.data = this.$store.getters.getDatabase;
                if (this.data) {
                    this.collections = this.data.collections;
                    this.database = this.data.db.databaseName;
                }
            },

            /*
             *  Ensure the database is set and at least 1 collection selected
             */
            validate() {
                if (this.form.collections.length === 0) {
                    this.$jqf(this.$refs.collection).replace(["success", "has-error"]);
                    this.errorMessage = this.showLanguage('errors', 'export.collectionsError');
                    return false
                }
                if (!this.database) {
                    if (!this.data.db) {
                        this.errorMessage = this.showLanguage('errors', 'export.databaseError');
                        return false
                    }
                    this.database = this.data.db.databaseName
                }
                return true
            },

            /*
             *  Send to API
             */
            runExport(event) {
                event.preventDefault();
                if (this.validate()) {
                    let data = { database: this.database, params: this.form };
                    this.$store .dispatch('exportCollection', data);
                    this.handleExport();
                }
            },

            /*
             *  Monitor and handle the export status
             */
            handleExport() {
                let status = this.$store.getters.getExportCollectionStatus;
                if (status === 1 && this.index < this.limit) {
                    setTimeout(() => {
                        this.handleExport();
                    },100);
                }
                else if(status === 2) {
                    if (this.form.download === true) {
                        this.actionMessage = "Download success";

                    } else {
                        this.exportData    = this.$store.getters.getExportData;
                        this.actionMessage = "Export complete: please copy your export data from the textarea field";
                        let arr    = this.exportData.split('\n');
                        let rows   = parseInt(arr.length + 4);
                        let height = parseInt(rows * 25);
                        setTimeout(() => {
                            this.$jqf(this.$refs.export).css('height', height + 'px');
                        }, 250);
                    }
                }
                else if (status === 3) {
                    this.errorMessage = "An error occurred during export";
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
            EventBus.$on('hide-database-panels',() => {
                this.hideComponent();
            });

            /*
             *    Show this component
             */
            EventBus.$on('show-database-export', () => {
                this.showComponent();
                this.getDatabase();
            });
        },
    }
</script>
