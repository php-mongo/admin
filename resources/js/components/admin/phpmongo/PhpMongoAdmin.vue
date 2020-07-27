<style lang="scss">
    /* no style here - inherited from parent */
</style>

<template>
    <div class="mongo-inner php-mongo-admin" v-if="pma">
       <table class="table bordered">
           <tr>
               <th class="bb" v-text="showLanguage('phpmongo', 'phpmongoadmin')" colspan="2"></th>
           </tr>
           <tr>
               <td class="server-info rb" v-text="showLanguage('phpmongo', 'version')"></td>
               <td class="server-info">{{ pma.version }}</td>
           </tr>
           <tr>
               <td class="server-info rb" v-text="showLanguage('phpmongo', 'description')"></td>
               <td class="server-info">{{ pma.description }}</td>
           </tr>
           <tr>
               <td class="server-info rb" v-text="showLanguage('phpmongo', 'keywords')"></td>
               <td class="server-info">{{ pma.keywords }}</td>
           </tr>
           <tr>
               <td class="server-info rb" v-text="showLanguage('phpmongo', 'authors')"></td>
               <td class="server-info"><author v-for="(author, index) in pma.authors" :key="index" v-bind:author="author"></author></td>
           </tr>
           <tr>
               <td class="server-info rb" v-text="showLanguage('phpmongo', 'support')"></td>
               <td class="server-info">
                   <p><span class="title" v-text="showLanguage('phpmongo', 'docs')"></span>: <a href="{ pma.support.docs }" target="_blank">{{ pma.support.docs }}</a> </p>
                   <p><span class="title" v-text="showLanguage('phpmongo', 'email')"></span>: <a href="{ pma.support.email }" target="_blank">{{ pma.support.email }}</a> </p>
                   <p><span class="title" v-text="showLanguage('phpmongo', 'issues')"></span>: <a href="{ pma.support.issues }" target="_blank">{{ pma.support.issues }}</a> </p>
                   <p><span class="title" v-text="showLanguage('phpmongo', 'source')"></span>: <a href="{ pma.support.source }" target="_blank">{{ pma.support.source }}</a> </p>
                   <p><span class="title" v-text="showLanguage('phpmongo', 'authentication')"></span>: <a href="{ pma.support.authentication }" target="_blank">{{ pma.support.authentication }}</a> </p>
                   <p><span class="title" v-text="showLanguage('phpmongo', 'users')"></span>: <a href="{ pma.support.users }" target="_blank">{{ pma.support.users }}</a> </p>
                   <p><span class="title" v-text="showLanguage('phpmongo', 'configuration')"></span>: <a href="{ pma.support.configuration }" target="_blank">{{ pma.support.configuration }}</a> </p>
               </td>
           </tr>
           <tr>
               <td class="server-info rb" v-text="showLanguage('phpmongo', 'license')"></td>
               <td class="server-info">{{ pma.license }}</td>
           </tr>
       </table>
    </div>
</template>

<script>
    /*
    *   Import components for the Gallery View
    */
    import Author from "./Author";

    export default {
        /*
       *   Register the components to be used by the home page.
       */
        components: {
            Author
        },

        /*
        *   The component accepts one db as a property
        */
        props: ['pma'],

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
