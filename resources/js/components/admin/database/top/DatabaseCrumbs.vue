<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DatabaseCrumbs.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DatabaseCrumbs.vue
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
    .crumb-nav-wrapper {
        background-color: $lighterGreyColor;
        padding-left: 50px;
    }
    nav.crumb-navigation {
        background-color: $lighterGreyColor;
        min-height: 33px;
        max-width: 100%;
        padding-left: 14px;
        ul.links {
            display: inline-block;
            margin: 0;
            li {
                display: inline-block;
                list-style-type: none;
                margin-left: 10px;

                &:hover {
                    background-color: inherit;
                    color: inherit;
                }

                span.crumb {
                    font-weight: bold;
                    font-size: 12px;
                    line-height: 33px;
                    color: $white;
                    &:hover {
                        color: $linkColor;
                        text-decoration: underline;
                    }
                }
                span.dbl-arr {
                    color: $white;
                    margin-right: 5px;
                }
            }
        }
        ul.right {
            position: absolute;
            right: 18px;
            top: 0;

            .li-filter {
                position: relative;

                .doc-filter {
                    max-height: 30px;
                    margin: 2px 0 0 0;
                }

                span {
                    position: absolute;
                    top: 0;
                    right: 3px;

                    img {
                        cursor: pointer;
                        display: inline-block;
                        width: 1rem;
                    }
                }
            }

            .nav-coll {
                span {
                    cursor: pointer;

                    img {
                        margin-top: -3px;
                    }
                }
            }
        }
    }
    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        nav.crumb-navigation {
            /* nothing yet */
        }
    }
    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 769px) and (max-width: 992px) {
        nav.crumb-navigation {
            /* nothing yet */
        }
    }
    /* Large only - (min-width: 64em) and (max-width: 74.9375em) */
    @media (min-width: 993px) and (max-width: 2048px) {
        nav.crumb-navigation {
            /* nothing yet */
        }
    }
</style>

<template>
    <div class="database-crumbs">
        <nav class="crumb-navigation">
            <ul class="links">
                <li class="crumb-link text-left">
                    <img src="img/icon/database.png" /> <span class="crumb pma-link" v-on:click="loadDatabase($event)">{{ databaseName }}</span>
                </li>
                <li class="crumb-link text-left" v-if="showCollection">
                    <span class="dbl-arr">>></span> <img src="img/icon/collection.png" />
                    <span class="crumb pma-link" v-on:click="loadCrumb( collectionName )">{{ collectionName }}</span>
                </li>
                <li class="crumb-link text-left" v-if="crumbs[1].name !== null">
                    <span class="dbl-arr">>></span> <img src="img/icon/function.png" />
                    <span class="pma-text">{{ getFunctionCrumbName }}</span>
                </li>
            </ul>
            <ul class="links right">
                <li class="li-filter" v-if="watchActiveCollection">
                    <input class="doc-filter" type="text" v-model="filter" v-on:keyup="filterCollection" placeholder="Document filter">
                    <span v-on:click="clearFilter"><img src="img/prev.png" alt="Clear filter" title="Clear filter"></span>
                </li>
                <li class="nav-coll" v-on:click="collapseDb">
                    <span class="nav-collapse" v-show="collapsed" :title="showLanguage('nav', 'collapse')"><img src="img/sort-asc.svg" alt="Collapse nav" /> </span>
                    <span class="nav-collapse" v-show="!collapsed" :title="showLanguage('nav', 'expand')"><img src="img/sort-desc.svg" alt="Expand nav" /> </span>
                </li>
            </ul>
        </nav>
    </div>
