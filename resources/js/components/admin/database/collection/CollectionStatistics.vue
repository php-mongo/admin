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
        ul {
            li {
                border-bottom: 1px solid $darkGreyColor;
                margin-bottom: 5px;
                padding: 2px 0 2px 5px;

                .title {
                    min-width: 175px !important;
                }

                &:hover {
                    background-color: $lightGrey !important;
                }
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
                        <img src="/img/icon/cross-red.png" />
                    </span>
                </div>
                <h3 v-text="showLanguage('collection','collectionStatistics')"></h3>
                <ul>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'namespace')"></span>
                            <span class="data">{{ statistics.ns }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'documentCount')"></span>
                            <span class="data">{{ statistics.count }}</span>
                        </p>
                    </li>
                    <li v-if="statistics.capped === true">
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'documentMax')"></span>
                            <span class="data">{{ statistics.max }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'capped')"></span>
                            <span class="data">{{ statistics.capped }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('database', 'avgObjSize')"></span>
                            <span class="data">{{ statistics.avgObjSize }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'ok')"></span>
                            <span class="data">{{ statistics.ok }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'dataSize')"></span>
                            <span class="data">{{ statistics.size }}</span>
                        </p>
                    </li>
                    <li v-if="statistics.capped === true">
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'size')"></span>
                            <span class="data">{{ statistics.maxSize }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('database', 'storageSize')"></span>
                            <span class="data">{{ statistics.storageSize }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('database', 'indexSize')"></span>
                            <span class="data">{{ statistics.totalIndexSize }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'indexesNumber')"></span>
                            <span class="data">{{ statistics.nindexes }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <strong v-text="showLanguage('collection', 'indexDetails')"></strong>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'indexType')"></span>
                            <span class="data">{{ statistics.indexDetails._id_.type }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'indexUri')"></span>
                            <span class="data">{{ statistics.indexDetails._id_.uri }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'indexCreationString')"></span>
                            <span class="data">{{ statistics.indexDetails._id_.creationString }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'indexSizes')"></span>
                            <span class="data">{{ statistics.indexSizes._id_ }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <strong v-text="showLanguage('collection', 'indexMetadata')"></strong>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'indexMetaFormat')"></span>
                            <span class="data">{{ statistics.indexDetails._id_.metadata.formatVersion }}</span>
                        </p>
                    </li>
                    <li>
                        <p>
                            <span class="title" v-text="showLanguage('collection', 'indexMetaInfo')"></span>
                            <span class="data">{{ statistics.indexDetails._id_.metadata.infoObj }}</span>
                        </p>
                    </li>
                </ul>
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

            getStatistics(data) {
                this.database   = data.db;
                this.collection = data.coll;
                this.statistics = this.$store.getters.getCollectionStats;
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
             * On event, show the collection stats modal
             */
            EventBus.$on('show-document-statistics', ( data ) => {
                this.getStatistics(data);
                this.showComponent();
            });
        }
    }
</script>
