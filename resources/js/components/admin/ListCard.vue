<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ListCard.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   ListCard.vue
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

    .ad-outer-list {
        margin-bottom: 25px;
        position: relative;
        padding-bottom: 5px;

        h4.title {
            background: $outerListBackground;
            margin: 0 auto;
            padding: 5px 5px 5px 20px;
            color: $listColor;
            position: relative;
            -webkit-border-top-left-radius: 10px;
            -webkit-border-top-right-radius: 10px;
            -moz-border-radius-topleft: 10px;
            -moz-border-radius-topright: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;

            .claim-button-box {
                display: inline-block;

                button {
                    font-size: 12px;
                    padding: 7px 10px;
                    color: $whiteFont;
                    background-color: $moreBackgroundColor;
                    border: 1px solid $moreBorderColor;
                    border-radius: 4px;

                    &:hover {
                        background-color: $moreBackgroundColorHover;
                        border-color: $moreBorderColorHover;
                    }
                }
            }

            .more-button-box {
                display: inline-block;
                margin-top: -4px;
                margin-right: 5px;

                button {
                    padding: 7px 14px;
                    font-size: 12px;
                    color: $whiteFont;
                    background-color: $moreBackgroundColor;
                    border: 1px solid $moreBorderColor;
                    border-radius: 4px;
                    cursor: pointer;

                    &:hover {
                        background-color: $moreBackgroundColorHover;
                        border-color: $moreBorderColorHover;
                    }
                }
            }

            .image-rating-container.over {
                background: $white url("/img/thumbs-color-over.jpg") no-repeat scroll 0 0;
            }
        }

        .ad-inner {
            padding: 7px 5px 0 20px;
            background-color: $innerBackground;
            display: inline-block;
            width: 100%;
            -webkit-border-bottom-left-radius: 10px;
            -webkit-border-bottom-right-radius: 10px;
            -moz-border-radius-bottomleft: 10px;
            -moz-border-radius-bottomright: 10px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;

            h5 {
                margin-bottom: 2px;
            }

            p:last-child {
                display: block;
                margin-bottom: 5px;
            }
        }

        .hidden-content {
            visibility: collapse;
            height: 0 !important;
        }
    }
</style>

<template>
    <div ref="lbox" class=" cell ad-outer-list" v-show="show">
        <h4 class="title">{{ ad.title }}
            <span title="Advertiser Rating: { $ad.rating } / 100" v-bind:class="'image-rating-container hide-from-small ' + setClass(ad.rating)" data-advertiser-rating="{ ad.rating }"></span>

            <span class="more-button-box float-right">
                <button v-on:click="showMoreAd(ad.id)" v-text="showLanguage('global', 'more')"></button>
            </span>

            <span class="claim-button-box float-right" v-show="userLoadStatus">
                <button v-bind:title="showLanguage('title', 'claimAd')" v-on:click="claimAd(ad.id)" v-text="showLanguage('global', 'claimAd')"></button>
            </span>
        </h4>
        <div class="ad-inner list">
            <h5 v-bind:title="ad.intro">{{ ad.intro }}</h5>
            <p><strong><span v-text="showLanguage('ad', 'phone')"></span> {{ ad.phone }}</strong>   <strong><span v-text="showLanguage('ad', 'advertiserAge')"></span> {{ ad.age }}</strong>   <strong><span v-text="showLanguage('ad', 'adCreated')"></span> {{ ad.created }}</strong>   <strong><span v-text="showLanguage('ad', 'suburb')"></span> {{ ad.suburb }}</strong></p>
        </div>
        <div class="hidden-content">{{ ad.text }}</div>
    </div>
</template>

<script>
    /*
    *   Imports the mixins used by the component.
    */
    import { LatestTextFilter } from '../../mixins/filters/LatestTextFilter.js';
    import { LatestCityFilter } from '../../mixins/filters/LatestCityFilter.js';

    /*
    * Import the Event bus
    */
    import { EventBus } from '../../event-bus.js';

    export default {
        /*
        *   The component accepts one ad as a property
        */
        props: ['ad'],

        /*
          Define the data used by the component.
        */
        data(){
            return {
                show: true
            }
        },

        /*
        *   Define the mixins used by the component.
        */
        mixins: [
            LatestTextFilter,
            LatestCityFilter
        ],

        /*
        *   Listen to the mounted lifecycle hook.
        */
        mounted(){
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
        * Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Is the browser a BOT?
            */
            isaBot() {
                return this.$store.getters.getBotStatus;
            },

            /*
            * Retrieves the User Load Status from Vuex
            */
            userLoadStatus() {
                return (this.$store.getters.getUserLoadStatus === 2);
            },

            /*
            *   Gets the city filter from the Vuex data store.
            */
            cityFilter() {
                return this.$store.getters.getCityFilter;
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
            *   Trigger the single ad display overlay
            */
            showMoreAd( id ) {
                EventBus.$emit('show-ad', { id: id });
                this.$store.dispatch( 'setDisplayedAd', { id: id } );
            },

            /*
            *   Trigger the Claim and Ad process  for the current user
            *   Used for ads that have been loaded from external sources
            */
            claimAd( id ) {
                console.log('ad claim staked for: ' + id);
            },

            /*
            * Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                //return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },

            returnClass( rating ) {
                if (rating >= 50) {
                    rating = 100 - rating;
                    return 'rotate' + rating + ' over';
                }
                if (rating < 50) {
                    return 'rotate' + rating + ' under';
                }
                return '';
            },

            setClass( rating ) {
                if (rating && isFinite(rating)) {
                    return this.returnClass( rating );
                }
                return '';
            },

            /*
            *   Process the selected filters from the user.
            */
            processFilters( ) {
                /*
                  If no filters are selected, show the card
                */
                if (this.textSearch === ''
                    && this.cityFilter === '') {
                    this.show = true;

                } else {
                    /*
                    *    Initialize flags for the filtering
                    */
                    let textPassed = false,
                        cityPassed = false;

                    /*
                      Check if text passes
                    */
                    if (this.textSearch !== '' && this.processLatestTextFilter(this.ad, this.textSearch)) {
                        textPassed = true;

                    } else if (this.textSearch === '') {
                        textPassed = false;
                    }

                    /*
                    *   Checks to see if the city passed or not.
                    */
                    if (this.cityFilter !== '' && this.processLatestCityFilter(this.ad, this.cityFilter)) {
                        cityPassed = true;

                    } else if (this.cityFilter === '') {
                        cityPassed = false;
                    }

                    /*
                      If we have passes, then we show the Ad Card
                    */
                    this.show = textPassed || cityPassed;
                }
            }
        },

        /*
        *   Defines what should be watched by the Ad card.
        */
        watch: {
            /*
            *   Watches the city filter
            */
            cityFilter() {
                this.processFilters();
            },

            /*
            *    Watches the text search filter.
            */
            textSearch() {
                this.processFilters();
            }
        }
    }
</script>
