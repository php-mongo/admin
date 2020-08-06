<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      CollectionCard.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   CollectionCard.vue
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

    .collection-inner {
        .criteria {
            height: 160px;
            margin-bottom: 0;
            max-width: 99%;
        }
        .v-top {
            vertical-align: top;
            width: 50%
        }
        .field-orders {
            p {
                height: 40px;
                margin-top: 0;
                input, select {
                    display: inline-block;
                }
                input {
                    width: 200px;
                }
                select {
                    width: 120px;
                }
            }
        }
        .f11 {
            font-size: 11px;
        }
        .page-message {
            font-weight: 600;
        }
        .buttons {
            span, label, input, select {
                display: inline-block;
                width: auto;
            }
            input[type="submit"], input[type="button"] {
                cursor: pointer;
                padding: 5px;
            }
        }
        .fields-control {
            display: none;
        }
        .collection-document {
            border: 2px #ccc solid;
            margin-bottom: 10px;
            min-height: 100px;
            position: relative;

            &:hover {
                background-color: $offWhite;
            }

            .doc-nav {
                border-bottom: 1px #999 solid;
                display: inline-block;
                margin: 0 0 5px 50px;
                padding: 5px 0 0 0;
            }
            .doc-data {
                display: block;
                max-height: 150px;
                overflow-y: hidden;
                padding: 0 0 5px 50px;
                width: 99%;
            }
            .doc-text {
                max-height: 150px;
                overflow-y: auto;
                padding: 0 0 5px 50px;
                width: 99%;
            }
            .doc-right-to-top {
                bottom: 5px;
                position: absolute;
                right: 10px;
            }
        }
    }
</style>

