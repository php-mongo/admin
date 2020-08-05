<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      CollectionCard.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      CollectionCard.vue
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
    <li class="coll">
        <span class="pma-link" @click="$emit('loadCollection', getCollectionName )">{{ getCollectionName }}</span>
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