</template>
<script>
    /*
    *   Imports the event bus.
    */
    import { EventBus } from '../../../../event-bus.js';

    /*
    *   Import components for the Databases View
    *   <crumb @loadCrumb="loadCrumb" v-for="(crumb, index) in this.crumbs" :key="index" v-bind:crumb="crumb"></crumb>
    */
    //import Crumb from "./Crumb";

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
        //    Crumb
        },

        /*
        *   Data used with this component
        */
        data() {
            return {
                crumbs: [
                    {
                        type: 'collection',
                        name: null,
                        link: true,
                        option: null
                    },
                    {
                        type: 'function',
                        name: null,
                        link: false,
                        option: null
                    }
                ],
                activeDb: null,
                activeDatabase: null,
                activeColl: null,
                activeCollection: null,
                collapsed: false,
                filter: null,
                filtering: false
            };
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Return the site name from config
            */
            databaseName() {
                return this.activeDb ? this.activeDb : this.activeDatabase;
            },

            collectionName() {
                return this.activeColl ? this.activeColl : this.activeCollection;
            },

            getFunctionCrumbName() {
                return this.crumbs[1].name;
            },

            /*
            *   Handle the collection crumb
            */
            checkActiveCollection() {
               this.activeCollection = this.activeColl = this.$store.getters.getActiveCollection;
            },

            watchActiveCollection() {
                return this.activeCollection;
            },

            /*
            *   Monitor for active database
            */
            checkActiveDatabase() {
                this.activeDatabase = this.activeDb = this.$store.getters.getActiveDatabase;
            },

            showCollection() {
                return (this.activeColl && this.activeCollection);
            }
        },

        /*
        *   Defined methods
        */
        methods: {
            /*
            * Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Load the database from crumbs
            */
            loadDatabase() {
                this.$store.dispatch('setActiveCollection', null);
                // clear the crumbs
                this.crumbs[0].name = null;
                this.crumbs[1].name = null;
                EventBus.$emit('hide-panels');
                EventBus.$emit('show-database', this.activeDb);
            },

            /*
            *   Run a crumb action
            */
            loadCrumb( crumb ) {
                console.log("loading crumb: " + crumb);
            },

            /*
            *   Set the collection crumb
            */
            setCollectionCrumb() {
                this.crumbs[0].name = this.activeCollection;
            },

            /*
             *  This monitors for the active database
             */
            checkDb() {
                if (!this.activeDb || this.activeDb === 'N/A') {
                    this.activeDb = this.activeDatabase;
                    if (!this.activeDb) {
                        // try pulling name from collection data
                        if (this.activeCollection) {
                            let collection = this.$store.getters.getCollection;
                            if (collection) {
                                this.activeDb = collection.collection.databaseName;
                            }
                        }
                    }
                }
            },

            /*
             *  Clear data so that we dont see previous elements
             */
            clearData() {
                this.activeColl = this.activeCollection = this.activeDb = this.activeDatabase = null;
            },

            /*
             *  We want to be able to collapse the Collection control panel
             */
            collapseDb() {
                this.collapsed = !this.collapsed;
                EventBus.$emit('collapse-db', this.collapsed);
            },

            filterCollection() {
                if (this.filter.length >= 1) {
                    this.filtering = true;
                    EventBus.$emit('run-document-filter', this.filter);

                } else {
                    if (this.filtering === true) {
                        EventBus.$emit('clear-document-filter');
                        this.filtering = false;
                    }
                }
            },

            clearFilter() {
                this.filter = null;
                EventBus.$emit('clear-document-filter');
            }
        },

        /*
        * Methods to run when component is mounted
        */
        mounted() {
            EventBus.$on('show-database', (db) => {
                if (db) {
                    this.activeDb = db;
                } else {
                    this.loadDatabase();
                }
            });

            EventBus.$on('show-collection-nav', (collectionName) => {
                this.activeColl = this.activeCollection = collectionName;
            });

            EventBus.$on('show-databases', (db) => {
                this.clearData();
            });

            /* run on mount */
            this.checkDb();
        },

        /*
         *  Was having issues with data hanging around after this was closed
         */
        destroyed() {
            this.clearData();
        },

        /*
        *   Watchers
        */
        watch: {
            /*
             *  When we detect an active collection
             */
            watchActiveCollection() {
                this.setCollectionCrumb();
            },

            /*
             *  There seems to be inconsistencies with the visible data in the store
             */
            checkActiveDatabase() {
                this.checkDb();
            }
        }
    }
</script>
