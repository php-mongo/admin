<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ServerView.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      ServerView.vue
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
    .pma-server-view {
        float: left;
        height: 105vh;
        width: 48%;

        .server-inner {
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
                text-align: center;
            }
            table.bordered th.bb {
                border-bottom: 1px solid $infoColor;
            }
            table.bordered th.rb {
                border-right: 1px solid $infoColor;
            }
            table.bordered td {
                border-bottom: 1px solid $infoColor;
                min-width: 100px;
                text-align: left;
            }
            table.bordered td.w50 {
                min-width: 49.9%;
                text-align: right;
            }
            table.bordered td.title {
               width: 15rem;
            }
            table.bordered td.rb {
                border-right: 1px solid $infoColor;
            }
        }
    }
</style>

<template>
    <div id="pma-server-view" class="pma-server-view align-left" v-show="show">
        <command-line v-bind:commandLine="getCommandLine"></command-line>
        <connection-card v-bind:connectionCard="getConnection"></connection-card>
        <web-server v-bind:webServer="getWebServer"></web-server>
        <directives v-bind:directives="getDirectives"></directives>
        <build-info v-bind:buildInfo="getBuildInfo"></build-info>
    </div>
</template>

<script>
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    /*
    *   Import components for the Server View
    */
    import CommandLine from "./CommandLine";
    import ConnectionCard from "./ConnectionCard";
    import WebServer from "./WebServer";
    import Directives from "./Directives";
    import BuildInfo from "./BuildInfo";

    export default {
        /*
         *   Register the components to be used by the home page.
         */
        components: {
            CommandLine,
            ConnectionCard,
            WebServer,
            Directives,
            BuildInfo
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
             *  Because of the complex variations of each child component it seemed a better idea to call each object individually
             */
            getBuildInfo() {
                return this.$store.getters.getBuildInfo;
            },

            getCommandLine() {
                return this.$store.getters.getCommandLine;
            },

            getConnection() {
                return this.$store.getters.getConnection;
            },

            getDirectives() {
                return this.$store.getters.getDirectives;
            },

            getWebServer() {
                return this.$store.getters.getWebServer;
            },

            getComposerData() {
                return this.$store.getters.getComposerData;
            }
        },

        /*
         *   Define methods for the server component
         */
        methods: {
            /*
             *   Show component
             */
            showComponent() {
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
            // load al the default view server data
            this.$store.dispatch( 'loadServer' );

            /*
             *    Hide this component
             */
            EventBus.$on('hide-panels', ( ) => {
                this.hideComponent();
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-server', () => {
                this.showComponent();
            });
        }
    }
</script>
