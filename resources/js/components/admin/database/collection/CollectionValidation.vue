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
    @import '~@/abstracts/_variables.scss';
    .panel-modal-inner {
        .validation-data {
            background-color: aquamarine;
            line-height: 1.5rem;
            padding: 1rem;

            .colon {
                padding: 0 5px;
            }
        }
    }
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
                <h3 v-text="showLanguage('collection','collectionValidation')"></h3>
                <p v-if="validation" v-text="showLanguage('collection', 'validateInfo')"></p>
                <div v-if="validation" class="validation-data" v-html="validation"></div>
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
                index: 0,
                limit: 75, // limit the status check iterations
                show: false,
                validation: null
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

            getValidation(data) {
                this.database    = data.db;
                this.collection  = data.coll;
                this.$store.dispatch('validateCollection', {database: data.db, collection: data.coll });
                this.handleValidate();
            },

            handleValidate() {
                let status = this.$store.getters.getCollectionValidationStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleValidate();
                    }, 100);

                }
                else if (status === 2) {
                    // success!
                    this.actionMessage = this.showLanguage('collection', 'validateSuccess');
                    let validation = this.$store.getters.getCollectionValidation;
                    validation = JSON.stringify(validation);
                    this.validation  = this.$convObj().jsonH(validation);
                }
                else if (status === 3) {
                    this.errorMessage = this.showLanguage('collection', 'validateError');
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
             * On event, show the collection validate modal
             */
            EventBus.$on('show-document-validate', ( data ) => {
                this.getValidation(data);
                this.showComponent();
            });
        }
    }
</script>
