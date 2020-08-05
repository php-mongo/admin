<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      BuildInfo.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      BuildInfo.vue
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
               <th v-text="showLanguage('server', 'buildinfo')" colspan="2"></th>
           </tr>
           <build-card v-for="(value, index) in buildInfo" :key="index" v-bind:value="value" v-bind:index="index"></build-card>
       </table>
    </div>
</template>

<script>
    /*
    *   Only one import here
    */
    import BuildCard from "./BuildCard";

    export default {
        /*
         *   Register the components to be used by the home page. // :key="(keyIndex++)"
         */
        components: {
            BuildCard
        },
        /*
         *   The component accepts one db as a property
         */
        props: ['buildInfo'],

        data() {
            return {
                keyIndex: 0
            }
        },

        /*
         *   Defined methods for this component
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
