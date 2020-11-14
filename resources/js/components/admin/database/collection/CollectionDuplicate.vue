<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DocumentDuplicate.vue 1001 8/8/20, 10:23 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DocumentDuplicate.vue
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
    /* @import '~@/abstracts/_variables.scss'; */
</style>

<template>
    <transition name="slide-in-top">
        <div class="panel-modal" v-if="show">
            <div class="panel-modal-inner">
                <div class="modal-header">
                    <span class="msg" v-show="errorMessage || actionMessage">
                        <span class="error">{{ errorMessage }}</span>
                        <span class="action">{{ actionMessage }}</span>
                    </span>
                    <span class="close u-pull-right" v-on:click="hideComponent">
                        <img src="img/icon/cross-red.png" />
                    </span>
                </div>
                <h3 v-text="showLanguage('collection','collectionDuplicate')"></h3>
                <ul class="properties">
                    <li>
                        <p>
                            <span class="title"><span v-text="showLanguage('collection', 'duplicating')"></span>:</span>
                            <span class="data"><strong>{{ collection }}</strong></span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <label for="name" class="title clr-bg" v-text="showLanguage('collection', 'duplicateName')"></label>
                            <input id="name" type="text" v-model="form.duplicateName" />
                        </p>
                    </li>
                    <li>
                        <p>
                            <label for="overwrite" class="title clr-bg" v-text="showLanguage('collection', 'overwrite')"></label>
                            <input id="overwrite" type="checkbox" v-model="form.overwrite" />
                        </p>
                    </li>
                    <li>
                        <p>
                            <label for="indexes" class="title clr-bg" v-text="showLanguage('collection', 'duplicateIndexes')"></label>
                            <input id="indexes" type="checkbox" v-model="form.indexes" />
                        </p>
                    </li>
                </ul>
                <p>
                    <button class="button" v-on:click="saveDuplicate" v-text="showLanguage('collection', 'duplicate')"></button>
                    <button class="button warning" v-on:click="hideComponent" v-text="showLanguage('collection', 'cancel')"></button>
                </p>
            </div>
        </div>
    </transition>
</template>

<script>
    /*
     * Imports the Event Bus to pass events on tag updates
     */
    import { EventBus } from '../../../../event-bus.js';

    export default {
        /*
         *  Defines the data required by this component.
         */
        data() {
            return {
                actionMessage: null,
                collection: null,
                database: null,
                errorMessage: null,
                form: {
                    duplicateName: this.collection + "_copy",
                    overwrite: false,
                    indexes: false
                },
                index: 0,
                limit: 75, // limit the status check iterations
                show: false,
                statistics: {}
            }
        },

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

            getProperties(data) {
                this.errorMessage  = null;
                this.actionMessage = null;
                this.database   = data.db;
                this.collection = data.coll;
                this.form = {
                    duplicateName: this.collection + "_copy",
                    overwrite: false,
                    indexes: false
                };
            },

            saveDuplicate() {
                let data = { database: this.database, collection: this.collection, params: this.form };
                this.$store.dispatch('duplicateCollection', data);
                this.handleDuplicate();
            },

            handleDuplicate() {
                let status = this.$store.getters.getCollectionDuplicateStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    let self = this;
                    setTimeout(function() {
                        self.handleDuplicate();
                    }, 100);
                }
                else if (status === 2) {
                    // success!
                    this.actionMessage = this.showLanguage('collection', 'duplicateSuccess', this.form.duplicateName);
                    setTimeout(() => {
                        EventBus.$emit('hide-panels');
                        EventBus.$emit('show-collection', this.form.duplicateName );
                        this.hideComponent();
                    }, 2000);
                }
                else if (status === 3) {
                    this.errorMessage = this.showLanguage('collection', 'duplicateError');
                }
            },

            /*
             *   Show component
             */
            showComponent() {
                this.show = true;
            },

            /*
             *   Hide component
             */
            hideComponent() {
                this.show = false;
            }
        },

        /*
          Sets up the component when mounted.
        */
        mounted() {
            /*
             * On event, show the collection duplicate modal
             */
            EventBus.$on('show-document-coll-duplicate', ( data ) => {
                this.getProperties(data);
                this.showComponent();
            });
        }
    }
</script>
