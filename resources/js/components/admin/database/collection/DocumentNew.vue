<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DocumentDuplicate.vue 1001 8/8/20, 10:23 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DocumentDuplicate.vue
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
        <div class="panel-modal" v-show="show">
            <div class="panel-modal-inner">
                <div class="modal-header"><span class="msg" v-show="errorMessage || actionMessage"><span class="error">{{ errorMessage }}</span> <span class="action">{{ actionMessage }}</span></span><span class="close u-pull-right" v-on:click="hideComponent"><img src="/img/icon/cross-red.png" /></span></div>
                <h3 v-text="showLanguage('document','documentCreate')"></h3>
                <form>
                    <label>
                        <span v-text="showLanguage('document','format')"></span>:
                        <select v-model="form.format" v-on:change="switchFormat($event)">
                            <option value="json" v-text="showLanguage('document', 'json')"></option>
                            <option value="array" v-text="showLanguage('document', 'array')"></option>
                        </select>
                    </label>
                    <label>
                        <span v-text="showLanguage('document','document')"></span>:
                        <textarea rows="7" v-model="form.document"></textarea>
                    </label>
                    <label>
                        <span v-text="showLanguage('document','number')"></span>:
                        <input type="number" min="1" v-model="form.number" required>
                    </label>
                    <p>&nbsp;</p>
                    <p>
                        <span class="save button" v-on:click="insertDocument()" v-text="showLanguage('document', 'save')"></span>
                        <span class="cancel button warning" v-on:click="hideComponent" v-text="showLanguage('document', 'cancel')"></span>
                    </p>
                </form>
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
                created: 0,
                document: {},
                errorMessage: null,
                errors: 0,
                form: {
                    collection: null,
                    database: null,
                    format: 'json',
                    number: 1,
                    document: '{\n\t\n}'
                },
                show: false,
                skel: {
                    collection: null,
                    database: null,
                    format: 'json',
                    number: 1,
                    document: '{\n\t\n}'
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
             *  Switch the document view format
             */
            switchFormat(event) {
                let value = this.$jqf(event.target).value();
                console.log('value: ' + value);
                if (value === 'array') {
                    console.log('go to array...');
                    this.form.format   = 'array';
                    this.form.document = this.makeArray( this.form.document );

                }  else if (value === 'json') {
                    console.log('go to json...');
                    this.form.format   = 'json';
                    this.form.document = this.makeJson( this.form.document );

                } else {
                    console.log("ohh no!!! not you again!!");
                }
            },

            makeJson( document ) {
                return this.$convObj( document ).jsonT();
            },

            makeArray( document ) {
                return this.$convObj( document ).arrayT();
            },

            /*
             *  This is our method that handles the insertDocument click
             */
            insertDocument( action ) {
                this.errorMessage = null;

                console.log( "creating!" );

                // check format
                if (this.form.format === 'array') {
                    this.form.document = this.makeJson( this.form.document );
                }

                // save duplicate
                this.saveDocument();
            },

            /*
             *  Run the save field to all document iteration
             */
            saveDocument() {
                // iterate docs
                let i;
                for (i = 0; i < this.form.number; i += 1) {
                    // minify
                    this.form.document = this.$convObj(this.form.document).minify();

                    // send
                    this.$store.dispatch( 'createDocument', this.form );

                    // used to monitor send success/fail
                    if (this.handleAll()) {
                        // notify doc duplicate success
                        this.created += 1;
                        this.actionMessage = this.showLanguage('document', 'created', this.duplicated);
                    }
                    else {
                        // track errors
                        this.errors += 1;
                        this.errorMessage = this.showLanguage('document', 'errorsCreate', this.errors);
                    }
                }

                // complete
                this.handleDuplicate();
            },

            /*
             *  Handle a single document duplicates'
             */
            handleDuplicate() {
                let status = this.$store.getters.getCreateDocumentStatus;
                if (status === 1) {
                    let self = this;
                    setTimeout(function() {
                        self.handleDuplicate();
                    }, 100);
                }
                if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('document', 'createSuccess', this.created) });
                    EventBus.$emit('document-inserted');
                    this.clearData();
                    this.hideComponent();
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('document', 'createError', this.errors) });
                }
            },

            /*
             *  use this when iterating through the full document array to monitor progress
             */
            handleAll() {
                let status = this.$store.getters.getDuplicateDocumentStatus;
                if (status === 1) {
                    let self = this;
                    setTimeout(function() {
                        self.handleAll();
                    }, 100);
                }
                if (status === 2) {
                    return true;
                }
                if (status === 3) {
                    return false;
                }
            },

            /*
             *  Clear on destroyed and completion
             */
            clearData() {
                this.actionMessage = null;
                this.document = {};
                this.errorMessage = null;
                this.errors = 0;
                this.form = this.skel;
                this.updated = 0;
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
          Sets up the component when mounted.
        */
        mounted() {
            /*
             * On event, show the new document field modal
             */
            EventBus.$on('show-document-new', ( data ) => {
                this.form.database   = data.db;
                this.form.collection = data.coll;
            //    this.setDocument();
                this.showComponent();
            });
        },

        destroyed() {
            this.clearData();
        }
    }
</script>
