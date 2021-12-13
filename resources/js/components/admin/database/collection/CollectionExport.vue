<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      CollectionExport.vue 1002 2/8/21, 12:23 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   CollectionExport.vue
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
    <transition name="slide-in-top">
        <div id="panel-modal-export" class="panel-modal" v-if="show" v-on:click="closeDialogOutside($event)">
            <div class="panel-modal-inner">
                <div class="modal-header">
                    <span class="msg" v-show="errorMessage || actionMessage">
                        <span class="error">{{ errorMessage }}</span>
                        <span class="action">{{ actionMessage }}</span>
                    </span>
                    <span class="close u-pull-right" v-on:click="hideComponent">
                        <img src="img/icon/cross-red.png" />
                    </span>
                </div>
                <h3 v-text="showLanguage('collection','collectionExport')"></h3>
                <h6><span v-text="showLanguage('collection', 'collections')"></span></h6>
                <ul>
                    <li>
                        <p>
                            <input id="all" type="checkbox" v-on:click="checkAll" v-model="form.all" >
                            <label for="all" class="label" v-text="showLanguage('collection', 'all')"></label>
                        </p>
                    </li>
                    <li v-for="(collection, index) in this.collections" :key="index" v-bind:collection="collection">
                        <p>
                            <input :id="'name_' + index" type="checkbox" v-model="form.collections" :value="collection.collection.name" >
                            <label :for="'name_' + index" class="label">{{ collection.collection.name }}</label>
                        </p>
                    </li>
                </ul>
                <p>
                    <label>
                        <input type="checkbox" v-model="form.download" >
                        <span v-text="showLanguage('collection', 'download')"></span>
                    </label>
                </p>
                <p v-if="form.all === false">
                    <label>
                        <input type="checkbox" v-model="form.json" >
                        <span v-text="showLanguage('collection', 'exportJson')"></span>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="checkbox" v-model="form.gzip" >
                        <span v-text="showLanguage('collection', 'compress')"></span>
                    </label>
                </p>
                <p>
                    <button class="button" v-text="showLanguage('collection', 'export')" v-on:click="runExport"></button>
                    <button class="button warning" v-on:click="hideComponent" v-text="showLanguage('global', 'cancel')"></button>
                </p>
                <div v-if="exportData" style="margin-top: 20px;">
                    <textarea ref="export" v-model="exportData" class="export-data"></textarea>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    /*
     * Imports the Event Bus to pass events on tag updates
     */
    import { EventBus } from '../../../../event-bus.js';

    export default {
        /*
         *  Defines the data required by this component.
         */
        data() {
            return {
                actionMessage: null,
                collections: null,
                collection: null,
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
                }
            }
        },

        methods: {
            /*
             *   Calls the Translation and Language service
             */
            showLanguage( context, key, str ) {
                if (str) {
                    let string = this.$store.getters.getLanguageString( context, key );
                    return string.replace("%s", str)
                }
                return this.$store.getters.getLanguageString( context, key )
            },

            /*
             *  Check all or reset to default
             */
            checkAll() {
                setTimeout( () => {
                    if (this.form.all === false) {
                        this.form.collections = [];
                        this.form.collections.push(this.collection)
                    }
                    if (this.form.all === true) {
                        this.form.json = false;
                        this.form.collections = [];
                        this.collections.forEach( (collection, index) => {
                            this.form.collections.push(collection.collection.name)
                        })
                    }
                }, 500)
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
             */
            setData(data) {
                this.clearData();
                this.database    = data.db;
                this.collection  = data.coll;
                this.collections = this.$store.getters.getCollections;
                this.form.collections.push(data.coll)
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
            runExport() {
                if (this.validate()) {
                    let data = { database: this.database, params: this.form };
                    this.$store .dispatch('exportCollection', data);
                    this.handleExport()
                }
            },

            /*
             *  Monitor and handle the export status
             */
            handleExport() {
                let status = this.$store.getters.getExportCollectionStatus;
                if (status === 1 && this.index < this.limit) {
                    setTimeout(() => {
                        this.handleExport()
                    },100)
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
                            this.$jqf(this.$refs.export).css('height', height + 'px')
                        }, 250)
                    }
                }
                else if (status === 3) {
                    this.errorMessage = "An error occurred during export"
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
                this.clearData();
                this.show = false
            },

            /*
             * Close on click outside panel modal
             */
            closeDialogOutside( event ) {
                if ($(event.target).is('#panel-modal-export')) {
                    this.hideComponent()
                }
            }
        },

        /*
         *  Sets up the component when mounted.
         */
        mounted() {
            /*
             * On event, show the collection export modal
             */
            EventBus.$on('show-document-export', ( data ) => {
                this.setData(data);
                this.showComponent()
            });
        }
    }
</script>
