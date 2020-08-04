<style lang="scss">

</style>

<template>
    <li class="coll">
        <span class="pma-link" @click="$emit('loadCollection', collection.collection.name)">{{ getCollectionName }}</span>
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

        computed: {
            getCollectionName() {
                return ((this.collection.collection.name) ? this.collection.collection.name : this.collection.collection.collectionName);
            }
        },

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
