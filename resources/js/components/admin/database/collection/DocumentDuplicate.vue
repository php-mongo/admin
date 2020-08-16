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
    @import '~@/abstracts/_variables.scss';

    div.document-duplicate-container {
        position: fixed;
        z-index: 999999;
        left: 10vw;
        right: 0;
        top: 0;

        div.document-duplicate {
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

                span.msg {
                    background-color: $offWhite;
                    border-radius: 5px;
                    left: 30px;
                    max-height: 25px;
                    padding: 2px 5px;
                    position: absolute;
                    top: 5px;

                    span.error {
                        color: $red;
                        position: relative;
                        top: -21px;
                    }

                    span.action {
                        color: $green;
                        position: relative;
                        top: -21px;
                    }
                }

                span.close {
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
        <div class="document-new-container" v-show="show">
            <div class="document-new">
                <div class="modal-header"><span class="msg" v-show="errorMessage || actionMessage"><span class="error">{{ errorMessage }}</span> <span class="action">{{ actionMessage }}</span></span><span class="close u-pull-right" v-on:click="hideComponent"><img src="/img/icon/cross-red.png" /></span></div>
                <h3 v-text="showLanguage('document','documentDuplicate')"></h3>
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
                        <input type="number" v-model="form.number">
                    </label>
                    <p>&nbsp;</p>
                    <p>
                        <span class="save button" v-on:click="saveDuplicate()" v-text="showLanguage('document', 'save')"></span>
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
                document: {},
                errorMessage: null,
                errors: 0,
                form: {
                    collection: null,
                    database: null,
                    format: 'json',
                    number: 1,
                    document: null
                },
                show: false,
                skel: {
                    collection: null,
                    database: null,
                    format: 'json',
                    number: 1,
                    document: null
                },
                duplicated: 0
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
             *  Set the current document for duplicating - received from the EventBus
             */
            setDocument( document ) {
                // clear first
                this.clearData();

                // display doc to dupe
                this.document      = document;
                this.document._id  = null;

                // clear the _id from the data and save a copy
                let str = this.$convObj( this.document ).json();
                this.form.document = str.replace("\"_id\":null,", "");
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
             *  This is our method that handles the saveDuplicate click
             */
            saveDuplicate( action ) {
                this.errorMessage = null;

                console.log( "duplicating!" );

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
                    this.$store.dispatch( 'duplicateDocument', this.form );

                    // used to monitor send success/fail
                    if (this.handleAll()) {
                        // notify doc duplicate success
                        this.duplicated += 1;
                        this.actionMessage = this.showLanguage('document', 'duplicated', this.duplicated);
                    }
                    else {
                        // track errors
                        this.errors += 1;
                        this.errorMessage = this.showLanguage('document', 'errorsDuplicate', this.errors);
                    }
                }

                // complete
                this.handleDuplicate();
            },

            /*
             *  Handle a single document duplicates'
             */
            handleDuplicate() {
                let status = this.$store.getters.getDuplicateDocumentStatus;
                if (status === 1) {
                    let self = this;
                    setTimeout(function() {
                        self.handleDuplicate();
                    }, 100);
                }
                if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('document', 'duplicateSuccess', this.duplicated) });
                    this.clearData();
                    this.hideComponent();
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: this.showLanguage('document', 'duplicateError', this.errors) });
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
            EventBus.$on('show-document-duplicate', ( data ) => {
            //    console.log(data);
                this.form.database   = data.db;
                this.form.collection = data.coll;
                this.setDocument( data.document );
                this.showComponent();
            });
        },

        destroyed() {
            this.clearValues();
        }
    }
</script>
