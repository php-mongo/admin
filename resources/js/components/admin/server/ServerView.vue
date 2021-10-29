<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ServerView.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   ServerView.vue
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
    .pma-server-view {
        float: left;
        height: 105vh;
        width: 47%;

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

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 768px) and (max-width: 992px) {
        .pma-server-view {
            float: none;
            height: auto;
            width: calc(93vw - 262px);
        }
    }
</style>

<template>
    <div ref="pmaServerView" id="pma-server-view" class="pma-server-view align-left" v-if="show">
        <div v-show="getServerLoadStatus === 2">
            <command-line v-bind:commandLine="getCommandLine"></command-line>
            <connection-card v-bind:connectionCard="getConnection"></connection-card>
            <web-server v-bind:webServer="getWebServer"></web-server>
            <directives v-bind:directives="getDirectives"></directives>
            <build-info v-bind:buildInfo="getBuildInfo"></build-info>
        </div>
        <div class="float-center text-center" v-show="getServerLoadStatus !== 2 && getServerLoadStatus !== 3">
            <p v-text="showLanguage('global', 'loadingApplication')"></p>
            <p><img src="img/ajax-loader-60x60.gif" :alt="showLanguage('global', 'loading')"></p>
        </div>
        <div class="float-center text-center" v-if="getServerLoadStatus === 3">
            <p v-text="getServerErrorMessage"></p>
        </div>
        <div>
            <p>&nbsp;</p>
        </div>
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
                show: true,
                expanded: false
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
                return this.$store.getters.getBuildInfo
            },

            getCommandLine() {
                return this.$store.getters.getCommandLine
            },

            getConnection() {
                return this.$store.getters.getConnection
            },

            getDirectives() {
                return this.$store.getters.getDirectives
            },

            getWebServer() {
                return this.$store.getters.getWebServer
            },

            getComposerData() {
                return this.$store.getters.getComposerData
            },

            /*
             *  Use the server load status to manage the display output
             */
            getServerLoadStatus() {
                return this.$store.getters.getServerLoadStatus
            },

            getServerErrorMessage() {
                return this.$store.getters.getServerErrorDataMessage
            },
        },

        /*
         *   Define methods for the server component
         */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key )
            },

            watchLeftNav() {
                this.expanded = !this.expanded;
                if (this.expanded === true) {
                    this.$jqf(this.$refs.pmaServerView).css('width', '93vw')
                }
                if (this.expanded === false) {
                    this.$jqf(this.$refs.pmaServerView).css('width', 'calc(93vw - 262px)')
                }
            },

            /*
             *   Show component
             */
            showComponent() {
                this.show = true
            },

            /*
             *   Hide component
             */
            hideComponent() {
                this.show = false
            },
        },

        /*
         *    get on ur bikes and ride !!
         */
        mounted() {
            // load all the default view server data
            this.$store.dispatch( 'loadServer' );

            /*
             *    Hide this component
             */
            EventBus.$on('hide-panels', ( ) => {
                this.hideComponent()
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-server', () => {
                this.showComponent()
            });

            EventBus.$on('collapse-left-nav', () => {
                this.watchLeftNav()
            });

            EventBus.$on('expand-left-nav', () => {
                this.watchLeftNav()
            })
        },

        watch: {
            getServerLoadStatus() {
                if (this.$store.getters.getServerLoadStatus === 2) {
                    // let the application know the we have a connection
                    this.$store.dispatch('setConnectionStatus', true)
                }
            }
        }
    }
</script>
