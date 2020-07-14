<style lang="scss">
    /* no style here - inherited from parent */
</style>

<template>
    <div class="php-mongo-author">
        <p><span class="title" v-text="showLanguage('phpmongo', 'name')"></span>: {{ author.name }} </p>
        <p><span class="title" v-text="showLanguage('phpmongo', 'email')"></span>: {{ author.email }} </p>
        <p><span class="title" v-text="showLanguage('phpmongo', 'homepage')"></span>: {{ author.homepage }} </p>
        <p><span class="title" v-text="showLanguage('phpmongo', 'role')"></span>: {{ author.role }} </p>
    </div>
</template>

<script>
    export default {
        /*
        *   Prop for this card
        */
        props: ['author'],

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
