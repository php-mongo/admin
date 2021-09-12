<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ServersView.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   ServersView.vue
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
    /* @import '~@/abstracts/_variables.scss'; */
</style>

<template>
    <div id="pma-execute-view" class="pma-servers-panel align-left" v-if="show">
        <div class="servers-inner">
            <p>Executing JavaScript commands via PHP is now deprecated: PhpMongoAdmin is designed to use MongoDB drivers which no longer supports the use of eval() command.</p>
        </div>
    </div>
</template>

<script>
    /*
     * Import the Event bus
     */
    import { EventBus } from '../../../event-bus.js';

    export default {
        name: "ExecuteView",

        /*
         *   Data required for this component
         */
        data() {
            return {
                show: false,
                server: {},
                form: {
                    host: null,
                    port: 27017,
                    username: null,
                    password: null,
                    password2: null,
                    active: null
                },
                message: null,
                error: null
            }
        },

        /*
         *   Defines the computed properties on the component.
         */
        computed: {
            /*
             *  Get the server configs for the current user
             */
            getServers() {
                return this.$store.getters.getServers;
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
                // load all servers allocated to the current user
                this.$store.dispatch( 'loadServers' );
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
            EventBus.$on('hide-panels', () => {
                this.hideComponent();
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-execute', () => {
                this.showComponent();
            });
        }
    }
</script>
