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
                // return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            }
        }
    }
</script>
