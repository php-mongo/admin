<style lang="scss">
    /* no style yet */
</style>

<template>
    <div class="collection-document">
        <document-nav @expand="expand($event)" @text="text($event)" v-bind:document="document" v-bind:collection="collection" v-bind:index="index"></document-nav>
        <div :id="'text_' + index" :class="'doc-data index-' + index" ref="data-document" v-html="document.data"></div>
        <div :id="'field_' + index" class="doc-text hidden-element" ref="text-document">
            <textarea ref="textbox" rows="7" cols="60" v-on:dblclick="selectAll($event)">{{ document.text }}</textarea>
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
        props: ['document','collection','index'],

        data() {
            return {
                expanded: false,
                showText: false
            }
        },

        /*
         *  Component methods
         */
        methods: {
            /*
             *   Calls the Translation and Language service
             */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /**
             *  Focus the textarea field
             */
            selectAll(event) {
                event.target.focus();
            },

            /**
             *  Expand the main data document view - works for both JSON and Array views
             *
             *  @param event
             */
            expand(event) {
                console.log(event);
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

            /**
             *  Show / Hide the Textarea (array view)
             *
             *  @param event
             */
            text(event) {
                console.log(event);
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
