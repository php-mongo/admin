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
                limit: 75, // limit the status check iterations
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
                    return string.replace("%s", str);
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
                        this.form.collections.push(this.collection);
                    }
                    if (this.form.all === true) {
                        this.form.json = false;
                        this.form.collections = [];
                        this.collections.forEach( (collection, index) => {
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
             */
            setData(data) {
                this.clearData();
                this.database    = data.db;
                this.collection  = data.coll;
                this.collections = this.$store.getters.getCollections;
                this.form.collections.push(data.coll);
            },

            /*
             *  Send to API
             */
            runExport() {
                let data = {database: this.database, params: this.form };
                this.$store .dispatch('exportCollection', data);
                this.handleExport();
            },

            handleExport() {
                let status = this.$store.getters.getExportCollectionStatus;
                if (status === 1 && this.index < this.limit) {
                    let self = this;
                    setTimeout(function() {
                        self.handleExport();
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
                        let self = this;
                        setTimeout(function() {
                            self.$jqf(self.$refs.export).css('height', height + 'px');
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
                this.showComponent();
            });
        }
    }
</script>
