<style lang="scss">
    @import '~@/abstracts/_variables.scss';
    .pma-dbs-view {
        background: url(/img/left-nav-bg.png) repeat-y right 0% $dbsBgColor;
        height: 100%;
        left: 0;
        position: fixed;
        top: 0;
        width: 240px;
        z-index: 800;

        #dbs-nav-resizer {
            background-color: $resizerBgColor;
            cursor: w-resize;
            height: 100%;
            left: 240px;
            margin: 0;
            overflow: hidden;
            padding: 0;
            position: absolute;
            top: 0;
            width: 5px;
            z-index: 2;
        }

        #dbs-nav-resizer:hover {
            background-color: $resizerHoverColor;
        }

        #dbs-nav-collapse {
            width: 25px;
            height: 30px;
            line-height: 22px;
            background-color: $collapseBgColor;
            color: #555;
            font-weight: bold;
            position: fixed;
            top: 0;
            left: 245px;
            text-align: center;
            cursor: pointer;
            z-index: 800;
            text-shadow: 0 1px 0 #fff;
            filter: dropshadow(color=#fff, offx=0, offy=1);
            border: 1px solid $collapseBorder;
        }

        .dbs-list-block-outer {
            margin-top: 10px;
            padding: 0 1rem;

            .dbs-list-block {
                margin-bottom: 10px;
                position: relative;
                padding-bottom: 5px;

                .dbs-list {
                    list-style: none;
                    margin: 0;
                }
            }
        }
    }
</style>

<template>
    <div id="left-pane" class="pma-dbs-view align-left">
        <div id="dbs-nav-resizer"></div>
        <div id="dbs-nav-collapse">←</div>
        <dbs-top></dbs-top>
        <article id="dbs" class="dbs-list-block-outer">
            <div class="dbs-list-block">
                <ul class="dbs-list">
                    <dbs-card v-for="db in dbs" :key="(db.id + 1)" v-bind:db="db"></dbs-card>
                </ul>
            </div>
        </article>
    </div>
</template>

<script>
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    /*
    *   Import components for the Gallery View
    */
    import DbsTop from "./DbsTop";
    import DbsCard from "./DbsCard";

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            DbsTop,
            DbsCard
        },

        /*
        *   Data required for this component
        */
        data() {
            return {
                display: false,
                index: 0
            }
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Get the latest ads
            */
            dbs() {
                return this.$store.getters.getDatabases;
            }
        },

        /*
        * get on ur bikes and ride !!
        */
        mounted() {
            EventBus.$on('change-list', function (data) {
                if (data.show === true) {
                    this.display = true;
                }
                if (data.hide === true) {
                    this.display = false;
                }
            }.bind(this));
        }
    }
</script>
