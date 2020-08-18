<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DbsTop.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DbsTop.vue
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
    .pma-logo {
        padding: 5px 10px;
    }
    .pma-icons span {
        margin: 0 5px;
    }
</style>

<template>
    <div class="pma-dbs-top" v-show="collapsed == false">
        <div class="pma-logo text-center">
            <img src="/img/logo-pma.png" :alt="{appTitle}"/>
        </div>
        <div class="pma-icons text-center">
            <span class="pma-link" v-on:click="loadHome()"><img src="/img/icon/pma-home.png" /> <span v-text="showLanguage('dbs','host')"></span></span>
            <span class="pma-link" v-on:click="loadOverview()"><img src="/img/icon/pma-world.png" /> <span v-text="showLanguage('dbs','overview')"></span></span>
        </div>
    </div>
</template>

<script>
    /*
     *  Imports the config.
     */
    import { MONGO_CONFIG } from "../../../config";
    /*
     *   Import the Event bus
     */
    import { EventBus } from '../../../event-bus.js';

    export default {
        /*
         *  We need single prop for the show and tell
         */
        props: ['collapsed'],

        /*
         *  Define the data used by the component.
         */
        data(){
            return {
                appTitle: MONGO_CONFIG.SITE_FULLNAME
            }
        },

        /*
         * Defines the computed properties on the component.
         */
        computed: {
            /*
             * Retrieves the User Load Status from Vuex
             */
            userLoadStatus() {
                return (this.$store.getters.getUserLoadStatus === 2);
            }
        },

        /*
         *   Defined methods for the component
         */
        methods: {
            /*
             * Calls the Translation and Language service
             */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
             *   Refresh all views to default | initial page load
             */
            loadHome() {
                EventBus.$emit('hide-collection-lists');
                EventBus.$emit('close-collection-panels');
                EventBus.$emit('hide-panels');

                // force a reload of the databases
                this.$store.dispatch('loadDatabases');

                this.$store.dispatch('setActiveNav', null);
                EventBus.$emit('show-server');
                EventBus.$emit('show-mongo');
            },

            /*
             *   Load the full server overview in the main panel
             */
            loadOverview() {
                EventBus.$emit('close-collection-panels');
                EventBus.$emit('hide-panels');
                EventBus.$emit('show-databases');
                this.$store.dispatch('setActiveNav', 'databases');
            }
        }
    }
</script>
