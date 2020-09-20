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
        .records-header {
            &.fixed {
                background: $cyan;
                color: aliceblue;
                left: 245px;
                padding: 10px 0 0 10px;
                position: fixed;
                top: 75px;
                width: calc(100vw - 278px);
                z-index: 9000;

                ul {
                    margin-bottom: .7rem;
                }
            }
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
            p.error {
                color: $red;
                font-weight: 600;
            }
        }
        .fields-control {
            display: none;
        }
        .collection-document {
            border: 2px $regularGrey solid;
            margin-bottom: 10px;
            min-height: 100px;
            position: relative;

            &:hover {
                background-color: $offWhite;
            }

            .doc-nav {
                border-bottom: 1px $lighterGrey solid;
                display: inline-block;
                margin: 0 0 5px 50px;
                padding: 5px 0 0 0;
            }
            .doc-data {
                display: block;
                max-height: 100px;
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
        .panel-modal {
            left: 0;
            background-color: $darkGreyColor;
            height: 100%;
            opacity: 0.95;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 9000;

            .panel-modal-inner {
                /* background: $white; */
                background: $white url("/img/white-bg.jpg") repeat center center;
                background-size: cover;
                box-shadow: 0 0 4px 0 rgba(0,0,0,0.12), 0 4px 4px 0 rgba(0,0,0,0.24);
                border-left: 5px solid $orange;
                border-right: 5px solid $orange;
                color: $noticeColor;
                font-family: "Lato", sans-serif;
                font-size: 16px;
                line-height: 60px;
                margin: auto auto auto auto;
                max-height: 90vh;
                max-width: 50vw;
                min-height: 666px;
                min-width: 400px;
                opacity: 1;
                overflow-y: auto;
                padding: 0 3rem 3rem 3rem;
                position: relative;
                z-index: 9999;

                .modal-header {
                    background-color: $lightGreyColor;
                    height: 33px;
                    margin: 0 -3rem 0 -3rem;
                    max-width: 49.5vw;
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

                textarea.export-data {
                    min-height: 150px;
                    width: 100%;
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

                p.file-select {
                    border-bottom: solid 1px $darkBlue;
                    margin-bottom: 20px;
                }

                ul {
                    list-style: none;

                    li {
                        background-color: $lighterGrey;
                        margin-bottom: 10px;
                        padding: 4px 10px;

                        .title {
                            display: inline-block;
                            min-width: 150px;
                        }

                        .clr-bg {
                            background-color: transparent;
                        }

                        p {
                            padding: 2px 0 3px 5px;

                            label {
                                background-color: $tabColor;
                                margin-left: 5px;
                            }

                            input {
                                margin: 0;
                                vertical-align: middle;
                            }
                        }

                        p.time {
                            border-bottom: 1px solid $darkGreyColor;

                            .log-link {
                                float: right;
                            }
                        }

                        .data {
                            display: inline-block;
                            max-width: 100%;
                            overflow-x: auto;
                            vertical-align: bottom;
                        }
                    }
                }
            }
        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 768px) and (max-width: 992px) {
        .collection-inner {
            .panel-modal .panel-modal-inner {
                min-width: 500px;
            }
            .panel-modal .panel-modal-inner .modal-header {
                max-width: 64vw;
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
                    <input type="submit" :value="showLanguage('collection', 'buttonSubmit')" v-on:click="submitQuery" />
                    <input type="button" :value="showLanguage('collection', 'buttonExplain')" v-on:click="explainQuery" />
                    <input type="button" :value="showLanguage('collection', 'buttonClear')" v-on:click="clearForm" />
                    [<a href="https://phpmongoadmin.com/support/documentation/collections/collection-query-examples" target="_blank" v-text="showLanguage('collection', 'queryExamples')"></a>]
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
            <div ref="recordsHeader" :class="'records-header ' + watchScroll">
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
        <document-update></document-update>
        <document-field></document-field>
        <document-duplicate></document-duplicate>
        <document-new></document-new>
        <query-logs></query-logs>
        <query-explain></query-explain>
        <collection-statistics></collection-statistics>
        <collection-export></collection-export>
        <collection-import></collection-import>
        <collection-properties></collection-properties>
        <collection-indexes></collection-indexes>
        <collection-rename></collection-rename>
        <collection-duplicate></collection-duplicate>
        <collection-validation></collection-validation>
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
    import DocumentUpdate from "./DocumentUpdate";
    import DocumentField from "./DocumentField";
    import DocumentDuplicate from "./DocumentDuplicate";
    import DocumentNew from "./DocumentNew";
    import QueryLogs from "./QueryLogs";
    import CollectionStatistics from "./CollectionStatistics";
    import CollectionExport from "./CollectionExport";
    import CollectionImport from "./CollectionImport";
    import CollectionProperties from "./CollectionProperties";
    import CollectionIndexes from "./CollectionIndexes";
    import CollectionRename from "./CollectionRename";
    import CollectionDuplicate from "./CollectionDuplicate";
    import CollectionValidation from "./CollectionValidation";
    import QueryExplain from "./QueryExplain";

    export default {
        /*
         *   Register the components to be used by the home page.
         */
        components: {
            PageSizeOption,
            Document,
            Pagination,
            DocumentUpdate,
            DocumentField,
            DocumentDuplicate,
            DocumentNew,
            QueryLogs,
            QueryExplain,
            CollectionStatistics,
            CollectionExport,
            CollectionImport,
            CollectionProperties,
            CollectionIndexes,
            CollectionRename,
            CollectionDuplicate,
            CollectionValidation
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
                expanded: false,
                name: null,
                start: 0,
                showing: 0,
                end: 0,
                current: 1,
                count: 0,
                stats: {},
                allObjects: [],
                filterObjects: [], // used to store allObjects while filtering
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
                lockPagination: false,
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
                // query elements
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
                message: null,
                index: 0,
                limit: 75
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
                if (this.collection && this.collection.objects) {
                    if (this.collection.objects || this.allObjects) {
                        if (this.collection.objects.count == 0) {
                            this.page.find.message = this.showLanguage('collection', 'empty', this.collection.collection.collectionName);
                            this.clearValues();

                        } else {
                            this.page.find.message = null;
                        }
                        //return this.collection.objects.count;
                        return this.allObjects.length;
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
                if (this.collection && this.collection.objects) {
                    return this.collection.objects;
                }
            },

            roundCost: function() {
                return Math.round(this.cost);
            },

            watchScroll() {
                return this.lockPagination === true ? 'fixed' : '';
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

            getDatabaseName() {
                return this.collection.collection.databaseName;
            },

            getCollectionName() {
                return this.collection.collection.collectionName;
            },

            submitQuery() {
                this.message = null;
                if (this.collection.objects.count === 0) {
                    this.message = this.showLanguage('collection', 'msgNoDocs');
                    return;
                }
                if (this.collection.objects.count <= this.pageSizeDefault) {
                    this.message = this.showLanguage('collection', 'msgTooFew');
                    return;
                }
                if (this.format === 'json') {
                    let json = this.$convObj().minify(this.form.criteria.json);
                    if (json === '{}') {
                        this.message = this.showLanguage('collection', 'msgEmptyQuery');
                        return;
                    }
                    this.form.criteria.json = json;
                }
                if (this.format === 'array') {
                    let array = this.$convObj().minify(this.form.criteria.array);
                    if (array === '()') {
                        this.message = this.showLanguage('collection', 'msgEmptyQuery');
                        return;
                    }
                    this.form.criteria.array = array;
                }
                // tests completed - send query to DB
                let data = {params: this.form, format: this.format, database: this.getDatabaseName(), collection: this.getCollectionName()};
                this.$store.dispatch('queryCollection', data);
                this.handleQuery();
            },

            handleQuery() {
                let status = this.$store.getters.getQueryCollectionLoadStatus;
                if (status === 1 && this.index < this.limit) {
                    let self = this;
                    setTimeout(function() {
                        self.handleQuery();
                    }, 100);
                }
                else if (status === 2) {
                    this.allObjects = this.$store.getters.getQueryCollection;
                    this.count = this.allObjects.length;
                    if (this.count >= 1) {
                        this.visibleObjects = [];
                        let x = 0;
                        for (x = this.start; x  < this.pageSizeDefault; x += 1) {
                            if (this.allObjects[x]) {
                                this.visibleObjects.push(this.allObjects[x]);
                            }
                        }
                    } else {
                        this.message = this.showLanguage('collection', 'msgEmptyResult');
                    }
                }
                else if (status === 3) {
                    this.message = this.showLanguage('collection', 'msgQueryError');
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

            explainQuery() {
                let query;
                if (this.format === 'json') {
                    query = this.$convObj(this.form.criteria.json).minify();
                    if (query === '{}') {
                        this.message = this.showLanguage('collection', 'msgEmptyQuery');
                        return;
                    }
                }
                if (this.format === 'array') {
                    query = this.$convObj(this.form.criteria.array).minify();
                    if (query === '()') {
                        this.message = this.showLanguage('collection', 'msgEmptyQuery');
                        return;
                    }
                    query = this.$convObj(query).arrayToJson();
                }
                let data = { query: query, format: this.format, database: this.getDatabaseName(), collection: this.getCollectionName() };
                EventBus.$emit('show-query-explain', data);
            },

            clearForm() {
                this.form = {
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
                };
                this.handlePageLoad();
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
                for (x = this.start; x  < this.pageSizeDefault * page; x += 1) {
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
                    this.filterObjects = this.allObjects;
                    for (x = this.start; x  < this.pageSizeDefault; x += 1) {
                        if (this.allObjects[x]) {
                            this.visibleObjects.push(this.allObjects[x]);
                        }
                    }
                }
            },

            /*
             *  Clear all data to prevent overlaps and residual data
             */
            clearValues() {
                this.allObjects     = [];
                this.filterObjects  = [];
                this.count          = 0;
                this.current        = 1;
                this.end            = 0;
                this.message        = null;
                this.start          = 0;
                this.showing        = 0;
                this.visibleObjects = [];
            },

            /*
             *  Handles a document updated by the Update modal
             */
            updateDocument( data ) {
                if (data) {
                    let obj = JSON.parse(data.document);
                    if (this.visibleObjects[data.index]) {
                        this.visibleObjects[ data.index ].raw = obj;
                    }

                    // Done!! these conversion will be based on the current FORMAT setting
                    if (this.format === 'json') {
                        let t = this.$convObj( obj ).jsonT( data.document );
                        let d = this.$convObj( obj ).jsonH( data.document );
                    }
                    if (this.format === 'array') {
                        let t = this.$convObj( obj ).arrayT( data.document );
                        let d = this.$convObj( obj ).arrayH( data.document );
                    }

                    this.visibleObjects[ data.index ].text = t;
                    this.visibleObjects[ data.index ].data = d;
                    this.$store.dispatch( 'setDocument', {  text: t, data: d, index: data.index } );
                }
            },

            /*
             *  When a document is inserted via the New Document modal we want to refresh this panel
             */
            fetchData() {
                this.handlePageLoad();
            },

            /*
             *  Handle a 'query resend' initialised by modal
             */
            sendQuery(query) {
                this.form.criteria[this.format] = query;
                this.submitQuery();
            },

            /*
             *  This is sent from the PanelView.vue when a collection is active
             */
            handleScroll(status) {
                this.lockPagination = status; //!this.lockPagination;
                if (status === true && this.expanded === true) {
                    this.$jqf(this.$refs.recordsHeader).css('left', '5px');
                    this.$jqf(this.$refs.recordsHeader).css('width', '98vw');
                }
                if (status === true && this.expanded === false) {
                    this.$jqf(this.$refs.recordsHeader).css('left', '245px');
                    this.$jqf(this.$refs.recordsHeader).css('width', 'calc(97vw - 260px)');
                }
            },

            /*
             *  This method receives input from the Breadcrumbs text filter
             */
            handleFilter(filter) {
                if (filter) {
                    this.allObjects = [];
                    this.visibleObjects = [];
                    let raw;
                    let x = 0;
                    this.filterObjects.forEach((object, index) => {
                        raw = JSON.stringify(object.raw);
                        if (raw.indexOf(filter) !== -1) {
                            this.allObjects.push(object);
                        }
                    });
                    this.count = this.allObjects.length;
                    for (x = this.start; x  < this.pageSizeDefault; x += 1) {
                        if (this.allObjects[x]) {
                            this.visibleObjects.push(this.allObjects[x]);
                        }
                    }

                } else {
                    this.handlePageLoad();
                }
            },

            watchLeftNav() {
                this.expanded = !this.expanded;
            }
        },

        /*
         *  Don't fall off your horse!!
         */
        mounted() {
            EventBus.$on('show-collection', () => {
                this.show = true;
            });

            EventBus.$on('collapse-db', (collapse) => {
                this.collapsed = collapse;
            });

            EventBus.$on('document-updated', (data) => {
                this.updateDocument( data );
            });

            EventBus.$on('document-inserted', () => {
                this.fetchData();
            });

            EventBus.$on('send-query', (query) => {
                this.sendQuery(query);
            });

            EventBus.$on('lockCollectionPagination', (status) => {
                this.handleScroll(status);
            });

            EventBus.$on('run-document-filter', (filter) => {
                this.handleFilter(filter);
            });

            EventBus.$on('clear-document-filter', () => {
                this.handleFilter();
            });

            EventBus.$on('collapse-left-nav', () => {
                this.watchLeftNav();
            });

            EventBus.$on('expand-left-nav', () => {
                this.watchLeftNav();
            });
        },

        /*
         *  In case of imminent destruction
         */
        destroyed() {
            this.clearValues();
        },

        /*
         *  Who watches the watchers?
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
