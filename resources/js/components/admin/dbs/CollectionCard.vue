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
        <img alt="Collection icon" src="img/icon/collection.png" /> <span class="pma-link" @click="$emit('loadCollection', getName)">{{getName}}</span>
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
            *   This was added for future use
            */
            /*countItems() {
                return '(' + this.collection.items.length + ')';
            },*/

            /*
             *  Because database and collections bot have two version of name keys (name & collectionName)
             */
            getName() {
              return this.collection.collection.name ? this.collection.collection.name : this.collection.collection.collectionName;
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
