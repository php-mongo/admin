<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      NewCollection.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   NewCollection.vue
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
    .pma-new-collection {
        float: left;
        width: 84.5vw;

        .new-collection-inner {

            table {
                border: none;

                th {
                    height: 60px;
                    padding-right: 12px !important;
                    text-align: right;
                }

                th.title {
                    height: auto;
                    padding: 10px 0;
                    text-align: center;
                }

                td {
                    padding-left: 12px !important;
                }

                .row {
                    padding-left: 18vw !important;
                }
            }

            .coll-help-link {
                margin-right: 10rem;
            }

            .coll-capped-help {
                margin-left: 5rem;
            }

            button {
                margin: 0 10px 0 0;
            }
        }
    }
</style>

<template>
    <div id="pma-new-collection" class="pma-new-collection align-left" v-show="show">
        <div class="new-collection-inner">
            <table class="bordered unstriped">
                <tr>
                    <th class="title bb" colspan="2" v-text="showLanguage('collection', 'create')"></th>
                </tr>
                <tr>
                    <th v-text="showLanguage('collection', 'name')"></th>
                    <td><input type="text" v-model="form.name" autofocus="autofocus" :placeholder="showLanguage('collection', 'placeholderName')"></td>
                </tr>
                <tr>
                    <td class="row" colspan="2">
                        <strong v-text="showLanguage('collection', 'options')"></strong>
                        <span class="coll-help-link u-pull-right">
                            <a target="_blank" href="https://docs.mongodb.com/php-library/v1.2/reference/method/MongoDBDatabase-createCollection/">docs.mongodb.com/php-library/v1.2/reference/method/MongoDBDatabase-createCollection/</a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th class="bb" v-text="showLanguage('collection', 'capped')"></th>
                    <td><input type="checkbox" v-model="form.capped" > <span class="coll-capped-help" v-text="showLanguage('collection', 'cappedHelp')"></span></td>
                </tr>
                <tr>
                    <th class="bb" v-text="showLanguage('collection', 'size')"></th>
                    <td><input type="text" v-model="form.size" :placeholder="showLanguage('collection', 'placeholderSize')"> <span v-text="showLanguage('collection', 'bytes')"></span></td>
                </tr>
                <tr>
                    <th v-text="showLanguage('collection', 'count')"></th>
                    <td><input type="text" v-model="form.count" :placeholder="showLanguage('collection', 'placeholderCount')"> <span v-text="showLanguage('collection', 'documents')"></span></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="u-pull-left" v-show="message | error" :class="getClass"></p>
                        <button class="button u-pull-right" v-on:click="createCollection" v-text="showLanguage('collection', 'createButton')"></button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        /*
         *  This component requires these data elements
         */
        data() {
            return {
                show: false,
                form: {
                    name: null,
                    capped: false,
                    size: 1,
                    count: 1,
                    db: null
                },
                skel: {
                    name: null,
                    capped: false,
                    size: 1,
                    count: 1,
                    db: null
                },
                message: null,
                error: null,
                indexes: 0
            }
        },

        computed: {
            getActiveDatabase() {
                return this.$store.getters.getActiveDatabase;
            },

            getClass() {
                if (this.error) {
                    return 'warning callout';
                }
                if (this.message) {
                    return 'primary callout'
                }
            }
        },

        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key, str ) {
                if (str) {
                    return this.$store.getters.getLanguageString( context, key ).replace("%s", str);
                }
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Show component
            */
            showComponent() {
                this.setActiveDatabase();
                this.show = true;
            },

            /*
            *   Hide component
            */
            hideComponent() {
                this.show = false;
            },

            /*
             * Send the form data to the store
             */
            createCollection() {
                if (this.form.name) {
                    this.$store.dispatch('createCollection', this.form );
                    this.handleCreateStatus();

                } else {
                    this.error = this.showLanguage('collection', 'error');
                }
            },

            /*
             * Monitor the creation process: "let there be light!!"s
             */
            handleCreateStatus() {
                let status = this.$store.getters.getCreateCollectionStatus;
                this.indexes+=1;
                if (status === 0) {
                    this.error = 'Error: collection create status = 0';
                }
                if (status === 1 && this.indexes < 100) {
                    setTimeout(() => {
                        let self = this;
                        self.handleCreateStatus();
                    }, 50);
                }
                else if (status === 2) {
                    // all good
                    EventBus.$emit('show-success', { notification: this.showLanguage('collection', 'createSuccess', this.form.name) });
                    let db = this.form.db;
                    this.form = this.skel;
                    setTimeout( () => {
                        this.show = false;
                        EventBus.$emit('load-database-panel', { panel: 'database', value: db });
                    }, 1000);
                }
                else if (status === 3) {
                    // opps
                    EventBus.$emit('show-error', { notification: this.showLanguage('collection', 'createError', this.form.name) });
                    this.error = 'An error has occurred...';
                }
            },

            setActiveDatabase() {
                this.form.db = this.$store.getters.getActiveDatabase;
            }
        },

        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent();

            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels',() => {
                this.hideComponent();

            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-new-collection', () => {
                this.showComponent();

            });
        },

        watch: {
            getActiveDatabase() {
                this.setActiveDatabase();
            }
        }
    }
</script>
