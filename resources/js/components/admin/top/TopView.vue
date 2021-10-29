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
        z-index: 90;

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
        <top @collapseNav="collapseNav($event)" @clearError="clearError" v-bind:error="error" v-bind:user="user"></top>
        <top-nav v-bind:collapsed="collapsed" v-bind:user="user"></top-nav>
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
                collapsed: false,
                limit: 5,
                retries: 0,
                retryInterval: null,
                user: {}
            }
        },

        computed: {
            /*
             *  Simple error monitor
             */
            watchError() {
                return this.$store.getters.getAppErrorData
            },
        },

        /*
         *   Defined methods for the component
         */
        methods: {
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key )
            },

            watchLeftNav() {
                this.expanded = !this.expanded;
                if (this.expanded === true) {
                    this.$jqf(this.$refs.pmaTopPanel).css('margin-left', '5px');
                    this.$jqf(this.$refs.pmaTopPanel).css('width', '99vw')
                }
                if (this.expanded === false) {
                    this.$jqf(this.$refs.pmaTopPanel).css('margin-left', '245px');
                    this.$jqf(this.$refs.pmaTopPanel).css('width', 'calc(100vw - 262px)')
                }
            },

            /*
             *  Collapsing this component via events
             */
            collapseNav( status ) {
                EventBus.$emit('collapse-nav', status)
                this.collapsed = status
            },

            /*
             *  Simple implementation - expect error to be:  error.error
             */
            setError(error) {
                if (error) {
                    this.error = { error: error};
                    return
                }
                let errorData = this.$store.getters.getAppErrorData
                this.error = errorData.errors ?
                    errorData.errors :
                    { error: 'Unhandled errors occurred' }
            },

            setPermissionError() {
                if (this.$store.getters.getCanUserReadDatabase === false && this.$store.getters.getCanUserWriteDatabase === false) {
                    this.setError(this.showLanguage('errors', 'noDatabaseRole'));
                    if (this.$store.getters.getConnectionConfirmation) {
                        this.retryInit()
                    }
                }
            },

            /*
             *  Handle event from child component
             */
            clearError() {
                this.$store.dispatch( 'clearAppErrorData' );
                this.error = null
            },

            /*
            *   Retrieves the User from Vuex
            */
            getUser() {
                this.user = this.$store.getters.getUser
            },

            handleGetUser() {
                if (!this.user.id && this.retries < this.limit) {
                    setTimeout(() => {
                        this.user = this.$store.getters.getUser
                    }, 100)
                } else {
                    this.setPermissionError()
                }
            },

            retryInit() {
                this.retryInterval = setInterval(() => {
                    this.error = 'Retrying initialisation';
                    if (this.retries < this.limit) {
                        this.retries++;
                        // try fetching db's again
                        this.retry()

                    } else {
                        this.retries = 0;
                        this.retryInterval = null
                    }
                }, 5000 )
            },

            retry()  {
                console.log("retrying databases fetch..");
                this.$store.dispatch('loadDatabases')
            }
        },

        /*
         * get on ur bikes and ride !!
         */
        mounted() {
            // we pass the user to child components as props
            setTimeout(() => {
                this.getUser();
            }, 250)

            EventBus.$on('collapse-left-nav', () => {
                this.watchLeftNav()
            });

            EventBus.$on('expand-left-nav', () => {
                this.watchLeftNav()
            });

           /* setTimeout(() => {
                if (!this.user.id) {
                    this.getUser()
                }
                this.setPermissionError()
            }, 500)
*/
        },

        watch: {
            watchError() {
                this.setError()
            },
        }
    }
</script>
