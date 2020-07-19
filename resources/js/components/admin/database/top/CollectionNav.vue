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
    <nav class="collection-navigation" v-show="show">
        <div class="text-left">
            <ul class="links">
                <li v-bind:class="{active: getActivePanel('array,json')}">
                    <span v-bind:title="showLanguage('title', 'queryTitle')"><span v-text="showLanguage('collection', 'query')"></span>[<span v-on:click="loadPanel('array', $event)" v-text="showLanguage('collection', 'array')"></span>|<span v-on:click="loadPanel('json', $event)" v-text="showLanguage('collection', 'json')"></span>]</span>
                </li>
                <li v-bind:class="{active: getActivePanel('history')}">
                    <span v-bind:title="showLanguage('title', 'historyTitle')" v-on:click="loadPanel('history', $event)" v-text="showLanguage('collection', 'array')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('refresh')}">
                    <span v-bind:title="showLanguage('title', 'refreshTitle')" v-on:click="loadPanel('refresh', $event)" v-text="showLanguage('collection', 'refresh')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('insert')}">
                    <span v-bind:title="showLanguage('title', 'insertTitle')" v-on:click="loadPanel('insert', $event)" v-text="showLanguage('collection', 'insert')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('clear')}">
                    <span v-bind:title="showLanguage('title', 'clearTitle')" v-on:click="loadPanel('clear', $event)" v-text="showLanguage('collection', 'clear')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('newField')}">
                    <span v-bind:title="showLanguage('title', 'newFieldTitle')" v-on:click="loadPanel('newField', $event)" v-text="showLanguage('collection', 'newField')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('statistics')}">
                    <span v-bind:title="showLanguage('title', 'statisticsTitle')" v-on:click="loadPanel('statistics', $event)" v-text="showLanguage('collection', 'statistics')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('export')}">
                    <span v-bind:title="showLanguage('title', 'exportTitle')" v-on:click="loadPanel('export', $event)" v-text="showLanguage('collection', 'export')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('import')}">
                    <span v-bind:title="showLanguage('title', 'importTitle')" v-on:click="loadPanel('import', $event)" v-text="showLanguage('collection', 'import')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('properties')}">
                    <span v-bind:title="showLanguage('title', 'propertiesTitle')" v-on:click="loadPanel('properties', $event)" v-text="showLanguage('collection', 'properties')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('indexes')}">
                    <span v-bind:title="showLanguage('title', 'indexesTitle')" v-on:click="loadPanel('indexes', $event)" v-text="showLanguage('collection', 'indexes')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('rename')}">
                    <span v-bind:title="showLanguage('title', 'renameTitle')" v-on:click="loadPanel('rename', $event)" v-text="showLanguage('collection', 'rename')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('duplicate')}">
                    <span v-bind:title="showLanguage('title', 'duplicateTitle')" v-on:click="loadPanel('duplicate', $event)" v-text="showLanguage('collection', 'duplicate')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('transfer')}">
                    <span v-bind:title="showLanguage('title', 'transferTitle')" v-on:click="loadPanel('transfer', $event)" v-text="showLanguage('collection', 'transfer')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('validate')}">
                    <span v-bind:title="showLanguage('title', 'validateTitle')" v-on:click="loadPanel('validate', $event)" v-text="showLanguage('collection', 'validate')"></span>
                </li>
                <li v-bind:class="{active: getActivePanel('drop')}">
                    <span v-bind:title="showLanguage('title', 'dropCollectionTitle')" v-on:click="loadPanel('drop', $event)" v-text="showLanguage('collection', 'drop')"></span>
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
                activePanel: null,
                current: null,
                show: false
            };
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            // Dr Smith! It does not compute!
            checkCollection() {
                return this.$store.getters.getActiveCollection;
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

            /*
            *   Load database panel content via event
            */
            loadPanel( item ) {
                this.activePanel = item;
                console.log("loading collection panel item: " + item);
                EventBus.$emit('hide-collection-panels');
                EventBus.$emit('show-collection-' + item);
            },

            /*
            *   Get the active panel
            */
            getActivePanel: function(panel) {
                return this.activePanel === panel;
            },

            /*
            *   We only show this navigation when we have an active collection
            */
            showNavigation() {
                this.show = this.$store.getters.getActiveCollection !== null;
            }
        },

        mounted() {
        //    this.showNavigation()
        },

        watch: {
            checkCollection() {
                this.showNavigation();
            }
        }
    }
</script>
