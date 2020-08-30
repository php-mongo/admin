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

    div.collection-export-container {
        position: fixed;
        z-index: 999999;
        left: 10vw;
        right: 0;
        top: 0;

        div.collection-export {
            background: $white;
            box-shadow: 0 0 4px 0 rgba(0,0,0,0.12), 0 4px 4px 0 rgba(0,0,0,0.24);
            border-left: 5px solid $orange;
            border-right: 5px solid $orange;
            color: $noticeColor;
            font-family: "Lato", sans-serif;
            font-size: 16px;
            line-height: 60px;
            margin: auto auto auto auto;
            max-height: 100vh;
            max-width: 800px;
            min-height: 50vh;
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
                    max-width: 37vw;
                    overflow: hidden;
                    padding: 2px 5px;
                    position: absolute;
                    top: 4px;

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

            p {
                label {
                    input {
                        margin-left: 5px;
                        vertical-align: baseline;
                    }
                }
                &.file-select {
                    border-bottom: solid 1px $darkBlue;
                    margin-bottom: 20px;
                }
            }

            ul {
                list-style: none;

                li {
                    background-color: $lightGrey;
                    margin-bottom: 5px;
                    padding: 2px 0 2px 5px;

                    p {
                        padding: 2px 0 3px 5px;

                        input {
                            margin: 0;
                            vertical-align: middle;
                        }

                        label {
                            background-color: $tabColor;
                            margin-left: 5px;
                        }
                    }
                }
            }

            textarea.export-data {
                min-height: 150px;
                width: 100%;
            }
        }
    }
</style>

<template>
    <transition name="slide-in-top">
        <div class="collection-export-container" v-if="show">
            <div class="collection-export">
                <div class="modal-header">
                    <span class="msg" v-show="errorMessage || actionMessage">
                        <span class="error">{{ errorMessage }}</span>
                        <span class="action">{{ actionMessage }}</span>
                    </span>
                    <span class="close u-pull-right" v-on:click="hideComponent">
                        <img src="/img/icon/cross-red.png" />
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
                limit: 75, // limit the status check iterations
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
                console.log(event.target);
                let name             = event.target.name;
                this.form.type = name; //[name] = true;
                this.form.selected   = name;
                this.form.file       = event.target.files[0];
            },

            /*
             *  Send to API
             */
            runImport() {
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
                else if(status === 2) {
                    this.actionMessage = "Import success";
                    if (this.form.useCurrentCollection === true) {
                        let data = {database: this.database, collection: this.collection};
                        this.$store.dispatch('loadCollection', data);
                    }
                }
                else if (status === 3) {
                    let error = this.$store.getters.getErrorData;
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
