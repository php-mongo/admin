<style lang="scss">
    @import '~@/abstracts/_variables.scss';
    .pma-collection-view {
        float: left;
        width: 96%;

        .collection-inner {
            form {
                margin-bottom: 10px;
            }
            p {
                margin-bottom: 0;
                padding-left: 1px;

                input {
                    line-height: 1.65;
                    margin-top: 1px;
                    vertical-align: top ;
                }

                .button {
                    margin: 0;
                    padding: 0.5em 1em;
                }
            }
            p.drop {
                padding-left: 5px;

                label {
                    display: inline-block;
                }

                span {
                    vertical-align: middle;
                }

                input {
                    margin-right: 0.1rem;
                    vertical-align: sub;
                }

                .pma-link {
                    font-size: 1.1rem;
                    margin-right: 20px;
                    vertical-align: sub;
                }

                button {
                    padding: 0.3em 0.5em;
                }
            }
            table {
                border: 1px solid $infoColor;
                border-radius: 5px;
                box-shadow: 2px 2px 5px $cccColor;
            }
            table th {
                background-color: $tableHeaderBg;
                color: $white;
                font-size: 1.2rem;
                padding: 4px;
            }
            table td {
                background-color: $infoBgColor;
                padding: 4px 4px 4px 8px;
            }
            /*table.bordered td.tr, table.collection td.tr {
                text-align: right;
                width: 50%;
            }
            table.collection td {
                li.coll {
                    margin-left: 20%;
                }
            }
            table.bordered {
                th.bb {
                    border-bottom: 1px solid $infoColor;
                }
                th.rb {
                    border-right: 1px solid $infoColor;
                }
                td {
                    border-bottom: 1px solid $infoColor;
                    text-align: left;
                }
                td.rb, table.collection td.rb {
                    border-right: 1px solid $infoColor;
                }
                td.text-center {
                    text-align: center !important;
                }
                td.vat {
                    vertical-align: top;
                }
            }*/
        }
    }
</style>

<template>
    <div id="pma-collection-view" class="pma-collection-view align-left" v-show="show">
        <database-top-view></database-top-view>
        <collection-card v-bind:collection="getCollection"></collection-card>
    </div>
</template>

<script>
    /*
    *   Import the Event bus
    */
    import { EventBus } from '../../../../event-bus.js';

    /*
    *   Import components for the Databases View
    */
    import DatabaseTopView from "../top/DatabaseTopView";
    import CollectionCard from "./CollectionCard";

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            DatabaseTopView,
            CollectionCard
        },

        /*
        *   Data required for this component
        */
        data() {
            return {
                show: false,
                collection: {}
            }
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            /*
            *  fetch the databases for iteration in the template
            */
            getCollection() {
                return this.$store.getters.getCollection;
            }
        },

        /*
        *   Define methods for the server component
        */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Show component
            */
            showComponent() {
                EventBus.$emit('show-collection-nav');
                this.show = true;
            },

            /*
            *   Hide component
            */
            hideComponent() {
                this.show = false;
            }
        },

        /*
        *    get on ur bikes and ride !!
        */
        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', function( ) {
                this.hideComponent();

            }.bind(this));

            /*
            *    Show this component
            */
            EventBus.$on('show-collection', function() {
                this.showComponent();

            }.bind(this));
        }
    }
</script>
