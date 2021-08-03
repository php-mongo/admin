<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DbsCard.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DbsCard.vue
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

    .db-inner {
        background-color: $innerBackground;
        display: block;
        padding: 4px 5px 4px 10px;
        margin-bottom: 10px;
        width: 100%;
        border-radius: 10px;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;

        ul.collections {
            list-style: none;
            display: block;
            margin-left: 20px;
        }

        .hide-list {
            display: none !important;
        }
    }
</style>

<template>
    <li ref="db" class="db-inner" v-show="show">
        <img alt="Database icon" src="img/icon/database.png" /> <span class="pma-link" v-on:click="showCollectionsList(getDbName(db))">{{getDbName(db)}}</span> {{countCollections}}
        <ul ref="coll" class="collections hide-list">
            <collection-card @loadDbsCollection="loadDbsCollection" v-for="collection in db.collections" :key="(collection.id + 1)" v-bind:collection="collection"></collection-card>
        </ul>
    </li>
</template>

<script>
    /*
    *   Imports the mixins used by the component.
    */
    import { DbsTextFilter } from '../../../mixins/filters/DbsTextFilter.js';

    /*
    * Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';
    import CollectionCard from "./CollectionCard";

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
          Define the data used by the component.
        */
        data(){
            return {
                show: true,
                activeDb: null
            }
        },

        /*
        *   Define the mixins used by the component.
        */
        mixins: [
             DbsTextFilter
        ],

        /*
        * Defines the computed properties on the component.
        */
        computed: {
            /*
            *
            */
            countCollections() {
                return ' (' + this.db.collections.length + ')';
            },

            /*
            *    Gets the text search filter from the Vuex data store.
            */
            textSearch() {
                return this.$store.getters.getTextSearch;
            }
        },

        /*
        *   Defined methods for the component
        */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                // return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   There are two versions of DB name returned
            */
            getDbName( db ) {
                if (db.db.databaseName) {
                    this.name = db.db.databaseName;
                    return db.db.databaseName;
                }
                else {db.db.name;
                    this.name = db.db.name;
                    return db.db.name;
                }
            },

            /*
            *   Show the collections for a db
            */
            showCollectionsList( db ) {
                // load the selected DB from our cache
                this.$store.dispatch('loadCachedDatabase', db );
                // hide all that are showing
                EventBus.$emit('hide-collection-lists', {});
                this.activeDb = db;
                this.$jqf(this.$refs.coll).replace(['hide-list', 'active']);
            },

            /*
            *   Hide the collections
            */
            hideCollections() {
                this.activeDb = null;
                if (this.$refs.coll) {
                    this.$jqf(this.$refs.coll).replace(['active', 'hide-list']);
                }
            },

            loadDbsCollection( collection ) {
                // send collection and db for tracking
                this.$store.dispatch('setActiveDatabase', this.activeDb );
                this.$store.dispatch('setActiveCollection', collection );
                // load
                let data = { database: this.activeDb, collection: collection };
                this.$store.dispatch('loadCollection', data );
                // event to hide panels
                EventBus.$emit('hide-panels');
                // event to enable collection panel
                EventBus.$emit('show-collection', collection);
            },

            /*
            *   Process the selected filters from the user.
            */
            processFilters( ) {
                /*
                  If no filters are selected, show the card
                */
                if (this.textSearch === '') {
                    this.show = true;

                } else {
                    /*
                    *    Initialize flags for the filtering
                    */
                    let textPassed = false;

                    /*
                      Check if text passes
                    */
                    if (this.textSearch !== '' && this.processDbsTextFilter(this.db, this.textSearch)) {
                        textPassed = true;

                    } else if (this.textSearch === '') {
                        textPassed = false;
                    }

                    /*
                      If we have passes, then we show the Ad Card
                    */
                    this.show = textPassed;
                }
            }
        },

        /*
        *   Listen to the mounted lifecycle hook.
        */
        mounted(){
            /*
                Hide any displayed collections lists
            */
            EventBus.$on('hide-collection-lists', ( )  => {
                this.hideCollections();

            });

            /*
              When the filters are updated, we process the filters.
            */
            EventBus.$on('filters-updated', ( filters ) => {
                this.processFilters( filters );

            });

            /*
              Apply filters
            */
            this.processFilters();
        },

        /*
        *   Defines what should be watched by the Ad card.
        */
        watch: {
            /*
            *    Watches the text search filter.
            */
            textSearch() {
                this.processFilters();
            }
        }
    }
</script>
