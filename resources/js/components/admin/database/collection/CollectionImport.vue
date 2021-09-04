<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      CollectionImport.vue 1002 02/08/21, 12:23 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   CollectionImport.vue.vue
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
        <div id="panel-modal-import" class="panel-modal" v-if="show" v-on:click="closeDialogOutside($event)">
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
                <h3 v-text="showLanguage('collection','collectionImport')"></h3>
                <h6 v-text="showLanguage('collection', 'file')"></h6>
                <p class="file-select">
                    <label>
                        <span v-text="showLanguage('collection', 'fileAdmin')"></span>
                        <input type="file" name="admin" v-on:change="setFile($event)">
                        <input type="radio" name="active" readonly aria-readonly="true" v-model="form.type" value="admin" >
                    </label>
                </p>
                <p class="file-select">
                    <label>
                        <span v-text="showLanguage('collection', 'fileMongo')"></span>
                        <input type="file" name="mongo" v-on:change="setFile($event)" >
                        <input type="radio" name="active" readonly aria-readonly="true" v-model="form.type" value="mongo">
                    </label>
                </p>
                <p>
                    <label>
                        <input type="checkbox" v-model="form.gzip" >
                        <span v-text="showLanguage('collection', 'compressed')"></span>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="checkbox" v-model="form.useCurrentCollection" >
                        <span v-text="showLanguage('collection', 'useCurrent', collection)"></span>
                    </label>
                </p>
                <p>
                    <button class="button" v-text="showLanguage('collection', 'import')" v-on:click="runImport"></button>
                    <button class="button warning" v-on:click="hideComponent" v-text="showLanguage('global', 'cancel')"></button>
                </p>
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
                collection: null,
                database: null,
                errorMessage: null,
                exportData: null,
                index: 0,
                limit: 55, // limit the status check iterations
                show: false,
                form: {
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
            }
        },

        methods: {
            /*
             *   Calls the Translation and Language service
             */
            showLanguage( context, key, str ) {
                if (str) {
                    let string = this.$store.getters.getLanguageString( context, key );
                    return string.replace("%s", str);
                }
                return this.$store.getters.getLanguageString( context, key );
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
            runImport() {
                this.actionMessage = null;
                this.errorMessage  = null;
                let data = {database: this.database, collection: this.collection, params: this.form };
                this.$store .dispatch('importCollection', data);
                this.handleImport();
            },

            handleImport() {
                let status = this.$store.getters.getImportCollectionStatus;
                if (status === 1 && this.index < this.limit) {
                    setTimeout(() => {
                        this.handleImport();
                    },100);
                }
                else if(status === 2) {
                    this.actionMessage = "Import success";
                    if (this.form.useCurrentCollection === true) {
                        let data = {database: this.database, collection: this.collection};
                        this.$store.dispatch('loadCollection', data);
                    }
                }
                else if (status === 3) {
                    let error = this.$store.getters.getCollectionErrorData;
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

            /*
             * Close on click outside panel modal
             */
            closeDialogOutside( event ) {
                if ($(event.target).is('#panel-modal-import')) {
                    this.hideComponent();
                }
            }
        },

        /*
         *  Sets up the component when mounted.
         */
        mounted() {
            /*
             * On event, show the collection import modal
             */
            EventBus.$on('show-document-import', ( data ) => {
                this.setData(data);
                this.showComponent();
            });
        }
    }
</script>
