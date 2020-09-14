<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      TopView.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   TopView.vue
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
    .pma-top-panel {
        background-color: $navBgColor;
        margin-left: 245px;
        padding-bottom: 2px;
        position: relative;
        top: 0;
        width: calc(100vw - 262px);
        z-index: 88;

        .pma-link {
            color: $linkColor;
            cursor: pointer;
        }
    }

    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        .pma-top {

        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 769px) and (max-width: 992px) {
        .pma-top {

        }
    }

    /* Large only - (min-width: 64em) and (max-width: 74.9375em) */
    @media (min-width: 993px) and (max-width: 2048px) {
        .pma-top {

        }
    }
</style>

<template>
    <div ref="pmaTopPanel" class="pma-top-panel">
        <top @collapseNav="collapseNav($event)" v-bind:error="error"></top>
        <top-nav v-bind:collapsed="collapsed"></top-nav>
    </div>
</template>
<script>
    /*
    *   Import components for the Gallery View
    */
    import Top  from "./Top";
    import TopNav from "./TopNav";
    import {EventBus} from "../../../event-bus";

    export default {
        /*
        *   Registers child components with the component.
        */
        components: {
            Top,
            TopNav
        },

        /*
         *  Define the data used by the component.
         */
        data() {
            return {
                error: null,
                expanded: false,
                collapsed: false
            }
        },

        computed: {
            /*
             *  Simple error monitor
             */
            watchError() {
                return this.$store.getters.getErrorData;
            }
        },

        /*
         *   Defined methods for the component
         */
        methods: {
            watchLeftNav() {
                this.expanded = !this.expanded;
                if (this.expanded === true) {
                    this.$jqf(this.$refs.pmaTopPanel).css('margin-left', '5px');
                    this.$jqf(this.$refs.pmaTopPanel).css('width', '99vw');
                }
                if (this.expanded === false) {
                    this.$jqf(this.$refs.pmaTopPanel).css('margin-left', '245px');
                    this.$jqf(this.$refs.pmaTopPanel).css('width', 'calc(100vw - 262px)');
                }
            },

            collapseNav( status ) {
                this.collapsed = status;
            },

            /*
             *  Simple implementation - expect error to be:  error.error
             */
            setError() {
                this.error = this.$store.getters.getErrorData;
            }
        },

        /*
         * get on ur bikes and ride !!
         */
        mounted() {
            EventBus.$on('collapse-left-nav', () => {
                this.watchLeftNav();
            });

            EventBus.$on('expand-left-nav', () => {
                this.watchLeftNav();
            });

            EventBus.$on('collapse-nav', (status) => {
                this.collapseNav(status);
            });
        },

        watch: {
            watchError() {
                this.setError();
            }
        }
    }
</script>
