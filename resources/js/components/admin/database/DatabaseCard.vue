<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DatabaseCard.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DatabaseCard.vue
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

    .collection-list {
        ul {
            list-style: none;
            display: block;
            margin-left: 20px;

            li.coll {
                border-bottom: 1px solid $lighterGrey;
                margin: 0 0 10px 0;

                input {
                    margin-right: 20px !important;;
                }

                .obj-count {
                    width: 10.5vw;
                }

                &:hover {
                    background-color: $lighterGrey;
                }
            }
        }
    }

    .hide-list {
        display: none !important;
    }
</style>

<template>
    <div class="database-inner" v-show="show">
        <table class="bordered unstriped">
            <tr>
                <th class="text-center bb title"><span v-text="showLanguage('database', 'database', getDbName(db))"></span> </th>
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
                            <td class="text-center rb">
                                <p><span class="pma-link" v-text="showLanguage('database', 'dropAll')"></span> | <span class="pma-link" v-text="showLanguage('database', 'clearAll')"></span></p>
                            </td>
                            <td class="text-center">
                                <p v-text="showLanguage('database', 'objectsCount')"></p>
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
                collections: [],
                database: {},
                stats: {},
                collapsed: false
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
                if (this.stats) {
                    return this.stats['collections'];
                }
            },

            /*
            *   Objects count
            */
            getObjectsCount: function() {
                if (this.stats) {
                    return this.stats['objects'];
                }
            },

            /*
            *   Average Objects Size
            */
            getAvgObjSize: function() {
                if (this.stats) {
                    return this.stats['avgObjSize'];
                }
            },

            /*
            *   Data size
            */
            getDataSize: function() {
                if (this.stats) {
                    return this.stats['dataSize'];
                }
            },

            /*
            *   Storage size
            */
            getStorageSize: function() {
                if (this.stats) {
                    return this.stats['storageSize'];
                }
            },

            /*
            *   Index size
            */
            getIndexSize: function() {
                if (this.stats) {
                    return this.stats['indexSize'];
                }
            },

            getDB() {
                return (this.db);
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

            getChildren() {
                this.stats       = this.db.stats;
                this.collections = this.db.collections;
                this.database    = this.db.db;
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
                // send collection for tracking
                this.$store.dispatch('setActiveCollection', collection );
                // load
                let data = {database: this.name, collection: collection };
                this.$store.dispatch('loadCollection', data );
                // event to hide th db panel
                EventBus.$emit('hide-panels');
                // event to enable collection panel
                EventBus.$emit('show-collection', collection );
                // hide this panel
                this.show = false;
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
            },
        },

        /*
        *   Handle mounted method requirements
        */
        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels', () => {
                this.hideComponent();

            });

            EventBus.$on('show-database', () => {
                // I was messing around trying to get this panel working correctly and loaded the db.child stats object - partly in use
                this.stats = this.$store.getters.getStats;
                this.show = true;

            });
        },

        watch: {
            getDB() {
                this.getChildren();
            }
        }
    }
</script>
