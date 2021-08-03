<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      QueryLogs.vue 1002 3/8/21, 12:23 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   QueryLogs.vue
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
        <div id="panel-modal-logs" class="panel-modal" v-show="show" v-on:click="closeDialogOutside($event)">
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
                <h3 v-text="showLanguage('collection','collectionHistory')"></h3>
                <ul>
                    <li class="log-entry" v-for="(log) in queryLogs" v-bind:log="log">
                        <p class="time">
                            <span>{{ log.time }}</span>
                            <span class="log-link pma-link" v-on:click="sendQuery(log.query)" v-text="showLanguage('collection', 'queryAgain')"></span>
                        </p>
                        <p>{{ log.params }}</p>
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
                limit: 55, // limit the status check iterations
                queryLogs: [],
                show: false
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

            getQueryLogs(data) {
                this.database   = data.db;
                this.collection = data.coll;
                data = {database: this.database, collection: this.collection };
                this.$store.dispatch('getQueryLogs', data);
                this.handleQueryLogs();
            },

            handleQueryLogs() {
                let status = this.$store.getters.getQueryLogsLoadStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleQueryLogs();
                    }, 100);

                }
                else if (status === 2) {
                    // success!
                    this.queryLogs = this.$store.getters.getQueryLogs;
                    if ( this.queryLogs.length > 0) {
                        this.actionMessage = this.showLanguage('collection', 'logsAction', this.queryLogs.length);
                    } else {
                        this.actionMessage = this.showLanguage('collection', 'logsActionEmpty');
                    }
                }
                else if (status === 3) {
                    this.errorMessage = this.showLanguage('collection', 'logsActionError');
                }
            },

            sendQuery( query ) {
                EventBus.$emit('send-query', query);
                setTimeout(() => {
                    this.hideComponent();
                },250);
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
            },

            /*
             * Close on click outside panel modal
             */
            closeDialogOutside( event ) {
                if ($(event.target).is('#panel-modal-logs')) {
                    this.hideComponent();
                }
            }
        },

        /*
          Sets up the component when mounted.
        */
        mounted() {
            /*
             * On event, show the new document field modal
             */
            EventBus.$on('show-document-history', ( data ) => {
                this.getQueryLogs(data);
                this.showComponent();
            });
        }
    }
</script>
