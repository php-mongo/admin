<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DbsView.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   DbsView.vue
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
    .pma-dbs-view {
        background: url('/img/left-nav-bg.png') repeat-y right 0% $dbsBgColor;
        height: 100%;
        left: 0;
        position: fixed;
        top: 0;
        width: 240px;
        z-index: 800;

        .dbs-nav-resizer {
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

        .dbs-nav-resizer:hover {
            background-color: $resizerHoverColor;
        }

        .dbs-nav-collapse {
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
            display: block;

            .dbs-list-block {
                margin-bottom: 10px;
                position: relative;
                padding-bottom: 5px;

                .dbs-list {
                    list-style: none;
                    margin: 0;

                    li {
                        border-bottom: 1px solid $darkGreyColor;

                        &:hover {
                            background-color: $lightGrey !important;
                        }
                    }
                }
            }
        }
    }
</style>

<template>
    <div ref="leftPane" class="pma-dbs-view align-left">
        <div class="dbs-nav-resizer" ref="resizer"></div>
        <div class="dbs-nav-collapse" ref="collapse" v-on:click="handleCollapse">←</div>
        <dbs-top v-bind:collapsed="collapsed"></dbs-top>
        <article ref="dbs" class="dbs-list-block-outer">
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
                collapsed: false
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
         *   Defined methods for the component
         */
        methods: {
            handleCollapse() {
                /* there are a few $refs involved in handling this process */
                this.collapsed = !this.collapsed;
                if (this.collapsed === true) {
                    // minimise
                    this.$jqf(this.$refs.leftPane).css('width', 0);
                    this.$jqf(this.$refs.dbs).css('display', 'none');
                    this.$jqf(this.$refs.resizer).css('left', 0);
                    this.$jqf(this.$refs.collapse).css('left', '5px');
                    this.$jqf(this.$refs.collapse).html('→');
                    EventBus.$emit('collapse-left-nav');
                }
                if (this.collapsed === false) {
                    // maximise
                    this.$jqf(this.$refs.leftPane).css('width', '240px');
                    this.$jqf(this.$refs.dbs).css('display', 'block');
                    this.$jqf(this.$refs.resizer).css('left', '240px');
                    this.$jqf(this.$refs.collapse).css('left', '245px');
                    this.$jqf(this.$refs.collapse).html('←');
                    EventBus.$emit('expand-left-nav');
                }
            }
        },

        /*
         * get on ur bikes and ride !!
         */
        mounted() {
            EventBus.$on('change-list', (data) => {
                if (data.show === true) {
                    this.display = true;
                }
                if (data.hide === true) {
                    this.display = false;
                }
            });
        }
    }
</script>
