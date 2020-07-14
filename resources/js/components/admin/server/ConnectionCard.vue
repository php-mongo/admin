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
                // return this.$trans( context, key );
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
