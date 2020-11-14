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
    .modal-indexes {
        input {
            display: inline-block;
            margin: 0 10px 0 0;
        }

        ul {
            margin: 0;

            li {
                background-color: $lightGrey !important;

                input {
                    width: auto;
                }

                select {
                    display: inline-block;
                    margin: 0;
                    vertical-align: bottom;
                    width: auto;
                }
            }
        }

        p.flds {
            span {
                display: inline-block;
                margin: 0 10px;
            }
        }
    }
</style>

<template>
    <transition name="slide-in-top">
        <div class="panel-modal" v-if="show">
            <div class="panel-modal-inner modal-indexes">
                <div class="modal-header">
                    <span class="msg" v-show="errorMessage || actionMessage">
                        <span class="error">{{ errorMessage }}</span>
                        <span class="action">{{ actionMessage }}</span>
                    </span>
                    <span class="close u-pull-right" v-on:click="hideComponent">
                        <img src="img/icon/cross-red.png" />
                    </span>
                </div>
                <h3 v-text="showLanguage('collection','collectionIndexes')"></h3>
                <p>
                    <button class="button" v-on:click="handleForm('index')" v-text="showLanguage('collection', 'addIndex')"></button>
                    <button class="button" v-on:click="handleForm('2d')" v-text="showLanguage('collection', 'add2dIndex')"></button>
                    <button class="button warning" v-on:click="hideComponent" v-text="showLanguage('global', 'close')"></button>
                </p>
                <div v-if="showIndexForm === true">
                    <form>
                        <p><a target="_blank" href="http://docs.mongodb.org/manual/indexes/">Documentation</a></p>
                        <ul>
                            <li><label for="field-0" v-text="showLanguage('collection', 'fields')"></label></li>
                            <li v-for="(field, index) in indexForm.fields" v-bind:field="field" v-bind:index="index">
                                <p>
                                    <input :id="'field-' + index" type="text" v-model="indexForm.fields[index].field">
                                    <select v-model="indexForm.fields[index].direction">
                                        <option value="ASC">ASC</option>
                                        <option value="DESC">DESC</option>
                                    </select>
                                    <span v-on:click="addIndexField(index)" class="button grey">+</span>
                                    <span v-show="index > 0" v-on:click="removeIndexField(index)" class="button grey">-</span>
                                </p>
                            </li>
                        </ul>
                        <label for="name-1">
                            <span v-text="showLanguage('collection', 'name')"></span>
                            <input id="name-1" type="text" v-model="indexForm.name" :placeholder="showLanguage('collection', 'namePlaceholder')" >
                        </label>
                        <label for="sparse">
                            <span v-text="showLanguage('collection', 'sparse')"></span>
                            <input id="sparse" type="checkbox" v-model="indexForm.sparse" >
                        </label>
                        <label for="unique">
                            <span v-text="showLanguage('collection', 'unique')"></span>
                            <input id="unique" type="checkbox" v-model="indexForm.unique" >
                        </label>
                        <p>
                            <button class="button" v-on:click="saveIndex" v-text="showLanguage('collection', 'createIndex')"></button>
                            <button class="button warning" v-on:click="handleForm('index')" v-text="showLanguage('global', 'cancel')"></button>
                        </p>
                    </form>
                </div>
                <div v-if="show2dIndexForm === true">
                    <form>
                        <p><a target="_blank" href="http://docs.mongodb.org/manual/core/2d">Documentation</a></p>
                        <label for="location">
                            <span v-text="showLanguage('collection', 'locationField')"></span>
                            <input id="location" type="text" v-model="index2dForm.locationField" >
                        </label>
                        <ul>
                            <li><label for="2dfield-0" v-text="showLanguage('collection', 'otherFields')"></label></li>
                            <li v-for="(field, index) in indexForm.fields" v-bind:field="field" v-bind:index="index">
                                <p>
                                    <input :id="'2dfield-' + index" type="text" v-model="indexForm.fields[index].field">
                                    <select v-model="indexForm.fields[index].direction">
                                        <option value="ASC">ASC</option>
                                        <option value="DESC">DESC</option>
                                    </select>
                                    <span v-on:click="add2dIndexField(index)" class="button grey">+</span>
                                    <span v-show="index > 0" v-on:click="remove2dIndexField(index)" class="button grey">-</span>
                                </p>
                            </li>
                        </ul>
                        <label for="name-2">
                            <span v-text="showLanguage('collection', 'name')"></span>
                            <input id="name-2" type="text" v-model="index2dForm.name"  :placeholder="showLanguage('collection', 'namePlaceholder')" >
                        </label>
                        <label for="min">
                            <span v-text="showLanguage('collection', 'minBound')"></span>
                            <p class="flds">
                                <input id="min" class="width-150" type="text" v-model="index2dForm.minBound" >
                                * <span v-text="showLanguage('collection', 'default')"></span> -180
                            </p>
                        </label>
                        <label for="max">
                            <span v-text="showLanguage('collection', 'maxBound')"></span>
                            <p class="flds">
                                <input id="max" class="width-150" type="text" v-model="index2dForm.maxBound" >
                                * <span v-text="showLanguage('collection', 'default')"></span> 180
                            </p>
                        </label>
                        <label for="precision">
                            <span v-text="showLanguage('collection', 'bitPrecision')"></span>
                            <p class="flds">
                                <input id="precision" class="width-150" type="text" v-model="index2dForm.bitPrecision" >
                                * <span v-text="showLanguage('collection', 'default')"></span> 26
                            </p>
                        </label>
                        <p>
                            This was planned for backward compatibility: it may not be fully implemented for Beta release
                            <!--<button class="button" v-on:click="saveIndex" v-text="showLanguage('collection', 'createIndex')"></button>-->
                            <button class="button warning" v-on:click="handleForm('2d')" v-text="showLanguage('collection', 'cancel')"></button>
                        </p>
                    </form>
                </div>
                <table class="table bordered">
                    <thead>
                        <tr>
                            <th v-text="showLanguage('collection', 'version')"></th>
                            <th v-text="showLanguage('collection', 'name')"></th>
                            <th v-text="showLanguage('collection', 'key')"></th>
                            <th v-text="showLanguage('collection', 'namespace')"></th>
                            <th v-text="showLanguage('collection', 'unique')"></th>
                            <th v-text="showLanguage('collection', 'operation')"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="index in indexes" v-bind:index="index">
                            <td class="rb">{{ index.version }}</td>
                            <td class="rb">{{ index.name }}</td>
                            <td class="rb">{{ index.key }}</td>
                            <td class="rb">{{ index.ns }}</td>
                            <td class="rb">{{ index.unique }}</td>
                            <td class="rb">{{ index.operation }}</td>
                        </tr>
                    </tbody>
                </table>
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
                showIndexForm: false,
                indexForm: {
                    create: true,
                    fields: [
                        { field: null, direction: 'ASC', index: 0 }
                    ],
                    name: null,
                    sparse: false,
                    unique: false
                },
                show2dIndexForm: false,
                index2dForm: {
                    create: true,
                    locationField: null,
                    fields: [
                        { field: null, direction: 'ASC', index: 0 }
                    ],
                    name: null,
                    minBound: null,
                    maxBound: null,
                    bitPrecision: null
                },
                index: 0,
                limit: 75, // limit the status check iterations
                show: false,
                indexes: [],
                nindexes: 0,
                statistics: null
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

            handleForm( id ) {
                if (id === '2d') {
                    this.show2dIndexForm = !this.show2dIndexForm;
                }
                if (id === 'index') {
                    this.showIndexForm = !this.showIndexForm;
                }
                this.clear();
            },

            addIndexField(index) {
                this.indexForm.fields.push(
                    { field: null, direction: 'ASC', index: index }
                );
            },

            removeIndexField(index) {
                this.indexForm.fields = this.indexForm.fields.map((field) => {
                    if (field.index !== index) {
                        return field;
                    }
                });
            },

            add2dIndexField(index) {
                this.index2dForm.fields.push(
                    { field: null, direction: 'ASC', index: index }
                );
            },

            remove2dIndexField(index) {
                this.index2dForm.fields = this.index2dForm.fields.map((field) => {
                    if (field.index !== index) {
                        return field;
                    }
                });
            },

            getProperties(data) {
                this.indexes = [];
                this.clear();
                this.database   = data.db;
                this.collection = data.coll;
                this.statistics = this.$store.getters.getCollectionStats;
                this.nindexes   = this.statistics.nindexes;
                let infoObj;
                if (this.nindexes > 0) {
                    let indexDetails = this.statistics.indexDetails;
                    let x;
                    for (x in indexDetails) {
                        if (indexDetails.hasOwnProperty(x)) {
                            infoObj = JSON.parse(indexDetails[ x ].metadata.infoObj);
                            this.indexes.push({
                                version: infoObj.v,
                                name: infoObj.name,
                                key: infoObj.key,
                                ns: infoObj.ns,
                                unique: '',
                                operation: ''
                            });
                        }
                    }
                }
            },

            saveIndex() {
                let form;
                if (this.showIndexForm === true) {
                    form = this.indexForm;
                }
                if (this.show2dIndexForm === true) {
                    form = this.index2dForm;
                }
                if (form.fields[0].field === null) {
                    this.errorMessage = this.showLanguage('collection', 'indexError');
                    return;
                }
                let data = { database: this.database, collection: this.collection, params: form };
                this.$store.dispatch('saveCollectionIndex', data);
                this.handleSaveIndex();
            },

            handleSaveIndex() {
                let status = this.$store.getters.getCollectionIndexStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    let self = this;
                    setTimeout(function() {
                        self.handleSaveIndex();
                    }, 100);
                }
                else if (status === 2) {
                    // success!
                    this.actionMessage = this.showLanguage('collection', 'indexSuccess', this.$store.getters.getCollectionIndex);
                }
                else if (status === 3) {
                    this.errorMessage = this.showLanguage('collection', 'indexError');
                }
            },

            clear() {
                this.actionMessage = null;
                this.errorMessage = null;
                this.indexForm = {
                    create: true,
                    fields: [
                        { field: null, direction: 'ASC', index: 0 }
                    ],
                    name: null,
                    sparse: false,
                    unique: false
                };
                this.index2dForm = {
                    create: true,
                    locationField: null,
                    fields: [
                        { field: null, direction: 'ASC', index: 0 }
                    ],
                    name: null,
                    minBound: null,
                    maxBound: null,
                    bitPrecision: null
                };
                this.index = 0;
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
             * On event, show the collection properties modal
             */
            EventBus.$on('show-document-indexes', ( data ) => {
                this.getProperties(data);
                this.showComponent();
            });
        }
    }
</script>
