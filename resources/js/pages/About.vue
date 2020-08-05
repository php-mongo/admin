<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      About.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      About.vue
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
                <div class="large-8 medium-8 small-12 cell text-justify">
                    <u><h4 v-text="showLanguage('about', 'heading1')"></h4></u>
                    <p><strong v-text="showLanguage('global', 'definition')"></strong> <span v-html="showLanguage('about', 'definition1')"></span></p>
                    <u><h4 v-text="showLanguage('about', 'heading2')"></h4></u>
                    <p><strong v-text="showLanguage('global', 'definition')"></strong></p>
                    <p><strong>1</strong> <span v-html="showLanguage('about', 'definition2')"></span></p>
                    <p><strong>2</strong> <span v-html="showLanguage('about', 'definition3')"></span></p>
                    <p class="text-info" v-html="showLanguage('about', 'intro')"></p>
                    <p class="text-info" v-html="showLanguage('about', 'intro2')"></p>
                    <p class="paragraph" v-text="showLanguage('about', 'p1')"></p>
                    <p class="paragraph" v-text="showLanguage('about', 'p3')"></p>
                    <p class="paragraph" v-text="showLanguage('about', 'p4')"></p>
                    <p class="paragraph" v-text="showLanguage('about', 'p5')"></p>
                    <p class="paragraph" v-text="showLanguage('about', '2')"></p>
                </div>
                <div class="large-4 medium-4 hide-for-small-only right">
                    <p><img class="img-responsive" alt="About PHP Mongo Admin Image" src="img/about-image.jpg" /></p>
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
