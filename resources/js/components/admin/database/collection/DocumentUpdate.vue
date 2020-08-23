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
    @import '~@/abstracts/_variables.scss';

    div.document-update-container {
        position: fixed;
        z-index: 999999;
        left: 10vw;
        right: 0;
        top: 0;

        div.document-update {
            background: $white;
            box-shadow: 0 0 4px 0 rgba(0,0,0,0.12), 0 4px 4px 0 rgba(0,0,0,0.24);
            border-left: 5px solid $orange;
            border-right: 5px solid $orange;
            color: $noticeColor;
            font-family: "Lato", sans-serif;
            font-size: 16px;
            line-height: 60px;
            margin: auto auto auto auto;
            max-height: 90vh;
            max-width: 800px;
            min-height: 50px;
            min-width: 400px;
            overflow-y: auto;
            padding: 0 3rem 3rem 3rem;

            .modal-header {
                background-color: $lightGreyColor;
                height: 33px;
                margin: 0 -3rem 0 -3rem;
                max-width: 790px;
                padding: 0.55rem 20px 0 0;
                position: fixed;
                width: 100%;

                span {
                    cursor: pointer;
                }

                img {
                    vertical-align: top;
                }
            }

            h3 {
                margin-top: 40px;
            }

            label.padd-left {

                label {
                    padding-left: 50px;
                }
            }

            textarea {
                height: auto;
            }
        }
    }
</style>

<template>
    <transition name="slide-in-top">
        <div class="document-update-container" v-show="show">
            <div class="document-update">
                <div class="modal-header"><span class="u-pull-right" v-on:click="hideComponent"><img src="/img/icon/cross-red.png" /></span></div>
                <h3 v-text="showLanguage('document', 'documentUpdate')"></h3>
                <form>
                    <label>
                        <span v-text="showLanguage('document', 'format')"></span>:
                        <select v-model="form.format" v-on:change="switchFormat($event)">
                            <option value="array" v-text="showLanguage('document', 'array')"></option>
                            <option value="json" v-text="showLanguage('document', 'json')"></option>s
                            <option value="fields" v-text="showLanguage('document', 'fields')"></option>
                        </select>
                    </label>
                    <label><span>_id</span>:
                        <input type="text" readonly="readonly" v-model="form._id">
                    </label>
                    <label v-show="form.edit == 'text'">
                        <span v-text="showLanguage('document', 'data')"></span>:
                        <textarea rows="7" v-model="form.document"></textarea>
                    </label>
                    <div v-show="form.edit == 'fields'">
                        <p v-for="field in this.fields" v-html="field"></p>
                    </div>
                    <p>
                        <span class="save button" v-on:click="saveUpdate" v-text="showLanguage('document', 'save')"></span>
                        <span class="cancel button warning" v-on:click="hideComponent" v-text="showLanguage('document', 'cancel')"></span>
                    </p>
                </form>
            </div>
        </div>
    </transition>
</template>

<script>
    /*
      Imports the Event Bus to pass events on tag updates
    */
    import { EventBus } from '../../../../event-bus.js';

    export default {
        /*
         *  Defines the data used by the component.
         *  In the form (field, value, type) are added for compatibility with the DocumentField component -
         *  - allows reuse of the same update process when adding new fields
         */
        data() {
            return {
                document: null,
                fields: [],
                fieldsData: {},
                form: {
                    collection: null,
                    database: null,
                    document: null,
                    edit: 'text',
                    field: null,
                    value: null,
                    type: null,
                    format: 'json',
                    index: null,
                    _id: null,
                },
                show: false,
                skel: {
                    collection: null,
                    database: null,
                    document: null,
                    edit: 'text',
                    field: null,
                    value: null,
                    type: null,
                    format: 'json',
                    index: null,
                    _id: null,
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
             *  get the document for editing
             */
            getDocument() {
                this.form.document = this.$store.getters.getDocument( this.id );
                this.form._id      = this.document.id;
            },

            /*
             *  Set the document for editing - received from view
             * JSON is the default to use that service method first
             */
            setDocument( data ) {
                // save requirements
                this.form.database   = data.db;
                this.form.collection = data.coll;
                this.form.index      = data.index;
                this.form._id        = data.document._id;
                data.document._id    = null;
                this.document        = data.document;

                // clear the _id from the data and save a copy
                //let str = this.$convObj( document ).json();
                //this.document = str.replace("\"_id\":null,", "");

                this.form.document = this.makeJson( data.document );
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
                    this.form.edit     = 'text';
                    this.form.document = this.makeArray();

                }  else if (value === 'json') {
                    console.log('go to json...');
                    this.form.format   = 'json';
                    this.form.edit     = 'text';
                    this.form.document = this.makeJson();

                } else if (value === 'fields') {
                    console.log('go to fields...');
                    this.form.format = 'fields';
                    this.fields      = this.makeFields();
                    this.form.edit   = 'fields';

                } else {
                    console.log("ohh no!!! not you again!!");
                }
            },

            makeJson() {
                return this.$convObj( this.document ).jsonT();
            },

            makeArray() {
                return this.$convObj( this.document ).arrayT();
            },

            makeFields() {
                // create the top level array
                let rows = Object.entries( this.document );

                return rows.map( function( value, index, array) {
                    // we dont want the _id key
                    if (value[0] != '_id') {
                        // If the value is an object - we need to drill deeper
                        // - this will need to be rewritten to be recursive
                        if (typeof value[1] == 'object') {
                            let arr = Object.entries( value[1] );
                            let output = arr.map( function( v, i, a) {
                                return '<label>' + v[0] + '<textarea v-model="fieldsData.' + value[0] + '.' + v[0] + '">' + v[1] + '</textarea></label>';
                            });
                            return '<label class="padd-left"><strong>' + value[0] + '</strong>' + output + '</label>';

                        } else {
                            return '<label>' + value[0] + '<textarea v-model="fieldsData.' + value[0] + '">' + value[1] + '</textarea></label>';
                        }
                    }
                });
            },

            saveUpdate() {
                //console.log("saving: " + this.form);
                if (this.form.format === 'json') {
                    this.sendJson();
                }
                if (this.form.format === 'array') {
                    this.sendArray();
                }
                if (this.form.format === 'fields') {
                    this.sendJson();
                }
            },

            sendJson() {
                // cleanup
                let doc = this.form.document;
                doc     = this.$convObj().minify(doc);
                // console.log(doc);

                // restore
                this.form.document = doc;

                // send
                this.$store.dispatch( 'updateDocument', this.form );

                // result
                this.handleUpdate();

            },

            sendArray() {
                let data = this.form.document;
                data = this.$convObj().arrayToJson(data);
                //console.log(data);
                data = this.$convObj().minify(data);

                // restore
                this.form.document = data;

                // send
                this.$store.dispatch( 'updateDocument', this.form );

                // result
                this.handleUpdate();
            },

            sendFields() {
                let data = this.form.document;
                console.log(data);
            },

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
                    EventBus.$emit('document-updated', { index: this.form.index, document: this.form.document });
                    this.clearData();
                    this.hideComponent();
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('document', 'updateError', this.form._id) });
                }
            },

            clearData() {
                this.document = null;
                this.fields = [];
                this.fieldsData = {};
                this.form = this.skel;
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
        mounted(){
            /*
              On event show the document update modal
            */
            EventBus.$on('show-document-update', ( data ) => {
                this.setDocument( data );
                this.showComponent();
            });
        }
    }
</script>
