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
    <nav class="panel-navigation">
        <div class="text-left">
            <ul class="links">
                <li v-bind:class="{active: getActivePanel('databases')}">
                    <span v-on:click="loadPanel('databases', $event)"><img src="/img/icon/databases.png" /> <span v-bind:title="showLanguage('title', 'databasesTitle')" v-text="showLanguage('nav', 'databases')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('execute')}">
                    <span v-on:click="loadPanel('execute', $event)"><img src="/img/icon/json.gif" /> <span v-bind:title="showLanguage('title', 'executeTitle')" v-text="showLanguage('nav', 'execute')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('status')}">
                    <span v-on:click="loadPanel('status', $event)"><img src="/img/icon/detail.png" /> <span v-bind:title="showLanguage('title', 'statusTitle')" v-text="showLanguage('nav', 'status')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('processes')}">
                    <span v-on:click="loadPanel('processes', $event)"><img src="/img/icon/report.png" /> <span v-bind:title="showLanguage('title', 'processesTitle')" v-text="showLanguage('nav', 'processes')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('command')}">
                    <span v-on:click="loadPanel('command', $event)"><img src="/img/icon/s-icon.gif" /> <span v-bind:title="showLanguage('title', 'commandTitle')" v-text="showLanguage('nav', 'command')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('users')}">
                    <span v-on:click="loadPanel('users', $event)"><img src="/img/icon/databases.png" /> <span v-bind:title="showLanguage('title', 'usersTitle')" v-text="showLanguage('nav', 'users')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('servers')}">
                    <span v-on:click="loadPanel('servers', $event)"><img src="/img/icon/servers2.png" /> <span v-bind:title="showLanguage('title', 'serversTitle')" v-text="showLanguage('nav', 'servers')"></span></span>
                </li>
                <li v-bind:class="{active: getActivePanel('master')}">
                    <span v-on:click="loadPanel('master', $event)"><img src="/img/icon/key.png" /> <span v-bind:title="showLanguage('title', 'masterTitle')" v-text="showLanguage('nav', 'master')"></span></span>
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
        *   Data used with this component
        */
        data() {
            return {
                activePanel: null,
                isLoggedIn: null,
                user: {}
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
                return (this.$store.getters.getUserLoadStatus === 2);
            },

            /*
            *   Defines a check that we have aa app member
            */
            isMember() {
                let isMember = this.$cookie.get('app-member');
                return ((isMember && isMember.length >= 3) || this.userLoadStatus);
            },

            activeNav() {
                this.activePanel = this.$store.getters.getActiveNav;
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
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Get the active panel
            */
            getActivePanel: function(panel) {
                return this.activePanel === panel;
            },

            /*
            *   Set the active panel
            */
            setActivePanel: function() {
                this.activePanel = this.$store.getters.getActivePanel;
            },

            /*
            *   Return the logged is state
            */
            userLoggedIn() {
                this.isLoggedIn = this.$store.getters.isLoggedIn;
            },

            /*
            *   Retrieves the User from Vuex
            */
            getUser() {
                this.user = this.$store.getters.getUser;
            },

            /*
            *   Load main panel content via event
            */
            loadPanel( item ) {
                this.$store.dispatch('setActiveNav', item);
            //    this.activePanel = item;
                EventBus.$emit('hide-panels');
                EventBus.$emit('show-' + item);
                console.log("loaded panel item: " + item);
            }
        },

        mounted() {
            this.userLoggedIn();
            this.getUser();

            /*EventBus.$on('clear-active-nav', function() {
                this.activePanel = null;
            }.bind(this));*/
        },

       watch: {
            activeNav() {
                this.setActivePanel()
            }
        }
    }
</script>
