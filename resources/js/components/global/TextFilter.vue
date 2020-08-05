<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      TextFilter.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      TextFilter.vue
  - @subpackage   Id
  - @link         https://github.com/php-mongo/admin PHP MongoDB Admin
  - @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
  - @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
  - @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
  -  php-mongo-admin - License conditions:
  -  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
  -  This web application is available as Free Software and has no implied warranty or guarantee of usability.
  -  See licence.txt for the complete licensing outline.
  -  See COPYRIGHT.php for copyright notices and further details.
  -->

<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    #text-filter {
        input {
            margin-right: 10px;
            font-size: 14px;
            height: 36px;
            padding: 10px 16px;
            line-height: 1.3333333;
            border-radius: 6px;
            color: #555;
            background-color: $white;
            background-image: none;
            border: 1px solid $inputBorder;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        }
    }
</style>

<template>
    <span id="text-filter" v-show="showFilters">
        <input class="form-control input-group-sm" v-model="textSearch" v-bind:placeholder="showLanguage('global', 'search')">
    </span>
</template>

<script>
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../event-bus.js';

    export default {
        /*
        *   Defines the mounted lifecycle hook.
        */
        mounted() {
            /*
              When the user wants to show the filters, we show the filter
              sidebar.
            */
            EventBus.$on('show-filters', () => {
                this.show = true;

            });

            /*
              When the user clears the filters, we clear all set filters.
            */
            EventBus.$on('clear-filters', () => {
                this.clearFilters();

            });
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Gets the show filters data from the state.
            */
            showFilters() {
                return this.$store.getters.getShowFilters;
            },

            /*
            *   Gets the current filter text search
            */
            textSearch: {
                set( textSearch ) {
                    this.$store.commit( 'setTextSearch', textSearch )
                },
                get() {
                    return this.$store.getters.getTextSearch;
                }
            },

            /*
            *   Get the cities from the Vuex store.
            */
            cities() {
                return this.$store.getters.getCities;
            },

            /*
              Gets the cities load status from the Vuex data store.
            */
            citiesLoadStatus() {
                return this.$store.getters.getCitiesLoadStatus;
            },

            /*
              Get the city filter and provide a setter. This way
              we can use it as model.
            */
            cityFilter: {
                set( cityFilter ) {
                    this.$store.commit( 'setCityFilter', cityFilter );
                },
                get() {
                    return this.$store.getters.getCityFilter;
                }
            },

        },

        /*
        *   Defined methods
        */
        methods: {
            /*
            * Calls the Translation and Language service
            */
            showLanguage( context, key ) {
             //   return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Clear all of the filters.
            */
            clearFilters() {
                this.$store.dispatch( 'resetFilters' );
            },

            /*
            *   Toggle the show and hide of filter sidebar.
            */
            toggleShowFilters() {
                this.$store.dispatch( 'toggleShowFilters', { showFilters : !this.showFilters } );
            },
        },

        watch: {
            /*
              Watch the city filter.
            */
            'cityFilter': function() {
                /*
                  If the city filter is not empty, find the city slug
                  for the city we are filtering by.
                */
                if ( this.cityFilter !== '' ) {
                    let slug = '';

                    /*
                      Iterate over all of the cities and find the city that
                      matches the ID of the selected filter.
                    */
                    for ( let i = 0; i < this.cities.length; i++ ) {
                        if ( this.cities[i].id === this.cityFilter ) {
                            slug = this.cities[i].slug;
                        }
                    }

                    if ( slug === '' ) {
                        /*
                          We are moving to just the cafes screen if the filter is empty.
                        */
                        this.$router.push( { name: 'cafes' } );

                    } else {
                        /*
                          Navigate to the city.
                        */
                        this.$router.push( { name: 'city', params: { slug: slug } } );
                    }

                } else {
                    /*
                      Navigate to the cafes view.
                    */
                    this.$router.push( { name: 'cafes' } );
                }
            },

            /*
              Watch the cities load status.
            */
            'citiesLoadStatus': function() {
                if ( this.citiesLoadStatus === 2 && this.$route.name === 'city' ) {
                    let id = '';

                    /*
                      Check to see if the slug matches the route parameter and
                      set the city filter.
                    */
                    for ( let i = 0; i < this.cities.length; i++ ) {
                        if ( this.cities[i].slug === this.$route.params.slug ) {
                            this.cityFilter = this.cities[i].id;
                        }
                    }
                }
            }
        }
    }
</script>
