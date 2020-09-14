<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      CollectionView.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   CollectionView.vue
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
    .pma-collection-view {
        float: left;
        width: 96%;

        td.rb {
            border-right: 1px solid $lighterGrey;
        }

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
        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 768px) and (max-width: 992px) {
        .pma-collection-view {
            width: 97%;

            .collection-inner .collection-document .doc-nav {
                margin: 0 0 5px 5px;
            }

            .collection-inner .collection-document .doc-data {
                padding: 0 0 5px 5px;
            }

            .collection-inner .collection-document .doc-text {
                padding: 0 0 5px 5px;
            }

            nav.collection-navigation ul.links li span {
                font-size: 10px;
                line-height: 40px;
                padding: .14rem;
            }
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
            showComponent(collection) {
                EventBus.$emit('show-collection-nav', collection );
                this.collection = this.$store.getters.getCollection;
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
            EventBus.$on('hide-panels', ( ) => {
                this.hideComponent();

            });

            /*
            *    Show this component
            */
            EventBus.$on('show-collection', (collection) => {
                this.showComponent(collection);

            });
        }
    }
</script>
