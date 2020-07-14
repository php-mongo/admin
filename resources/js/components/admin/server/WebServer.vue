<style lang="scss">
    /* no style here - inherited from parent */
</style>

<template>
    <div class="server-inner">
       <table class="bordered unstriped" v-if="webServer">
           <tr>
               <th v-text="showLanguage('server', 'webserver')" colspan="2"></th>
           </tr>
           <tr>
               <td class="server-info rb w50" v-text="showLanguage('server', 'server')"></td>
               <td class="server-info">{{ webServer.server }}</td>
           </tr>
           <tr>
               <td class="server-info rb w50" v-text="showLanguage('server', 'version')"></td>
               <td class="server-info" v-html="webServer.phpv.version"></td>
           </tr>
           <tr>
               <td class="server-info rb w50" v-text="showLanguage('server', 'extension')"></td>
               <td class="server-info" v-html="webServer.phpe.extension"></td>
           </tr>
       </table>
    </div>
</template>

<script>
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    export default {
        /*
        *   The component accepts one db as a property
        */
        props: ['webServer'],

        /*
        *   Defined methods for the component
        */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                // return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            }
        }
    }
</script>
