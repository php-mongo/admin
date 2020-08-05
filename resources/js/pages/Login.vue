<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Login.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      Login.vue
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
