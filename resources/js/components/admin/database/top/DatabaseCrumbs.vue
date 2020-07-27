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
                margin-left: 7px;
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
                    <img src="/img/icon/database.png" /> <span class="crumb pma-link" v-on:click="loadDatabase($event)">{{databaseName}}</span>
                </li>
                <li class="crumb-link text-left" v-if="crumbs[0].name !== null">
                    <span class="dbl-arr">>></span> <img src="/img/icon/collection.png" />
                    <span class="crumb pma-link" v-on:click="loadCrumb( crumb[0].name )">{{ getCollectionCrumbName }}</span>
                </li>
                <li class="crumb-link text-left" v-if="crumbs[1].name !== null">
                    <span class="dbl-arr">>></span> <img src="/img/icon/function.png" />
                    <span class="pma-text">{{ getFunctionCrumbName }}</span>
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
                activeDb: 'N/A',
                activeDatabase: null,
                activeCollection: null
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
                return this.activeDb;
            },

            getCollectionCrumbName() {
                return this.crumbs[0].name;
            },

            getFunctionCrumbName() {
                return this.crumbs[1].name;
            },

            /*
            *   Handle the collection crumb
            */
            checkActiveCollection() {
               this.activeCollection = this.$store.getters.getActiveCollection;
            },

            watchActiveCollection() {
                return this.activeCollection;
            },

            /*setActiveCollection() {
                this.crumbs[0].name = this.activeCollection;
            },*/

            /*
            *   Monitor for active database
            */
            checkActiveDatabase() {
                this.activeDatabase = this.$store.getters.getActiveDatabase;
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
                // return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Load the database from crumbs
            */
            loadDatabase() {
                // console.log("re-loading database view");
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
                this.crumbs[0].name = this.activeCollection;// this.$store.getters.getActiveCollection;
            },

            checkDb() {
                if (!this.activeDb || this.activeDb === 'N/A') {
                    this.activeDb = this.activeDatabase; // this.$store.getters.getActiveDatabase;
                    if (!this.activeDb) {
                        // try pulling name from collection data
                        console.log("cant find activeDb - looking in collection!!!");
                        if (this.activeCollection) {
                            let collection = this.$store.getters.getCollection;
                            if (collection) {
                                console.log("crumbs coll: " + collection);
                                this.activeDb = collection.collection.databaseName;
                            }
                        }
                    }
                }
            }
        },

        /*
        * Methods to run when component is mounted
        */
        mounted() {
            EventBus.$on('show-database', function(db) {
                console.log("db to crumbs: " + db);
                this.activeDb = db;

            }.bind(this));

            this.checkDb();
        },

        watch: {
            watchActiveCollection() {
                this.setCollectionCrumb();
            },

            checkActiveDatabase() {
                this.checkDb();
            }
        }
    }
</script>
