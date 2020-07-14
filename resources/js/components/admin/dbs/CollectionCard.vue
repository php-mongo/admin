<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    .collection-list-item {
        margin-top: 10px;
        position: relative;

        li:last-child {
            display: block;
            margin-bottom: 5px;
        }
    }
</style>

<template>
    <li ref="colbox" class="collection-list-item">
        <img alt="Collection icon" src="/img/icon/table.png" /> <span class="pma-link">{{collection.collection.name}}</span>
    </li>
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
        props: ['collection'],


        /*
        * Defines the computed properties on the component.
        */
        computed: {
            /*
            *
            */
            countItems() {
                return ' (' + this.collection.items.length + ')';
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
