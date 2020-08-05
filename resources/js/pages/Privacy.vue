<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Privacy.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      Privacy.vue
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

</style>

<template>
    <div class="page privacy">
        <div class="grid-container">
            <div class="grid-x grid-padding-x">
                <div class="col-sm-8 text-justify">

                    <p class="text-info" v-html="showLanguage('privacy', 'intro')"></p>
                    <p class="text-info" v-html="showLanguage('privacy', 'intro2')"></p>

                    <p v-html="showLanguage('privacy', 'p1')"></p>
                    <p v-html="showLanguage('privacy', '111p2')"></p>

                    <h3 v-html="showLanguage('privacy', 't3')"></h3>
                    <p v-html="showLanguage('privacy', 'p3')"></p>
                    <p v-html="showLanguage('privacy', 'p4')"></p>

                    <h3 v-html="showLanguage('privacy', 't5')"></h3>
                    <p v-html="showLanguage('privacy', 'p5')"></p>

                    <h3 v-html="showLanguage('privacy', 't6')"></h3>
                    <p v-html="showLanguage('privacy', 'p6')"></p>

                    <h3 v-html="showLanguage('privacy', 't7')"></h3>
                    <p v-html="showLanguage('privacy', 'p7')"></p>

                    <h3 v-html="showLanguage('privacy', 't8')"></h3>
                    <p v-html="showLanguage('privacy', 'p8')"></p>

                    <h3 v-html="showLanguage('privacy', 't9')"></h3>
                    <p v-html="showLanguage('privacy', 'p9')"></p>

                    <h3 v-html="showLanguage('privacy', 't10')"></h3>
                    <p v-html="showLanguage('privacy', 'p10')"></p>

                    <h3 v-html="showLanguage('privacy', 't11')"></h3>
                    <p v-html="showLanguage('privacy', 'p11')"></p>

                    <h3 v-html="showLanguage('privacy', 't12')"></h3>
                    <p v-html="showLanguage('privacy', 'p12')"></p>

                    <h3 v-html="showLanguage('privacy', 't13')"></h3>
                    <p v-html="showLanguage('privacy', 'p13')"></p>

                </div>
                <div class="col-sm-4 text-right">
                    <p><img class="img-responsive" alt="Privacy Policy Image" src="img/privacy-policy-image.jpg" /></p>
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
