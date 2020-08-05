<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      PhpMongo.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      PhpMongo.vue
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
    .pma-mongo-view {
        float: left;
        height: auto;
        margin-left: 20px;
        width: 48%;

        .mongo-inner {
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
            table .server-info {
                background-color: $infoBgColor;
                padding: 4px;
            }
            table td p {
                text-align: left;
                margin-bottom: 7px;
            }
            table.bordered th.bb {
                border-bottom: 1px solid $infoColor;
            }
            table.bordered th.rb {
                border-right: 1px solid $infoColor;
            }
            table.bordered td {
                border-bottom: 1px solid $infoColor;
                text-align: left;
            }
            table.bordered td.rb {
                border-right: 1px solid $infoColor;
                min-width: 25%;
                text-align: right !important;
            }
            .server-info {
                .title {
                    display: inline-block;
                    min-width: 90px;
                    text-align: right;
                }
            }
        }
    }
</style>

<template>
    <div id="pma-mongo-view" class="pma-mongo-view align-left" v-show="show">
        <php-mongo-admin v-bind:pma="getComposerData"></php-mongo-admin>
    </div>
</template>

<script>
    /*
    *   Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    /*
    *   Import components for the Gallery View
    */
    import PhpMongoAdmin from "./PhpMongoAdmin";

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            PhpMongoAdmin
        },

        /*
        *   Data required for this component
        */
        data() {
            return {
                show: true
            }
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            /*
            *  Only one to grab here
            */
            getComposerData() {
                return this.$store.getters.getComposerData;
            }
        },

        /*
        *   Define methods for the server component
        */
        methods: {
            /*
            *   Show
            */
            showComponent() {
                this.show = true;
            },

            /*
            *   Hide
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
            EventBus.$on('show-mongo', () => {
                this.showComponent();
            });
        }
    }
</script>
