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

                .active {
                    background-color: $white;
                    border-bottom: 1px solid $white;
                    border-right: 0;
                }
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
    <nav class="database-navigation">
        <div class="text-left">
            <ul class="links">
                <li>
                    <span v-on:click="loadPanel('satistics', $event)"><img src="/img/icon/databases.png" /> <span v-bind:title="showLanguage('title', 'databasesTitle')" v-text="showLanguage('database', 'statistics')"></span></span>
                </li>
                <li>
                    <span v-on:click="loadPanel('new-collection', $event)"><img src="/img/icon/json.gif" /> <span v-bind:title="showLanguage('title', 'executeTitle')" v-text="showLanguage('database', 'newCollection')"></span></span>
                </li>
                <li>
                    <span v-on:click="loadPanel('command', $event)"><img src="/img/icon/server.png" /> <span v-bind:title="showLanguage('title', 'serverTitle')" v-text="showLanguage('database', 'command')"></span></span>
                </li>
                <li>
                    <span v-on:click="loadPanel('execute', $event)"><img src="/img/icon/detail.png" /> <span v-bind:title="showLanguage('title', 'statusTitle')" v-text="showLanguage('database', 'execute')"></span></span>
                </li>
                <li>
                    <span v-on:click="loadPanel('transfer', $event)"><img src="/img/icon/report.png" /> <span v-bind:title="showLanguage('title', 'processesTitle')" v-text="showLanguage('database', 'transfer')"></span></span>
                </li>
                <li>
                    <span v-on:click="loadPanel('export', $event)"><img src="/img/icon/s-icon.gif" /> <span v-bind:title="showLanguage('title', 'commandTitle')" v-text="showLanguage('database', 'export')"></span></span>
                </li>
                <li>
                    <span v-on:click="loadPanel('import', $event)"><img src="/img/icon/databases.png" /> <span v-bind:title="showLanguage('title', 'usersTitle')" v-text="showLanguage('database', 'import')"></span></span>
                </li>
                <li>
                    <span v-on:click="loadPanel('profile', $event)"><img src="/img/icon/key.png" /> <span v-bind:title="showLanguage('title', 'masterTitle')" v-text="showLanguage('database', 'profile')"></span></span>
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
        *   Data used with this component
        */
        data() {
            return {
                current: null
            };
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {

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

            /*
            *   Load database panel content via event
            */
            loadPanel( item ) {
                console.log("loading database panel item: " + item);
                EventBus.$emit('hide-database-panels');
                EventBus.$emit('show-database-' + item);
            }
        },

        mounted() {

        }
    }
</script>
