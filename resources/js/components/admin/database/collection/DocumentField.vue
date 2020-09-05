<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DocumentUpdate.vue 1001 8/8/20, 10:23 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DocumentUpdate.vue
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
                <h3 v-text="showLanguage('document','documentNew')"></h3>
                <form>
                    <label>
                        <span v-text="showLanguage('document','type')"></span>:
                        <select v-model="form.type" v-on:change="switchType($event)">
                            <option value="boolean" v-text="showLanguage('document', 'boolean')"></option>
                            <option value="double" v-text="showLanguage('document', 'double')"></option>
                            <option value="integer" v-text="showLanguage('document', 'integer')"></option>
                            <option value="long" v-text="showLanguage('document', 'long')"></option>
                            <option value="mixed" v-text="showLanguage('document', 'mixed')"></option>
                            <option value="null" v-text="showLanguage('document', 'null')"></option>
                            <option value="string" v-text="showLanguage('document', 'string')"></option>
                        </select>
                    </label>
                    <label>
                        <span>_id</span>:
                        <input type="text" v-model="form._id" readonly>
                    </label>
                    <label ref="field">
                        <span v-text="showLanguage('document','field', false)"></span>:
                        <input type="text" v-model="form.field">
                    </label>
                    <label>
                        <span v-text="showLanguage('document','exists', false)"></span>
                        <input type="checkbox" v-model="form.exists">
                    </label>
                    <label ref="value">
                        <span v-text="showLanguage('document','value', false)"></span>:
                        <textarea rows="7" v-model="form.value" v-show="form.type==='string'"></textarea>
                        <textarea rows="7" v-model="form.value" v-show="form.type==='mixed'">{\n\n}</textarea>
                        <input type="text" v-model="form.value" v-show="form.type==='integer' || form.type==='long' || form.type==='double'">
                        <select v-model="form.value" v-show="form.type==='boolean'">
                            <option value="true" v-text="showLanguage('document','true')"></option>
                            <option value="false" v-text="showLanguage('document','false')"></option>
                        </select>
                        <span v-text="showLanguage('document','nullIsSet',false)" v-show="form.type==='null'"></span>
                    </label>
                    <p>&nbsp;</p>
                    <p>
                        <span v-show="apply.both" class="save button" v-on:click="saveField('apply')" v-text="showLanguage('document', 'apply')"></span>
                        <span class="save button" v-on:click="saveField('apply-all')" v-text="showLanguage('document', 'applyAll')"></span>
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
                action: 'apply',
                actionMessage: null,
                apply: {
                  both: true
                },
                confirmed: null,
                document: {},
                documents: [],
                errorMessage: null,
                errors: 0,
                form: {
                    collection: null,
                    database: null,
                    _id: null,
                    field: null,
                    index: null,
                    exists: false,
                    type: 'string',
                    value: null,
                    document: null
                },
                invalid: [],
                newDocument: false,
                show: false,
                total: 0,
                types: [
                    'boolean',
                    'double',
                    'integer',
                    'long',
                    'mixed',
                    'null',
                    'string'
                ],
                updated: 0
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
             *  Set the current document for editing - received from the EventBus
             *  This is primarily used when adding a new field to a single document
             *  When the 'Apply all' is triggered we will iterate through the full doc array
             */
            setDocument( data ) {
                this.form.database   = data.db;
                this.form.collection = data.coll;
                this.form.index      = data.index;
                if (data.document) {
                    // save requirements
                    this.form._id = data.document._id;
                    // document._id  = null;
                    this.document = data.document;

                } else {
                    // if no document is passed this.index is null
                    if (this.index == null) {
                        this.apply.both = false;
                    }
                }
            },

            /*
             *  Switch the document view format
             */
            switchType(event) {
                let type = this.$jqf(event.target).value();
                console.log('value: ' + type);
                this.form.type = type;
                if (type === 'null') {
                    this.form.value = null;
                }
                else if (type === 'boolean') {
                    this.form.value = 'true';
                }
                else if (type === 'mixed') {
                    this.form.value = '{\n\n}'
                }
                else {
                    this.form.value='';
                }
            },

            /*
             *  This is our method that handles the save field click
             */
            saveField( action ) {
                this.action = action;
                console.log( "saving action: " + this.action );

                if (this.validate()) {
                    if (action === 'apply-all') {
                        if (this.newDocument === true) {
                            this.createNewDocument();
                            return;
                        }
                        this.saveAllDocuments();

                    } else {
                        // add field and check overwrite
                        if ( this.addFieldToDocument( this.document) ) {
                            this.saveDocument();
                        }
                    }
                }
            },

            createNewDocument() {
                let document = '{"' + this.form.field + '":"' + this.form.value + '"}';
             //   console.log(document);
                let form = {
                    collection: this.form.collection,
                    database: this.form.database,
                    format: 'json',
                    number: 1,
                    document: document
                };
             //   console.log(form);
             //   return;
                // send
                this.$store.dispatch( 'createDocument', form );
                this.handleNew();
            },

            /*
             *  Handle the create new scenario
             */
            handleNew() {
                let status = this.$store.getters.getCreateDocumentStatus;
                if (status === 1) {
                    let self = this;
                    setTimeout(function() {
                        self.handleNew();
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

            validate() {
                if (this.form.field == null)  {
                    this.errorMessage = this.showLanguage('document', 'validate.fieldNameEmpty');
                    this.invalid.push('field');
                    this.$jqf(this.$refs.field).add('is-invalid');
                    return false;
                }
                return true;
            },

            reset() {
                this.errorMessage = null;
                this.$jqf(this.$refs.field).remove('is-invalid');
                this.$jqf(this.$refs.value).remove('is-invalid')
            },

            /*
             *  Handles adding the new fields data to the document that we need to save
             */
            addFieldToDocument( document ) {
                // if Overwrite existing is false and key exists exhibit a warning !!
                if (document[this.form.field] && this.form.exists === false) {
                    this.errorMessage = 'Field already exists in document. Confirm overwrite existing';
                    return false;
                }
                if (this.form.type === 'mixed') {
                    this.document[this.form.field] = JSON.parse(this.$convObj(this.form.value).minify(this.form.value));

                } else if (this.form.type === 'null') {
                    this.document[this.form.field] = null;
                    return true;

                } else {
                    this.document[this.form.field] = this.$convObj(this.form.value).minify(this.form.value);
                }
                // cleanup the value
                this.form.value = this.$convObj(this.form.value).minify(this.form.value);
                return true;
            },

            /*
             *  Save field to current doc on;ly
             */
            saveDocument() {
                // clear _id
                this.document._id = null;

                // convert to json and add to form
                this.form.document = this.$convObj( this.document ).json();

                // clear _id from string
                this.form.document = this.form.document.replace('"_id":null,"', "");

                // send
                this.$store.dispatch( 'updateDocument', this.form );

                // result
                this.handleUpdate();
            },

            /*
             *  Run the save field to all document iteration
             */
            saveAllDocuments() {
                // get all documents from current collection
                this.documents = this.$store.getters.getDocuments;

                // iterate docs
                //let i = 0;
                this.documents.forEach((document, index) => {
                    // set index
                    this.form.index = index;

                    // get the _id and add to form
                    this.form._id = document.raw._id;

                    // add the raw document object for processing
                    this.document = document.raw;

                    // clear the _id
                    this.document._id = null;

                    // use common method to update the document
                    if (this.addFieldToDocument( this.document)) {
                        // convert to json and add to form
                        this.form.document = this.$convObj( this.document ).json();

                        // replace _id:null
                        this.form.document = this.form.document.replace('"_id":null,"', "");

                        // add id to form
                        this.form._id = document._id;

                        // send
                        this.$store.dispatch( 'updateDocument', this.form );

                        // update stored docs
                        EventBus.$emit('document-updated', { index: this.form.index, document: this.form.document });

                        // used to monitor send success/fail
                        if (this.handleAll()) {
                            // notify doc update success
                            this.updated += 1;
                            this.actionMessage = this.showLanguage('document', 'updated', this.updated);
                        }
                        else {
                            // track errors
                            this.errors += 1;
                            this.errorMessage = this.showLanguage('document', 'errors', this.errors);
                        }
                    }
                    // track total number
                    this.total += 1;
                  //  i+=1;
                });

                // complete
                this.handleUpdate();
            },

            /*
             *  Handle a single document update or completion of 'Apply all'
             */
            handleUpdate() {
                let status = this.$store.getters.getUpdateDocumentStatus;
                if (status === 1) {
                    let self = this;
                    setTimeout(function() {
                        self.handleUpdate();
                    }, 100);
                }
                if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('document', 'updateSuccess', this.form._id) });
                    if (this.action === 'apply') {
                        EventBus.$emit('document-updated', { index: this.form.index, document: this.form.document });
                    }
                    this.clearData();
                    this.hideComponent();
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('document', 'updateError', this.form._id) });
                }
            },

            /*
             *  use this when iterating through the full document array to monitor progress
             */
            handleAll() {
                let status = this.$store.getters.getUpdateDocumentStatus;
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
                this.action = 'apply';
                this.actionMessage = null;
                this.document = {};
                this.errorMessage = null;
                this.errors = 0;
                this.updated = 0;
                this.form =  {
                    collection: null,
                    database: null,
                    _id: null,
                    field: null,
                    index: null,
                    exists: true,
                    type: 'string',
                    value: null,
                    document: null
                };
            },

            /*
             *  This method help sort out changes in context for this component
             */
            checkDocumentCount() {
                this.newDocument = false;
                this.documents   = this.$store.getters.getDocuments;
             // this.$jqf().isEmpty(this.documents)
                if (this.documents.length === 0) {
                    // this infirm the user that a NEW document will have to be created in order to add a field
                    this.errorMessage = this.showLanguage('document', 'noDocuments');
                    this.newDocument  = true;
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

        /*s
          Sets up the component when mounted.
        */
        mounted() {
            /*
             * On event, show the new document field modal
             */
            EventBus.$on('show-document-field', ( data ) => {
                this.clearData();
                this.setDocument( data );
                this.showComponent();
                this.checkDocumentCount();
            });
        },

        destroyed() {
            this.clearData();
        }
    }
</script>
