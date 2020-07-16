<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    .crumb-nav-wrapper {
        background-color: $lightGreyColor;
        padding-left: 50px;
    }
    nav.crumb-navigation {
        background-color: $lightGreyColor;
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

                span {
                    font-weight: bold;
                    font-size: 12px;
                    line-height: 33px;
                    color: $white;

                    &:hover {
                        color: $linkColor;
                        text-decoration: underline;
                    }
                }
            }
        }
    }

    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        nav.top-navigation {
            div.text-center {
                a {
                    span.logo {
                        font-size: 20px;
                        padding-top: 8px;
                    }
                }
            }
        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 769px) and (max-width: 992px) {
        nav.top-navigation {
            div.text-center {
                a {
                    span.logo {
                        font-size: 25px;
                        padding-top: 4px;
                    }
                }
            }
        }
    }

    /* Large only - (min-width: 64em) and (max-width: 74.9375em) */
    @media (min-width: 993px) and (max-width: 2048px) {

    }
</style>

<template>
    <div class="database-crumbs">
        <nav class="crumb-navigation">
            <ul class="links">
                <li class="crumb-link text-left">
                    <img src="/img/icon/database.png" /> <span class="pma-link" v-on:click="loadDatabase($event)">{{databaseName}}</span>
                </li>
            </ul>
        </nav>
    </div>
</template>
<script>
    /*
    *   Import the application JS config
    */
    import { MONGO_CONFIG } from "../../../../config.js";

    /*
    *   Imports the event bus.
    */
    import { EventBus } from '../../../../event-bus.js';

    export default {
        /*
        *   Data used with this component
        */
        data() {
            return {
                crumbs: [],
                activeDb: 'N/A'
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

            loadDatabase() {
                console.log("re-loading database view");
                EventBus.$emit('hide-panels');
                EventBus.$emit('show-database', this.activeDb);
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
        }
    }
</script>
