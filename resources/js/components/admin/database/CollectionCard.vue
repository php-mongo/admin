<style lang="scss">

</style>

<template>
    <li class="coll">
        <span class="pma-link" @click="$emit('loadCollection', collection.collection.name)">{{ collection.collection.name }}</span>
    </li>
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
                return this.$store.getters.getLanguageString( context, key );
            }
        }
    }
</script>
