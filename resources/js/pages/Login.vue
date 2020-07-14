<style lang="scss">
    .about-us {
        p.paragraph {
            margin-bottom: 20px;
        }
    }
</style>

<template>
    <div class="page about-us">
        <div class="grid-container">
            <div class="grid-x grid-padding-x">
                <div class="large-12 medium-12 small-12 cell text-justify">
                    <p>&nbsp;</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../event-bus";

    export default {

        /*
        Sets up the component on the mounted lifecycle hook.
        */
        mounted() {
            /*
            When mounted display login modal.
            */
            setTimeout(function() {
                EventBus.$emit('prompt-login');
            },100);

        },

        methods: {
            /*
            * Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                //return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            }
        }
    }
</script>
