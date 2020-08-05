<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      CollectionNav.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      CollectionNav.vue
  - @subpackage   Id
  - @link         https://github.com/php-mongo/admin PHP MongoDB Admin
  - @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
  - @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
  - @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
  -  php-mongo-admin - License conditions:
  -  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
  -  This web application is available as Free Software and has no implied warranty or guarantee of usability.
  -  See licence.txt for the complete licensing outline.
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
                <li v-bind:class="{active: getActivePanel('query')}">
                    <span>
                        <span class="text" v-text="showLanguage('collection', 'query')" v-bind:title="showLanguage('title', 'queryTitle')"></span>
                        [<span :class="isFormatArray" v-on:click="setFormat( 'array')" v-text="showLanguage('collection', 'array')"></span>
                        |<span :class="isFormatJson" v-on:click="setFormat( 'json' )" v-text="showLanguage('collection', 'json')"></span>]
                    </span>
                </li>
                <li v-bind:class="{active: getActivePanel('history')}">
                    <span v-bind:title="showLanguage('title', 'historyTitle')" v-on:click="loadPanel('history', $event)" v-text="showLanguage('collection', 'history')"></span>
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
        *
        *   Set the default format to 'json' - it should be provide on the mounted event
        */
        data() {
            return {
                activePanel: null,
                activeFormat: 'json',
                current: null,
                show: false
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

            isFormatJson() {
                return (this.activeFormat === 'json') ? 'underline' : '';
            },

            isFormatArray() {
                return (this.activeFormat === 'array') ? 'underline' : '';
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
                EventBus.$emit('hide-collection-panels');
                EventBus.$emit('show-collection-' + item);
            },

            /*
            *   Get the active panel
            */
            getActivePanel (panel ) {
                return this.activePanel === panel;
            },

            setFormat( format ) {
                this.activeFormat = format;
                EventBus.$emit('set-query-format', format);
            },

            /*
            *   We only show this navigation when we have an active collection
            */
            showNavigation() {
                this.show = true;
            },

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

            EventBus.$on('default-query-format', ( format ) => {
                this.activeFormat = format;

            });
        }
    }
</script>
