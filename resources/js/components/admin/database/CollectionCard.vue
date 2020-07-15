<style lang="scss">

</style>

<template>
    <p>
        <span class="pma-link">{{ collection.collection.name }}</span>
    </p>
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
        props: ['collection'],


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
