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
                        <img src="/img/icon/cross-red.png" />
                    </span>
                </div>
                <h3 v-text="showLanguage('collection','collectionProperties')"></h3>
                <ul class="properties">
                    <li>
                        <p>
                            <span class="title"><span v-text="showLanguage('collection', 'name')"></span>:</span>
                            <span class="data"><strong>{{ collection }}</strong></span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title header" v-text="showLanguage('collection', 'cappedOptions')"></span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <label for="is-capped" class="title clr-bg" v-text="showLanguage('collection', 'cappedCheck')"></label>
                            <input id="is-capped" type="checkbox" v-model="form.capped" />
                        </p>
                    </li>
                    <li>
                        <p>
                            <label for="coll-size" class="title clr-bg" v-text="showLanguage('collection', 'size')"></label>
                            <input id="coll-size" type="text" v-model="form.size" />
                        </p>
                    </li>
                    <li>
                        <p>
                            <label for="doc-count" class="title clr-bg" v-text="showLanguage('collection', 'documentMax')"></label>
                            <input id="doc-count" type="text" v-model="form.max" />
                        </p>
                    </li>
                </ul>
                <p>
                    <button class="button" v-on:click="saveProperties" v-text="showLanguage('collection', 'save')"></button>
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
                    capped: false,
                    size: null,
                    max: null
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
                this.database   = data.db;
                this.collection = data.coll;
                let statistics = this.$store.getters.getCollectionStats;
                this.form.capped = statistics.capped;
                if (statistics.capped === true) {
                    this.form.size = statistics.maxSize;
                    this.form.max  = statistics.max;
                }
            },

            saveProperties() {
                console.log("save the props..");
                let data = { database: this.database, collection: this.collection, params: this.form };
                this.$store.dispatch('saveCollectionProperties', data);
            },

            handleSaveProperties() {
                let status = this.$store.getters.getCollectionPropertiesStatus;
                console.log("status: " + status);
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    let self = this;
                    setTimeout(function() {
                        self.handleSaveProperties();
                    }, 100);
                }
                else if (status === 2) {
                    // success!
                    this.actionMessage = this.showLanguage('collection', 'propertiesSuccess');
                }
                else if (status === 3) {
                    this.errorMessage = this.showLanguage('collection', 'propertiesError');
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
             * On event, show the collection properties modal
             */
            EventBus.$on('show-document-properties', ( data ) => {
                this.getProperties(data);
                this.showComponent();
            });
        }
    }
</script>
