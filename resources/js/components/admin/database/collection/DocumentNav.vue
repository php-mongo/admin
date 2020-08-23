<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DocumentNav.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DocumentNav.vue
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
    /* no style yet */
</style>

<template>
    <div :id="'document-nav-' + index" ref="doc-nav" class="doc-nav" v-if="document">
        <span v-if="document.can_modify"><span class="pma-link" @click="update" v-text="showLanguage('document', 'update')"></span> |</span>
        <span v-if="document.can_delete"><span class="pma-link" @click="deleteDocument" v-text="showLanguage('document', 'delete')"></span></span>
        <span v-if="!document.can_delete"><span class="disabled-link"  v-text="showLanguage('document', 'delete')"></span></span> |
        <span v-if="document.can_add_field"><span class="pma-link" @click="newField" v-text="showLanguage('document', 'newField')"></span></span>
        <span v-if="!document.can_add_field"><span class="disabled-link" v-text="showLanguage('document', 'newField')"></span></span> |
        <span v-if="document.can_duplicate"><span class="pma-link" @click="duplicate" v-text="showLanguage('document', 'duplicate')"></span> |</span>
        <span v-if="document.can_refresh"><span class="pma-link" @click="refresh" v-text="showLanguage('document', 'refresh')"></span> |</span>
        <span class="pma-link" @click="$emit('text', $event.target)" v-text="showLanguage('document', 'text')"></span> |
        <span class="pma-link" @click="$emit('expand', $event.target)" v-text="showLanguage('document', 'expand')"></span>
    </div>
</template>

<script>
    import { EventBus } from '../../../../event-bus.js';

    export default {
        /*
         *  Document nav props
         */
        props: ['document', 'collection', 'index'],

        /*
         * Document navigation methods
         */
        methods: {
            /*
             *   Calls the Translation and Language service
             */
            showLanguage( context, key, str ) {
                if (str) {
                    let string = this.$store.getters.getLanguageString( context, key );
                    return string.replace("%s", str);
                }
                return this.$store.getters.getLanguageString( context, key );
            },

            update() {
                let data = { document: this.document.raw, db: this.collection.databaseName, coll: this.collection.collectionName, index: this.index };
                EventBus.$emit('show-document-update', data );
            },

            deleteDocument() {
                console.log("deleting: " + this.document._id);
                EventBus.$emit('delete-confirmation', {id: this.document._id, element: 'document', notification: this.showLanguage('document', 'deleteConfirm') });
            },

            newField() {
                let data = { document: this.document.raw, db: this.collection.databaseName, coll: this.collection.collectionName, index: this.index };
                EventBus.$emit('show-document-field', data );
            },

            duplicate() {
                let data = { document: this.document.raw, db: this.collection.databaseName, coll: this.collection.collectionName };
                EventBus.$emit('show-document-duplicate', data );
            },

            refresh() {
                let data = { database: this.collection.databaseName, collection: this.collection.collectionName };
                this.$store.dispatch('loadCollection', data);
                setTimeout(function() {
                    EventBus.$emit('document-insert' );
                }, 500);
            }
        }
    }
</script>
