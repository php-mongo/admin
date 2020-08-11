<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Terms.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Terms.vue
  - @link         https://github.com/php-mongo/admin PHP MongoDB Admin
  - @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
  - @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
  - @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
  -  php-mongo-admin - License conditions:
  -  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
  -  This web application is available as Free Software and has no implied warranty or guarantee of usability.
  -  See licence.txt for the complete licensing outline.
  -  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
  -  See COPYRIGHT.php for copyright notices and further details.
  -->

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
                    <p><img class="img-responsive" alt="Terms and conditions Image" src="/img/php-mongo-admin.jpg" /></p>
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
