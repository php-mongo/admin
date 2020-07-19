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
    }
</style>

<template>
    <div class="collection-inner" v-show="show">
        <table>
            <tr>
                <td class="v-top">
                    <textarea class="criteria" rows="7" cols="70" v-model="form.criteria"></textarea><br/>
                    <div id="newObjInput" v-show="rules.modify">
                        New Object(see <a href="http://www.mongodb.org/display/DOCS/Updating" target="_blank">Updating</a> operators):<br/>
                        <textarea id="newObj" name="newObj" rows="5" cols="70" v-model="form.newObj"></textarea>
                    </div>
                </td>
                <td class="field-orders v-top">
                    <!-- fields will be used in sorting -->
                    <p>
                        <input type="text" v-model="form.field[0]" />
                        <select v-model="form.order[0]">
                            <option value="asc" :selected="orderDefault[0] === 'asc'">ASC</option>
                            <option value="desc" :selected="orderDefault[0] === 'desc'">DESC</option>
                        </select>
                    </p>
                    <p>
                        <input type="text" v-model="form.field[1]" />
                        <select v-model="form.order[1]">
                            <option value="asc" :selected="orderDefault[1] === 'asc'">ASC</option>
                            <option value="desc" :selected="orderDefault[1] === 'desc'">DESC</option>
                        </select>
                    </p>
                    <p>
                        <input type="text" v-model="form.field[2]" />
                        <select v-model="form.order[2]">
                            <option value="asc" :selected="orderDefault[2] === 'asc'">ASC</option>
                            <option value="desc" :selected="orderDefault[2] === 'desc'">DESC</option>
                        </select>
                    </p>
                    <p>
                        <input type="text" v-model="form.field[3]" />
                        <select v-model="form.order[3]">
                            <option value="asc" :selected="orderDefault[3] === 'asc'">ASC</option>
                            <option value="desc" :selected="orderDefault[3] === 'desc'">DESC</option>
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
                                Fields(<span id="query_fields_count">{{queryFieldsCount}}</span>)
                                <span class="f11">▼</span>
                            </span>
                            |
                            <span class="pma-link" v-on:onclick="showQueryHints($event)" title="Choose indexes will be used in query">
                                Hints(<span id="query_hints_count">{{queryHintsCount}}</span>)
                                <span class="f11">▼</span>
                            </span>
                            |
                        </span>
                    </span>
                    <!-- end query fields and hints -->
                    <label id="limitLabel" v-show="rules.findAll">Limit:<input type="text" v-model="form.limit" size="5" /> |</label>
                    <span id="pageSetLabel" v-show="rules.findAll">
                        <select title="Rows per Page" v-model="form.pageSize">
                            <page-size-option v-for="(size, index) in pageSizes" :key="index" v-bind:size="size" v-bind:value="pageSizeDefault"></page-size-option>
                        </select>
                        |
                    </span>
                    Action:
                    <select v-model="form.command" v-on:change="changeCommand($event)">
                        <option value="findAll" :selected="commandDefault === 'findAll'">findAll</option>
                        <option value="remove" :selected="commandDefault === 'remove'">remove</option>
                        <option value="modify" :selected="commandDefault === 'modify'">modify</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="buttons">
                    <input type="submit" value="Submit Query" v-on:click="submitQuery" />
                    <input type="button" value="Explain" v-on:click="explainQuery" />
                    <input type="button" value="Clear Conditions" v-on:click="clearForm" />
                    [<a href="http://rockmongo.com/wiki/queryExamples?lang=en_us" target="_blank">Query Examples</a>]
                    <span v-if="cost">Cost {{ roundCost }}s</span>
                    <p v-if="message" class="error">{{ message }}</p>
                </td>
            </tr>
    </table>
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

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            PageSizeOption
        },

        /*
        *   The component accepts one db as a property
        */
        props: ['collection'],

        data() {
            return {
                show: false,
                name: null,
                stats: {},
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
                nativeFields: null,
                queryFields: [],
                queryHints: [],
                cost: '0.000186',
                message: null
            }
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {
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

            setCurrentFormat( format ) {
                this.$store.dispatch('setCurrentFormat', format);
                this.format = format;
            },

            setFormat() {
                let format = this.$store.getters.getCurrentFormat;
                if (format !== this. format) {
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
                console.log(this.form);
            }
        },

        mounted() {
            EventBus.$on('show-database', function() {
                this.show = true;

            }.bind(this));
        },

        watch: {
            getCurrentFormat() {
                this.setFormat();
            }
        }
    }
</script>
