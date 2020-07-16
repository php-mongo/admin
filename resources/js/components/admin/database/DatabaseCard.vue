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
        <table class="bordered unstriped">
            <tr>
                <th class="text-center bb"><span v-text="showLanguage('database', 'database', getDbName(db))"></span> </th>
                <th class="bb">&nbsp;</span> </th>
            </tr>
            <tr>
                <th v-text="showLanguage('database', 'collections')" class="bb rb"></th>
                <th v-text="showLanguage('database', 'statistics')" class="bb"></th>
            </tr>
            <tr>
                <td class="vat" style="width: 50%">
                    <table class="collection unstriped">
                        <tr>
                            <td v-text="showLanguage('database', 'collections')" class="text-center rb" style="width: 50%;"></td>
                            <td class="text-center">
                                {{ getCollectionCount }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center rb">&nbsp;</td>
                            <td class="text-center">
                                <p><span class="pma-link" v-text="showLanguage('database', 'dropAll')"></span> | <span class="pma-link" v-text="showLanguage('database', 'clearAll')"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="collection-list">
                                <ul>
                                    <collection-card v-for="(collection, index) in db.collections" :key="index" v-bind:collection="collection"></collection-card>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table class="collection unstriped">
                        <tr>
                            <td class="tr rb" v-text="showLanguage('database', 'objects')"></td>
                            <td>
                                {{ getObjectsCount }}
                            </td>
                        </tr>
                        <tr>
                            <td class="tr rb" v-text="showLanguage('database', 'avgObjSize')"></td>
                            <td>
                                {{ humanReadable(db.stats.avgObjSize) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="tr rb" v-text="showLanguage('database', 'dataSize')"></td>
                            <td>
                                {{ humanReadable(db.stats.dataSize) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="tr rb" v-text="showLanguage('database', 'storageSize')"></td>
                            <td>
                                {{ humanReadable(db.stats.storageSize) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="tr rb" v-text="showLanguage('database', 'indexSize')"></td>
                            <td>
                                {{ humanReadable(db.stats.indexSize) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    /*
    *   Import components for the Databases View
    */
    import CollectionCard from './CollectionCard';

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            CollectionCard
        },

        /*
        *   The component accepts one db as a property
        */
        props: ['db'],

        data() {
            return {
                show: false,
                name: null,
                stats: {}
            }
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {
            statsLength() {
                return typeof this.db.stats == 'object';
            },

            getCollectionCount: function() {
                return this.stats['collections'];
            },

            getObjectsCount: function() {
                return this.stats['objects'];
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
            *   There are two versions of DB name returned
            */
            getDbName: function(db) {
                if (db) {
                    if (db.db.databaseName) {
                        this.name = db.db.databaseName;
                        return db.db.databaseName;
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
            }//,

            /*setStats() {
                this.stats = this.db.stats;
            }*/
        },

        mounted() {
            EventBus.$on('show-database', function() {

                this.stats = this.$store.getters.getStats;
                this.show = true;

            }.bind(this));
        }//,

        /*watch: {
            statsLength() {
                this.setStats();
            }
        }*/
    }
</script>