<template>
    <div class="collection-inner" v-show="show && collection">
        <table v-show="!collapsed">
            <tr>
                <td class="v-top">
                    <textarea class="criteria" rows="7" cols="70" v-model="form.criteria[currentFormat]"></textarea><br/>
                    <div id="newObjInput" v-show="rules.modify">
                        <span v-text="showLanguage('collection', 'newObject')"></span>(see <a href="http://www.mongodb.org/display/DOCS/Updating" target="_blank" v-text="showLanguage('collection', 'updating')"></a> operators):<br/>
                        <textarea id="newObj" name="newObj" rows="5" cols="70" v-model="form.newObj"></textarea>
                    </div>
                </td>
                <td class="field-orders v-top">
                    <!-- fields will be used in sorting -->
                    <p>
                        <input type="text" v-model="form.field[0]" />
                        <select v-model="form.order[0]">
                            <option value="asc" :selected="orderDefault[0] === 'asc'" v-text="showLanguage('collection', 'asc')"></option>
                            <option value="desc" :selected="orderDefault[0] === 'desc'" v-text="showLanguage('collection', 'desc')"></option>
                        </select>
                    </p>
                    <p>
                        <input type="text" v-model="form.field[1]" />
                        <select v-model="form.order[1]">
                            <option value="asc" :selected="orderDefault[1] === 'asc'" v-text="showLanguage('collection', 'asc')"></option>
                            <option value="desc" :selected="orderDefault[1] === 'desc'" v-text="showLanguage('collection', 'desc')"></option>
                        </select>
                    </p>
                    <p>
                        <input type="text" v-model="form.field[2]" />
                        <select v-model="form.order[2]">
                            <option value="asc" :selected="orderDefault[2] === 'asc'" v-text="showLanguage('collection', 'asc')"></option>
                            <option value="desc" :selected="orderDefault[2] === 'desc'" v-text="showLanguage('collection', 'desc')"></option>
                        </select>
                    </p>
                    <p>
                        <input type="text" v-model="form.field[3]" />
                        <select v-model="form.order[3]">
                            <option value="asc" :selected="orderDefault[3] === 'asc'" v-text="showLanguage('collection', 'asc')"></option>
                            <option value="desc" :selected="orderDefault[3] === 'desc'" v-text="showLanguage('collection', 'desc')"></option>
                        </select>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="buttons">
                    <!-- query fields and hints -->
                    <span id="fieldsAndHints" v-show="rules.findAll">
                        <span v-if="nativeFields">
                            <span class="pma-link" v-on:onclick="showQueryFields($event)" title="Choose fields to display">
                                <span v-text="showLanguage('collection', 'fields')"></span>(<span id="query_fields_count">{{queryFieldsCount}}</span>)
                                <span class="f11">▼</span>
                            </span>
                            |
                            <span class="pma-link" v-on:onclick="showQueryHints($event)" title="Choose indexes will be used in query">
                                <span v-text="showLanguage('collection', 'hints')"></span>(<span id="query_hints_count">{{queryHintsCount}}</span>)
                                <span class="f11">▼</span>
                            </span>
                            |
                        </span>
                    </span>
                    <!-- end query fields and hints -->
                    <label id="limitLabel" v-show="rules.findAll"><span v-text="showLanguage('collection', 'limit')"></span>: <input type="text" v-model="form.limit" size="5" /> |</label>
                    <span id="pageSetLabel" v-show="rules.findAll">
                        <select title="Rows per Page" v-model="form.pageSize">
                            <page-size-option v-for="(size, index) in pageSizes" :key="index" v-bind:size="size" v-bind:value="pageSizeDefault"></page-size-option>
                        </select>
                        |
                    </span>
                    <span v-text="showLanguage('collection', 'action')"></span>:
                    <select v-model="form.command" v-on:change="changeCommand($event)">
                        <option value="findAll" :selected="commandDefault === 'findAll'" v-text="showLanguage('collection', 'findAll')"></option>
                        <option value="remove" :selected="commandDefault === 'remove'" v-text="showLanguage('collection', 'remove')"></option>
                        <option value="modify" :selected="commandDefault === 'modify'" v-text="showLanguage('collection', 'modify')"></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="buttons">
                    <input type="submit" value="Submit Query" v-on:click="submitQuery" />
                    <input type="button" value="Explain" v-on:click="explainQuery" />
                    <input type="button" value="Clear Conditions" v-on:click="clearForm" />
                    [<a href="http://rockmongo.com/wiki/queryExamples?lang=en_us" target="_blank" v-text="showLanguage('collection', 'queryExamples')"></a>]
                    <span v-if="cost"><span v-text="showLanguage('collection', 'cost')"></span> {{ roundCost }}s</span>
                    <p v-if="message" class="error">{{ message }}</p>
                </td>
            </tr>
        </table>
        <div ref="query-fields-list" class="fields-menu">
            <span class="fields-control"><span class="pma-link" title="Click to close" v-on:click="closeQueryFields()"><img alt="Select" src="/img/icon/accept.png" /></span></span>
            <ul>
                <!-- to do fields list -->
            </ul>
        </div>
        <div ref="query-hints-list" class="fields-menu">
            <span class="fields-control"><span class="pma-link" title="Click to close" v-on:click="closeQueryHints()"><img alt="Select" src="/img/icon/accept.png" /></span></span>
            <ul>
                <!-- to do hints list -->
            </ul>
        </div>
        <div id="records" ref="records">
            <div class="records-header">
                <p class="page-message" v-if="page.find.message">{{ page.find.message }}</p>
                <pagination
                    @pageChanged="pageChanged($event)"
                    v-bind:max-visible-buttons="getMaxButtons"
                    v-bind:total-pages="totalPages"
                    v-bind:total="getTotal"
                    v-bind:limit="getLimit"
                    v-bind:current-page="getCurrentPage"
                    ></pagination>
            </div>
            <document v-for="(document, index) in getDocuments" :key="index" v-bind:index="index" v-bind:document="document" v-bind:collection="getCollection" v-bind:format="currentFormat"></document>
        </div>
    </div>
</template>

