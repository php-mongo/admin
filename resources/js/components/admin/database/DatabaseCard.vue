<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    ul.collections {
        list-style: none;
        display: block;
        margin-left: 20px;
    }

    .hide-list {
        display: none !important;
    }
</style>

<template>
    <div class="database-inner" v-show="show">
        <form id="db-list">
            <table class="bordered unstriped">
                <tr>
                    <th colspan="2" class="bb"><span v-text="showLanguage('database', 'database', getDbName(db))"></span> </th>
                </tr>
                <tr>
                    <th v-text="showLanguage('database', 'collections')" class="bb br"></th>
                    <th v-text="showLanguage('database', 'statistics')" class="bb"></th>
                </tr>
                <tr>
                    <td>
                        <table class="collection unstriped">
                            <tr>
                                <td v-text="showLanguage('database', 'collections')" class="br"></td>
                                <td>{{ db.stats.collections }}</td>
                            </tr>
                            <tr>
                                <td class="br">
                                    <p><span class="pma-link" v-text="showLanguage('database', 'dropAll')"></span></p>
                                </td>
                                <td>
                                    <p><span class="pma-link" v-text="showLanguage('database', 'clearAll')"></span></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <p>Collections go here</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        // statistics
                    </td>
                </tr>
            </table>
        </form>
    </div>
</template>

<script>
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    export default {
        /*
        *   The component accepts one db as a property
        */
        props: ['db'],

        data() {
            return {
                show: false,
                name: null
            }
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {
            setCollections: ( collections ) => {
                console.log("collections: " + collections);
                let x = 0;
                let str = '';
                for( x in collections) {
                    str += '<p><span class="pma-link">' + collections[x].collection.name + '</span></p>';
                }
                return str;
            }/*,

            dbLength() {
                return this.db.stats.length > 0;
            }*/
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
            *   There are two versions of DB name returned
            */
            getDbName( db ) {
                if (db) {
                    if (db.databaseName) {
                        this.name = db.databaseName;
                        return db.databaseName;
                    }
                    else {db.name;
                        this.name = db.name;
                        return db.name;
                    }
                }
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
            }
        },

        mounted() {
            EventBus.$on('show-database', function() {
                this.show = true;
            }.bind(this));
        }
    }
</script>
