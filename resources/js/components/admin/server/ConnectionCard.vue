<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ConnectionCard.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      ConnectionCard.vue
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
    <div class="server-inner" v-if="connectionCard">
       <table class="bordered unstriped">
           <tr>
               <th v-text="showLanguage('server', 'connection')" colspan="2"></th>
           </tr>
           <tr>
               <td class="server-info rb w50" v-text="showLanguage('server', 'host')"></td>
               <td class="server-info" v-text="getValue('Host')"></td>
           </tr>
           <tr>
               <td class="server-info rb w50" v-text="showLanguage('server', 'port')"></td>
               <td class="server-info" v-text="getValue('Port')">}</td>
           </tr>
           <tr>
               <td class="server-info rb w50" v-text="showLanguage('server', 'username')"></td>
               <td class="server-info" v-text="getValue('Username')"></td>
           </tr>
           <tr>
               <td class="server-info rb w50" v-text="showLanguage('server', 'password')"></td>
               <td class="server-info" v-text="getValue('Password')"></td>
           </tr>
       </table>
    </div>
</template>

<script>
    export default {
        /*
        *   The component accepts one db as a property
        */
        props: ['connectionCard'],

        /*
        *   Defined methods for the component
        */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Added this method to try and solve a 'this.connectionCard' not found error
            *   Turn out wrapping the <template> inner with a v-if fixed the problem - the data was taking to long to return
            */
            getValue: function(key)  {
                return this.$props.connectionCard[key];
            }
        }
    }
</script>
