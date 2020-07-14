<style lang="scss">
    .terms {
        p.paragraph {
            margin-bottom: 20px;
        }
    }
</style>

<template>
    <div class="page terms">
        <div class="grid-container">
            <div class="grid-x grid-padding-x">
                <div class="large-8 medium-8 small-12 cell text-justify">
                    <p class="text-info" v-text="showLanguage('terms', 'intro')"></p>
                    <p class="text-info" v-text="showLanguage('terms', 'intro2')"></p>
                    <p class="paragraph" v-text="showLanguage('terms', 'p1')"></p>
                    <p class="paragraph" v-text="showLanguage('terms', 'p2')"></p>
                    <p class="paragraph" v-text="showLanguage('terms', 'p3')"></p>
                    <p class="paragraph" v-text="showLanguage('terms', 'p4')"></p>
                    <p class="paragraph" v-text="showLanguage('terms', 'p5')"></p>
                    <p class="paragraph" v-text="showLanguage('terms', 'p6')"></p>
                </div>
                <div class="large-4 medium-4 hide-for-small-only cell text-right">
                    <p><img class="img-responsive" alt="Terms and conditions Image" src="img/terms-and-conditions-image.jpg" /></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        methods: {
            /*
            * Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                //return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },
        }
    }
</script>
