<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      CollectionCard.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   CollectionCard.vue
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

</style>

<template>
    <li class="coll" v-if="checkCollection">
        <input type="checkbox" v-model="checked" />
        <span class="pma-link" @click="$emit('loadCollection', getCollectionName )">{{ getCollectionName }}</span>
        <span class="obj-count u-pull-right">{{ getObjectsCount }}</span>
    </li>
</template>

<script>
    export default {
        /*
        *   The component accepts one db as a property
        */
        props: ['collection'],

        data() {
            return {
                checked: false
            }
        },

        /*
         *  Dr Smith! It does not compute!
         */
        computed: {
            /*
             *  We ave 2 variations on collection name
             */
            getCollectionName() {
                return this.collection.collection.name ? this.collection.collection.name : this.collection.collection.collectionName
            },

            /*
             *  Prevent errors when there are no objects
             */
            getObjectsCount() {
                return this.collection.objects ? this.collection.objects.count : 0
            },

            /*
             *  Only show id true
             */
            checkCollection() {
                return (this.collection)
            }
        },

        /*
        *   Defined methods for the component
        */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage(context, key) {
                return this.$store.getters.getLanguageString(context, key)
            }
        }
    }
</script>
