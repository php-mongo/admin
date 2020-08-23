<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      CollectionNav.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   CollectionNav.vue
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

    nav.collection-navigation {
        min-height: 40px;
        border-top: 1px solid #aaa;

        ul.links {
            display: block;
            float: left;
            list-style: none;
            margin: 0;

            li {
                background-color: $tabBgColor;
                border-right: 2px solid $white;
                border-left: 1px solid $cccColor;
                border-bottom: 1px solid $cccColor;
                display: inline-block;
                float: left;
                list-style-type: none;
                padding: 0 10px;

                span {
                    font-weight: bold;
                    font-size: 12px;
                    line-height: 40px;
                    padding: 8px;
                    color: $tabColor;
                    cursor: pointer;

                    &:hover {
                        color: $bodyFontColor;
                    }
                }

                span.text {
                    color: $black;
                    cursor: help;
                }

                span.underline {
                    text-decoration: underline;
                }

                .active {
                    background-color: $white;
                    border-bottom: 1px solid $white;
                    border-right: 0;
                }

                .hide {
                    display: none;
                }
            }
        }
    }

    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        nav.collection-navigation {
        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 769px) and (max-width: 992px) {
        nav.collection-navigation {
        }
    }

    /* Large only - (min-width: 64em) and (max-width: 74.9375em) */
    @media (min-width: 993px) and (max-width: 2048px) {
        nav.collection-navigation {
        }
    }
</style>

