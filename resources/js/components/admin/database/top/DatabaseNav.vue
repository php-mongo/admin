<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DatabaseNav.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DatabaseNav.vue
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

    nav.database-navigation {
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

                .hide {
                    display: none;
                }
            }

            li.active {
                background-color: $tableHeaderBg;
                border-bottom: 1px solid $tableHeaderBg;
                color: $white;
            }
        }
    }

    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        nav.database-navigation {
        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 769px) and (max-width: 992px) {
        nav.database-navigation {
        }
    }

    /* Large only - (min-width: 64em) and (max-width: 74.9375em) */
    @media (min-width: 993px) and (max-width: 2048px) {
        nav.database-navigation {
        }
    }
</style>

<template>
    <nav class="database-navigation" v-show="showHide">
        <div class="text-left">
            <ul class="links">
                <li v-bind:class="{active: getActivePanel('database')}">
                    <span v-on:click="showDatabase"><img src="img/icon/databases.png" /> <span v-bind:title="showLanguage('title', 'statisticsTitle')" v-text="showLanguage('database', 'statistics')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('new-collection')}">
                    <span v-on:click="loadPanel('new-collection')"><img src="img/icon/json.gif" /> <span v-bind:title="showLanguage('title', 'newCollectionTitle')" v-text="showLanguage('database', 'newCollection')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('command')}">
                    <span v-on:click="loadPanel('command')"><img src="img/icon/server.png" /> <span v-bind:title="showLanguage('title', 'commandTitle')" v-text="showLanguage('database', 'command')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('execute')}">
                    <span v-on:click="loadPanel('execute')"><img src="img/icon/detail.png" /> <span v-bind:title="showLanguage('title', 'executeTitle')" v-text="showLanguage('database', 'execute')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('transfer')}">
                    <span v-on:click="loadPanel('transfer')"><img src="img/icon/report.png" /> <span v-bind:title="showLanguage('title', 'transferTitle')" v-text="showLanguage('database', 'transfer')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('export')}">
                    <span v-on:click="loadPanel('export')"><img src="img/icon/s-icon.gif" /> <span v-bind:title="showLanguage('title', 'exportTitle')" v-text="showLanguage('database', 'export')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('import')}">
                    <span v-on:click="loadPanel('import')"><img src="img/icon/databases.png" /> <span v-bind:title="showLanguage('title', 'importTitle')" v-text="showLanguage('database', 'import')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('profile')}">
                    <span v-on:click="loadPanel('profile')"><img src="img/icon/edit.png" /> <span v-bind:title="showLanguage('title', 'profileTitle')" v-text="showLanguage('database', 'profile')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('repair')}">
                    <span v-on:click="loadPanel('repair')"><img src="img/icon/connect.png" /> <span v-bind:title="showLanguage('title', 'repairTitle')" v-text="showLanguage('database', 'repair')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('authentication')}">
                    <span v-on:click="loadPanel('authentication')"><img src="img/icon/key.png" /> <span v-bind:title="showLanguage('title', 'authenticationTitle')" v-text="showLanguage('database', 'authentication')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('drop')}">
                    <span v-on:click="loadPanel('drop')"><img src="img/icon/delete.png" /> <span v-bind:title="showLanguage('title', 'dropTitle')" v-text="showLanguage('database', 'drop')"></span></span>
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
        */
        data() {
            return {
                activePanel: 'database',
                activeDb: null,
                current: null,
                show: false,
                collapsed: false
            };
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            // Dr Smith! It does not compute!
            checkDatabase() {
                return this.$store.getters.getActiveDatabase;
            },

            // Dr Smith! It does not compute!
            checkCollection() {
                return !this.$store.getters.getActiveCollection;
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
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            showDatabase() {
                let db = this.getActiveDb();
                this.activePanel = 'database';
                EventBus.$emit('hide-database-panels');
                if (db) {
                    EventBus.$emit('show-database', db);
                } else {
                    EventBus.$emit('show-database', this.activeDb);
                }
            },

            /*
            *   Load database panel content via event
            */
            loadPanel( item ) {
                this.activePanel = item;
                EventBus.$emit('hide-database-panels');
                EventBus.$emit('show-database-' + item);
            },

            /*
            *   Get the active panel
            */
            getActivePanel(panel) {
                return this.activePanel === panel;
            },

            setActivePanel(panel) {
                this.activePanel = panel;
            },

            getActiveDb() {
                this.activeDb = this.$store.getters.getActiveDatabase
            },

            /*
             *  Show or hide this nav -  we have the same for the collections nav
             */
            showNavigation() {
                this.show = true;
            },

            hideNavigation() {
                this.show = false;
            }
        },

        mounted() {
             EventBus.$on('show-database-nav', () => {
                 this.getActiveDb();
                 this.showNavigation();
             });

            EventBus.$on('show-database', () => {
                this.setActivePanel('database');
            });

            EventBus.$on('show-collection-nav', () => {
                this.hideNavigation();
            });

            EventBus.$on('collapse-db', (collapse) => {
                this.collapsed = collapse;
            });

            EventBus.$on('load-database-panel', (data) => {
                if (data.panel === 'database') {
                    this.showDatabase(data.value);
                }
            });
        }
    }
</script>
