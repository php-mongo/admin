<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      TopNav.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   TopNav.vue
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

    nav.panel-navigation {
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
                background-color: $white;
                border-bottom: 1px solid $white;
                border-right: 0;
            }
        }
    }

    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        nav.panel-navigation {
        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 769px) and (max-width: 992px) {
        nav.panel-navigation {
        }
    }

    /* Large only - (min-width: 64em) and (max-width: 74.9375em) */
    @media (min-width: 993px) and (max-width: 2048px) {
        nav.panel-navigation {
        }
    }
</style>

<template>
    <nav ref="navPanel" class="panel-navigation" v-show="!collapsed">
        <div class="text-left">
            <ul class="links">
                <li v-bind:class="{active: getActivePanel('databases')}">
                    <span v-on:click="loadPanel('databases', $event)"><img src="img/icon/databases.png" /> <span v-bind:title="showLanguage('title', 'databasesTitle')" v-text="showLanguage('nav', 'databases')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('servers')}">
                    <span v-on:click="loadPanel('servers', $event)"><img src="img/icon/servers2.png" /> <span v-bind:title="showLanguage('title', 'serversTitle')" v-text="showLanguage('nav', 'servers')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('execute')}" v-if="isRootUser || isAdminUser">
                    <span v-on:click="loadPanel('execute', $event)"><img src="img/icon/json.gif" /> <span v-bind:title="showLanguage('title', 'executeTitle')" v-text="showLanguage('nav', 'execute')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('status')}" v-if="isRootUser || isAdminUser">
                    <span v-on:click="loadPanel('status', $event)"><img src="img/icon/detail.png" /> <span v-bind:title="showLanguage('title', 'statusTitle')" v-text="showLanguage('nav', 'status')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('processes')}" v-if="isRootUser || isAdminUser">
                    <span v-on:click="loadPanel('processes', $event)"><img src="img/icon/report.png" /> <span v-bind:title="showLanguage('title', 'processesTitle')" v-text="showLanguage('nav', 'processes')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('command')}">
                    <span v-on:click="loadPanel('command', $event)"><img src="img/icon/s-icon.gif" /> <span v-bind:title="showLanguage('title', 'commandTitle')" v-text="showLanguage('nav', 'command')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('users')}" v-if="isRootUser || isAdminUser || isUserAdmin">
                    <span v-on:click="loadPanel('users', $event)"><img src="img/icon/databases.png" /> <span v-bind:title="showLanguage('title', 'usersTitle')" v-text="showLanguage('nav', 'users')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('master')}" v-if="isRootUser || isAdminUser">
                    <span v-on:click="loadPanel('master', $event)"><img src="img/icon/key.png" /> <span v-bind:title="showLanguage('title', 'masterTitle')" v-text="showLanguage('nav', 'master')"></span></span>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
    /*
    *   Imports the event bus.
    */
    import { EventBus } from '../../../event-bus.js';

    export default {
        /*
         *  Its a one prop shop
         */
        props: ['collapsed','user'],

        /*
        *   Data used with this component
        */
        data() {
            return {
                activePanel: null,
            };
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Retrieves the User Load Status from Vuex
            */
            userLoadStatus() {
                return (this.$store.getters.getUserLoadStatus === 2)
            },

            /*
            *   Defines a check that we have aa app member
            */
            isMember() {
                let isMember = this.$cookies.get('app-member');
                return ((isMember && isMember.length >= 3) || this.userLoadStatus)
            },

            /*
             *  Monitor the active Nav
             */
            activeNav() {
                return this.$store.getters.getActiveNav
            },

            isRootUser() {
                return (this.user.user_role && this.user.user_role.hasRoot === true)
            },

            isAdminUser() {
                return (this.user.admin_user && this.user.admin_user === "1")
            },

            isUserAdmin() {
                return this.$store.getters.canUserAdminUsers;
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
                // return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key )
            },

            /*
            *   Get the active panel
            */
            getActivePanel: function(panel) {
                return this.activePanel === panel
            },

            /*
            *   Set the active panel
            */
            setActivePanel: function() {
                this.activePanel = this.$store.getters.getActivePanel
            },

            /*
            *   Load main panel content via event
            */
            loadPanel( item ) {
                this.$store.dispatch('setActiveNav', item);
                EventBus.$emit('hide-panels');
                EventBus.$emit('show-' + item)
            }
        },

       watch: {
            activeNav() {
                this.setActivePanel()
            }
        }
    }
</script>
