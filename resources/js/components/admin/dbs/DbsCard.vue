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
        <img alt="Database icon" src="/img/icon/database.png" /> <span class="pma-link" v-on:click="showCollectionsList(db.db.name)">{{getDbName(db)}}</span> {{countCollections}}
        <ul ref="coll" class="collections hide-list">
            <collection-card @loadCollection="loadCollection" v-for="collection in db.collections" :key="(collection.id + 1)" v-bind:collection="collection"></collection-card>
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
        *   Listen to the mounted lifecycle hook.
        */
        mounted(){
            /*
                Hide any displayed collections lists
            */
            EventBus.$on('hide-collection-lists', function( ) {
                this.hideCollections();

            }.bind(this));

            /*
              When the filters are updated, we process the filters.
            */
            EventBus.$on('filters-updated', function( filters ) {
                this.processFilters( filters );

            }.bind(this));

            /*
              Apply filters
            */
            this.processFilters();
        },

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
                this.$jqf(this.$refs.coll).replace(['active', 'hide-list']);
            },

            loadCollection( collection ) {
                //console.log("loading collection from left nav: " + collection);
                // send collection and db for tracking
                this.$store.dispatch('setActiveDatabase', this.activeDb );
                this.$store.dispatch('setActiveCollection', collection );
                // load
                let data = {database: this.activeDb, collection: collection };
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
