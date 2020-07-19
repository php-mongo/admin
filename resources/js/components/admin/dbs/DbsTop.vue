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
    <div class="pma-dbs-top">
        <div class="pma-logo text-center">
            <img src="/img/logo-pma.png" :alt="{appTitle}"/>
        </div>
        <div class="pma-icons text-center">
            <span class="pma-link" v-on:click="loadHome()"><img src="/img/icon/pma-home.png" /> <span v-text="showLanguage('dbs', 'host')"></span></span>
            <span class="pma-link" v-on:click="loadOverview()"><img src="/img/icon/pma-world.png" /> <span v-text="showLanguage('dbs', 'overview')"></span></span>
        </div>
    </div>
</template>

<script>
    /*
*  Imports the PHP Mongo Admin URL from the config.
*/
    import { MONGO_CONFIG } from "../../../config";
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    export default {
        /*
          Define the data used by the component.
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
                //return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Refresh all views to default | initial page load
            */
            loadHome() {
            //    console.log("loading home");
                EventBus.$emit('hide-collection-lists');
                EventBus.$emit('close-collection-panels');
                EventBus.$emit('hide-panels');
                // force a reload of the databases
                this.$store.dispatch('loadDatabases');
                //EventBus.$emit('clear-active-nav');
                this.$store.dispatch('setActiveNav', null);
                EventBus.$emit('show-server');
                EventBus.$emit('show-mongo');
            },

            /*
            *   Load the full server overview in the main panel
            */
            loadOverview() {
            //    console.log('loading overview in main panel');
                EventBus.$emit('close-collection-panels');
                EventBus.$emit('hide-panels');
                EventBus.$emit('show-databases');
                this.$store.dispatch('setActiveNav', 'databases');
            }
        }
    }
</script>
