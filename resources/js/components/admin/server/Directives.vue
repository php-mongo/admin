<style lang="scss">
    /* no style here - inherited from parent */
</style>

<template>
    <div class="server-inner">
       <table class="bordered unstriped">
           <tr>
               <th v-text="showLanguage('server', 'directives')" colspan="4" class="bb"></th>
           </tr>
           <tr>
               <th v-text="showLanguage('server', 'directive')" class="rb" style="width:25%"></th>
               <th v-text="showLanguage('server', 'global')" class="rb" style="width:25%"></th>
               <th v-text="showLanguage('server', 'local')" class="rb"></th>
               <th v-text="showLanguage('server', 'access')"></th>
           </tr>
           <directive v-for="(directive, index) in directives" :key="index" v-bind:directive="directive"></directive>

       </table>
    </div>
</template>

<script>
    import Directive from "./Directive";
    import CommandLine from "./CommandLine";
    import Connection from "./ConnectionCard";
    import WebServer from "./WebServer";

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            Directive
        },

        /*
        *   The component accepts one db as a property
        */
        props: ['directives'],

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
