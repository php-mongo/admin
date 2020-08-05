<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Document.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      Document.vue
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
    /* no style yet */
</style>

<template>
    <div class="collection-document">
        <document-nav @expand="expand($event)" @text="text($event)" v-bind:document="document" v-bind:collection="collection" v-bind:index="index"></document-nav>
        <div :id="'text_' + index" :class="'doc-data index-' + index" ref="data-document" v-html="getDataByFormat"></div>
        <div :id="'field_' + index" class="doc-text hidden-element" ref="text-document">
            <textarea ref="textbox" rows="7" cols="60" v-on:dblclick="selectAll($event)">{{ getTextByFormat }}</textarea>
        </div>
        <div class="doc-right-to-top"><span class="pma-link">Top</span></div>
    </div>
</template>

<script>
    /*
     *   Import components
     */
    import DocumentNav from "./DocumentNav";

    export default {
        /*
         *   Register the child components to be used by the document
         */
        components: {
            DocumentNav
        },

        /*
         *   Document properties
         */
        props: ['document','collection','index','format'],

        /*
         *  Required data elements
         */
        data() {
            return {
                expanded: false,
                showText: false
            }
        },

        computed: {
            /*
             *  When a format is selected in the Collection nav, this method flips the visible data content
             */
            getDataByFormat( document ) {
                if (this.format === 'json') {
                    return this.document.data;
                }
                if (this.format === 'array') {
                    return this.document.text;
                }
            },

            /*
             *  When a format is selected in the Collection nav, this method flips the textarea content
             */
            getTextByFormat( document ) {
                if (this.format === 'json') {
                    return this.document.text;
                }
                if (this.format === 'array') {
                    return this.document.data;
                }
            }
        },

        /*
         *  Component methods
         */
        methods: {
            /*
             *   Calls the Translation and Language service
             *
             *  @param context string This defines (page-name).php parent language group
             *  @param key string     This defines the key item to select from the language group
             */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
             *  Focus the textarea field
             *
             *  @param event
             */
            selectAll(event) {
                event.target.focus();
            },

            /*
             *  Expand the main data document view - works for both JSON and Array views
             *
             *  @param event
             */
            expand(event) {
                if (this.expanded === true) {
                    this.expanded = false;
                    if (this.showText === true) {
                        this.$jqf(this.$refs['text-document']).css("maxHeight", "150px");
                    } else {
                        this.$jqf(this.$refs['data-document']).css("maxHeight", "150px");
                    }
                    this.$jqf(event).text('Expand');

                } else {
                    this.expanded = true;
                    if (this.showText === true) {
                        this.$jqf(this.$refs['text-document']).css("maxHeight", "100vh");
                    } else {
                        this.$jqf(this.$refs['data-document']).css("maxHeight", "100vh");
                    }
                    this.$jqf(event).text('Collapse');
                }
            },

            /*
             *  Show / Hide the Textarea (default array view) and data container (default JSON view)
             *
             *  @param event
             */
            text(event) {
                if (this.showText === true) {
                    this.showText = false;
                    this.$jqf(this.$refs['text-document']).hide();
                    this.$jqf(this.$refs['data-document']).show();
                    this.$jqf(event).text('Text');

                } else {
                    this.showText = true;
                    this.$jqf(this.$refs['data-document']).hide();
                    this.$jqf(this.$refs['text-document']).show();
                    this.$jqf(event).text('Data');
                }
            }
        }
    }
</script>
