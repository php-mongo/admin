<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Directives.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      Directives.vue
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
                return this.$store.getters.getLanguageString( context, key );
            }
        }
    }
</script>