<template>
    <nav class="collection-navigation" v-show="showHide">
        <div class="text-left">
            <ul class="links">
                <li v-bind:class="{active: getActivePanel('query')}">
                    <span>
                        <span class="text" v-text="showLanguage('collection', 'query')" v-bind:title="showLanguage('title', 'queryTitle')"></span>
                        [<span :class="isFormatArray" v-on:click="setFormat( 'array')" v-text="showLanguage('collection', 'array')"></span>
                        |<span :class="isFormatJson" v-on:click="setFormat( 'json' )" v-text="showLanguage('collection', 'json')"></span>]
                    </span>
                </li>
                <li v-bind:class="{active: getActivePanel('history')}">
                    <span v-bind:title="showLanguage('title', 'historyTitle')" v-on:click="loadModal('history')" v-text="showLanguage('collection', 'history')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('refresh')}">
                    <span v-bind:title="showLanguage('title', 'refreshTitle')" v-on:click="runCommand('refresh')" v-text="showLanguage('collection', 'refresh')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('new')}">
                    <span v-bind:title="showLanguage('title', 'insertTitle')" v-on:click="loadModal('new')" v-text="showLanguage('collection', 'insert')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('clear')}">
                    <span v-bind:title="showLanguage('title', 'clearTitle')" v-on:click="runCommand('clear')" v-text="showLanguage('collection', 'clear')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('field')}">
                    <span v-bind:title="showLanguage('title', 'newFieldTitle')" v-on:click="loadModal('field')" v-text="showLanguage('collection', 'newField')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('statistics')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'statisticsTitle')" v-on:click="loadPanel('statistics', $event)" v-text="showLanguage('collection', 'statistics')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('export')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'exportTitle')" v-on:click="loadPanel('export', $event)" v-text="showLanguage('collection', 'export')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('import')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'importTitle')" v-on:click="loadPanel('import', $event)" v-text="showLanguage('collection', 'import')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('properties')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'propertiesTitle')" v-on:click="loadPanel('properties', $event)" v-text="showLanguage('collection', 'properties')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('indexes')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'indexesTitle')" v-on:click="loadPanel('indexes', $event)" v-text="showLanguage('collection', 'indexes')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('rename')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'renameTitle')" v-on:click="loadPanel('rename', $event)" v-text="showLanguage('collection', 'rename')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('duplicate')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'duplicateTitle')" v-on:click="loadPanel('duplicate', $event)" v-text="showLanguage('collection', 'duplicate')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('transfer')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'transferTitle')" v-on:click="loadPanel('transfer', $event)" v-text="showLanguage('collection', 'transfer')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('validate')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'validateTitle')" v-on:click="loadPanel('validate', $event)" v-text="showLanguage('collection', 'validate')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('drop')}">
                    <span class="hide" v-bind:title="showLanguage('title', 'dropCollectionTitle')" v-on:click="loadPanel('drop', $event)" v-text="showLanguage('collection', 'drop')"></span>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
    /*
    *   Imports the event bus.
    */
    import { EventBus } from '../../../../event-bus.js';

    export default {
        /*
         *  Prop to handle the collapse expand
         */
        prop: ['collapsed'],

        /*
        *   Data used with this component
        *
        *   Set the default format to 'json' - it should be provide on the mounted event
        */
        data() {
            return {
                activePanel: null,
                activeFormat: 'json',
                collapsed: false,
                collection: null,
                current: null,
                db: null,
                index: 0,
                limit: 75,
                show: false,
            }
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            // Dr Smith! It does not compute!
            checkCollection() {
                return this.$store.getters.getActiveCollection;
            },

            checkDb() {
                return this.$store.getters.getActiveDatabase;
            },

            isFormatJson() {
                return (this.activeFormat === 'json') ? 'underline' : '';
            },

            isFormatArray() {
                return (this.activeFormat === 'array') ? 'underline' : '';
            },

            /*
             *  Secondary collapse handler
             */
            showHide() {
                return (this.show && !this.collapsed);
            }
        },

        /*
        *   Defined methods
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

            /*
            *   Load database panel content via event
            */
            loadPanel( item ) {
                this.activePanel = item;
                EventBus.$emit('hide-collection-panels');
                EventBus.$emit('show-collection-' + item);
            },

            loadModal( panel ) {
                let data = { document: null, db: this.db, coll: this.collection, index: null };
                EventBus.$emit('show-document-' + panel, data );
            },

            runCommand( command ) {
                let data = null;
                switch(true) {
                    case command === 'refresh':
                        data = { database: this.db, collection: this.collection };
                        this.$store.dispatch('loadCollection', data);
                        setTimeout(function() {
                            EventBus.$emit('document-insert' );
                        }, 500);
                        break;

                    case command === 'clear':
                        data = { notification: this.showLanguage('collection', 'clearConfirm', this.collection), element: 'clear-collection', id: this.collection };
                        EventBus.$emit('delete-confirmation', data);

                    default:
                        console.log("commanding: " + command);
                }
            },

            /*
            *   Get the active panel
            */
            getActivePanel (panel ) {
                return this.activePanel === panel;
            },

            /*
             *  Send the collection documents a message to change their perspective
             */
            setFormat( format ) {
                this.activeFormat = format;
                this.$store.dispatch('setCurrentFormat', format);
            },

            getCollection() {
                this.collection = this.$store.getters.getActiveCollection;
            },

            getDb() {
                this.db = this.$store.getters.getActiveDatabase;
            },

            clearCollection( data ) {
                console.log("clearing collection: " + data);
                if (data && data === this.collection) {
                    let data = { database: this.db, collection: this.collection };
                    this.$store.dispatch('clearCollection', data );
                }
            },

            handleClearCollection() {
                let status = this.$store.getters.getClearCollectionStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index+=1;
                    let self = this;
                    setTimeout(function() {
                        this.handleClearCollection();
                    }, 100);
                }
                else if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('collection', 'clearSuccess', this.collection), timer: 5000 });
                }
                else if (status === 3) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('collection', 'clearError', this.collection), timer: 5000 });
                }
            },

            /*
            *   We only show this navigation when we have an active collection and blah blah
            */
            showNavigation() {
                this.show = true;
            },

            /*
             * Hide and seek
             */
            hideNavigation() {
                this.show = false;
            }
        },

        mounted() {
            EventBus.$on('show-collection-nav', () => {
                this.showNavigation();
            });

            EventBus.$on('show-database-nav', () => {
                this.hideNavigation();
            });

            EventBus.$on('collapse-db', (collapse) => {
                this.collapsed = collapse;
            });

            EventBus.$on('default-query-format', ( format ) => {
                this.activeFormat = format;
            });

            EventBus.$on('confirm-delete-clear-collection', (data) => {
                this.clearCollection(data);
            });

            EventBus.$on('cancel-delete-clear-collection', () => {
                this.clearCollection();
            });
        },

        watch: {
            checkCollection() {
                this.getCollection();
            },

            checkDb() {
                this.getDb();
            }
        }
    }
</script>
