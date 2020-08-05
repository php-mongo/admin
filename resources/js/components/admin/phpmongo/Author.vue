<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Author.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      Author.vue
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
