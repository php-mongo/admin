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
                <th class="bb">&nbsp;</th>
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
                                    <collection-card @loadCollection="loadCollection" v-for="(collection, index) in db.collections" :key="index" v-bind:collection="collection"></collection-card>
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
                                {{ humanReadable(getAvgObjSize) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="tr rb" v-text="showLanguage('database', 'dataSize')"></td>
                            <td>
                                {{ humanReadable(getDataSize) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="tr rb" v-text="showLanguage('database', 'storageSize')"></td>
                            <td>
                                {{ humanReadable(getStorageSize) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="tr rb" v-text="showLanguage('database', 'indexSize')"></td>
                            <td>
                                {{ humanReadable(getIndexSize) }}
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

        /*
        *   Data requirements for this component
        *   ToDo: some of our DB objects dont have a db.stats child - especially in default mode - so we load a stats inti out data ans use computed values
        */
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
            /*
            *   Collections count
            */
            getCollectionCount: function() {
                return this.stats['collections'];
            },

            /*
            *   Objects count
            */
            getObjectsCount: function() {
                return this.stats['objects'];
            },

            /*
            *   Average Objects Size
            */
            getAvgObjSize: function() {
                return this.stats['avgObjSize'];
            },

            /*
            *   Data size
            */
            getDataSize: function() {
                return this.stats['dataSize'];
            },

            /*
            *   Storage size
            */
            getStorageSize: function() {
                return this.stats['storageSize'];
            },

            /*
            *   Index size
            */
            getIndexSize: function() {
                return this.stats['indexSize'];
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
                    if (db.db) {
                        if (db.db.databaseName) {
                            this.name = db.db.databaseName;
                            return db.db.databaseName;
                        }
                        else {db.db.name;
                            this.name = db.db.name;
                            return db.db.name;
                        }
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
            },

            /*
            *   Load the collection panel !! child component event !!
            */
            loadCollection(collection) {
                console.log("load collection: " + collection);
                // send collection for tracking
                this.$store.dispatch('setActiveCollection', collection );
                // load
                let data = {database: this.name, collection: collection };
                this.$store.dispatch('loadCollection', data );
                // hide this panel
                this.show = false;
                // event to hide the db panel
                EventBus.$emit('hide-panels');
                // event to enable collection panel
                EventBus.$emit('show-collection');
            }
        },

        /*
        *   Handle mounted method requirements
        */
        mounted() {
            EventBus.$on('show-database', function() {
                // I was messing around trying to get this panel working correctly and loaded the db.child stats object - partly in use
                this.stats = this.$store.getters.getStats;
                this.show = true;

            }.bind(this));
        }
    }
</script>