<script>
    /*
     * Import the Event bus
     */
    import { EventBus } from '../../../../event-bus.js';

    /*
     *   Import components for the Databases View
     */
    import PageSizeOption from "./PageSizeOption";
    import Document from "./Document";
    import Pagination from "./Pagination";

    export default {
        /*
         *   Register the components to be used by the home page.
         */
        components: {
            PageSizeOption,
            Document,
            Pagination
        },

        /*
         *   The component accepts one db as a property
         */
        props: ['collection'],

        /*
         *   Data housing for our collections
         */
        data() {
            return {
                show: false,
                collapsed: false,
                name: null,
                start: 0,
                showing: 0,
                end: 0,
                current: 1,
                count: 0,
                stats: {},
                allObjects: [],
                visibleObjects:[],
                criteriaDefault: '{\n' +
                    '\t\n' +
                    '}',
                newObjDefault: '{\n' +
                    '\t\'$set\': {\n' +
                    '\t\t//your attributes\n' +
                    '\t}\n' +
                    '}',
                fieldDefault: '_id',
                orderDefault: {
                    0 : 'DESC',
                    1 : 'ASC',
                    2 : 'ASC',
                    3 : 'ASC'
                },
                limitDefault: 0,
                pageSizeDefault: 10,
                commandDefault: 'findAll',
                form : {
                    criteria: {
                        json: '{\n' +
                                    '\t\n' +
                               '}',
                        array: '(\n' +
                                    '\t\n' +
                                ')'
                    },
                    newObj: '{\n' +
                        '\t\'$set\': {\n' +
                        '\t\t//your attributes\n' +
                        '\t}\n' +
                        '}',
                    field: {
                        0 : '_id',
                        1 : null,
                        2 : null,
                        3 : null
                    },
                    order: {
                        0 : 'desc',
                        1 : 'asc',
                        2 : 'asc' ,
                        3 : 'asc'
                    },
                    limit: 0,
                    pageSize: 10,
                    command: 'findAll'
                },
                // format for data entry
                format: 'json',
                rules: {
                    // config: allow the modify command
                    modify: false,
                    findAll: true
                },
                pageSizes: [
                    10, 15, 20, 30, 50, 100, 200
                ],
                maxPageButtonDisplay: 3,
                nativeFields: null,
                queryFields: [],
                queryHints: [],
                cost: '0.000186',
                page: {
                    find: {
                        count: 0,
                        message: null
                    },
                    query: {
                        success: null,
                        message: null
                    }
                },
                message: null
            }
        },

        /*
         * Defines the computed properties on the component.
         */
        computed: {
            totalPages() {
                return Math.ceil(this.count / this.pageSizeDefault);
            },

            getTotal() {
                if (this.collection) {
                    if (this.collection.objects) {
                        if (this.collection.objects.count == 0) {
                            this.page.find.message = 'The collection >> ' + this.collection.collection.collectionName + ' << has no documents';
                            this.clearValues();
                        } else {
                            this.page.find.message = null;
                        }
                        return this.collection.objects.count;
                    }
                }
                return 0;
            },

            getCount() {
                return this.count;
            },

            getCurrentPage() {
               return this.current;
            },

            getLimit() {
                return this.pageSizeDefault;
            },

            getMaxButtons() {
                return this.maxPageButtonDisplay;
            },

            currentFormat() {
                return this.format;
            },

            getCurrentFormat() {
                return this.$store.getters.getCurrentFormat;
            },

            queryFieldsCount() {
                return this.queryFields.length;
            },

            queryHintsCount() {
                return this.queryHints.length;
            },

            getDocuments() {
                if (this.visibleObjects && this.visibleObjects.length >= 1) {
                    return this.visibleObjects;
                }
                return [];
            },

            getCollection() {
                return this.collection.collection;
            },

            getObjects() {
                return this.collection.objects;
            }
        },

        /*
         *   Defined methods for the component
         */
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
             *   Make the byte human readable
             */
            humanReadable(bytes, precision) {
                if (bytes === 0) {
                    return 0;
                }
                if (bytes < 1024) {
                    return bytes + "B";
                }
                if (bytes < 1024 * 1024) {
                    return Math.round(bytes/1024, precision) + "k";
                }
                if (bytes < 1024 * 1024 * 1024) {
                    return Math.round(bytes/1024/1024, precision) + "m";
                }
                if (bytes < 1024 * 1024 * 1024 * 1024) {
                    return Math.round(bytes/1024/1024/1024, precision) + "g";
                }
                return bytes;
            },

            /*
             *  This should respond to a change in the global 'format' setting
             */
            setFormat() {
                let format = this.$store.getters.getCurrentFormat;
                if (format !== this.format) {
                    this.format = format;
                }
            },

            showQueryFields( event ) {
                console.log("query fields...");
                console.log(event);
            },

            showQueryHints( event ) {
                console.log("query hints..");
                console.log(event);
            },

            changeCommand( event ) {
                console.log("change command...");
                console.log(event);
            },

            roundCost: function() {
                console.log('rounding: ' + this.cost);
                return new Math.round(this.cost);
            },

            submitQuery() {
                console.log("submitting query...");
                console.log(this.form);
            },

            explainQuery() {
                console.log("explain form...");
                console.log(this.form);
            },

            clearForm() {
                console.log("clearing form...");
                this.form = {
                    criteria: '{\n' +
                        '\t\n' +
                        '}',
                    newObj: '{\n' +
                        '\t\'$set\': {\n' +
                        '\t\t//your attributes\n' +
                        '\t}\n' +
                        '}',
                    field: {
                        0 : '_id',
                        1 : null,
                        2 : null,
                        3 : null
                    },
                    order: {
                        0 : 'desc',
                        1 : 'asc',
                        2 : 'asc' ,
                        3 : 'asc'
                    },
                    limit: 0,
                    pageSize: 10,
                    command: 'findAll'
                };
            },

            closeQueryFields() {
                console.log("close query fields");
            },

            closeQueryHints() {
                console.log("close query hints");
            },

            /*
             *  Set the active query format - passed from the Collection Navbar
             *  The format value is passed to each document where the view is flipped accordingly
             *
             *  @var format string
             */
            setQueryFormat( format ) {
                if (format) {
                    this.format = format;
                    // send to store in case other components need to access this value
                    this.$store.dispatch('setCurrentFormat', format);
                }
            },

            /*
             * Handle the pagination process
             *
             * @var page integer This is the desired page number
             */
            pageChanged(page) {
                this.current = page;
                let x = 0;
                let p = page - 1;
                this.visibleObjects = [];
                this.start = (p * this.pageSizeDefault);
                this.end = page * this.pageSizeDefault; // not really using this
                for (x = this.start; x  < this.pageSizeDefault * page; x+=1) {
                    if (this.allObjects[x]) {
                        this.visibleObjects.push(this.allObjects[x]);
                    }
                }
            },

            /*
             *  On mount save the full collection objects (documents) if they exists
             *  Iterate the objects and create a visibleObjects array according to te pagination parameters
             */
            handlePageLoad() {
                let x = 0;
                // need to clear this to remove residual results
                this.clearValues();
                if (this.collection && this.collection.objects) {
                    this.count = this.collection.objects.count;
                    this.allObjects = this.collection.objects.objects;
                    for (x = this.start; x  < this.pageSizeDefault; x+=1) {
                        this.visibleObjects.push(this.allObjects[x]);
                    }
                }
            },

            /*
             *  Clear all data to prevent overlaps and residual data
             */
            clearValues() {
                this.allObjects     = [];
                this.visibleObjects = [];
                this.showing        = 0;
                this.start          = 0;
                this.end            = 0;
                this.current        = 1;
                this.count          = 0;
            }
        },

        /*
         *  Don't fall off your horse!!
         */
        mounted() {
            EventBus.$on('show-collection', () => {
                this.show = true;

            });

            EventBus.$on('set-query-format', ( format ) => {
                this.setQueryFormat( format );
            });

            EventBus.$on('collapse-db', (collapse) => {
                this.collapsed = collapse;

            });
        },

        /*
         *  In case of imenent destruction
         */
        destroyed() {
            this.clearValues();
        },

        /*
         *  Who watches the wathers?
         */
        watch: {
            getCurrentFormat() {
                this.setFormat();
            },

            getObjects() {
                this.handlePageLoad();
            }
        }
    }
</script>
