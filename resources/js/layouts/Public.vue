<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Public.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Public.vue
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

    .public-view {
        /*background-color: $navBgColor;

        header.header-content {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 80px;
            background: url('img/header-background-dark-blue.png') repeat-x 0 -22px;
            padding-top: 16px;
            z-index: 2;

            .grid-container {
                max-width: 99%;
            }
        }

        .main-content {
            padding: 30px 0 10px 0;
            margin-top: 0;
        }*/

        div.notification-container {
            left: 2rem;
        }
    }
</style>

<template>
    <div id="app-layout" class="public-view off-canvas-wrapper-inner" ref="appLayout">
        <header class="header-content">
            <navigation></navigation>
        </header>

        <success-notification></success-notification>

        <error-notification></error-notification>

        <message-notification></message-notification>

        <router-view></router-view>

        <site-footer></site-footer>

        <register-modal></register-modal>

        <setup-modal></setup-modal>

        <login-modal></login-modal>

        <language-modal></language-modal>

    </div>
</template>

<script>
    /*
    * Imports the event bus
    *  v-on:scroll="weAreScrolling($event)"
    *  v-on:scroll.native="weAreScrolling($event)"
    */
    import { EventBus } from '../event-bus.js';

    /*
    *   Import components
    */
    import Navigation from '../components/global/PublicHeader.vue';

    import SuccessNotification from "../components/global/SuccessNotification.vue";

    import ErrorNotification from "../components/global/ErrorNotification.vue";

    import MessageNotification from "../components/global/MessageNotification";

    import SiteFooter from "../components/global/SiteFooter.vue";

    import RegisterModal from "../components/global/RegisterModal.vue";

    import LoginModal from "../components/global/LoginModal.vue";

    import LanguageModal from "../components/global/LanguageModal";

    import SetupModal from "../components/global/SetupModal.vue";
    import router from "../routes";

    export default {
        components: {
            Navigation,
            SuccessNotification,
            ErrorNotification,
            MessageNotification,
            SiteFooter,
            RegisterModal,
            LoginModal,
            LanguageModal,
            SetupModal,
        },

        data() {
            return {
                index: 0,
                limit: 20,
                showLogin: false,
                loggedOut: null,
            }
        },

        methods: {
            getCountryFromCookie() {
                return this.$cookies.get('my-country')
            },

            getCountryFromStore() {
                return this.$store.getters.getCountryName
            },

            /*
             * when the user reloads or refreshes and token is still valid
             */
            handleLoginCheck() {
                let status = this.$store.getters.getUserLoadStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleLoginCheck();
                    }, 200)
                }
                if (status === 2) {
                    setTimeout(() => {
                        router.push( { name: 'admin' } );
                    }, 200)
                }
                if (status === 3) {
                    this.loggedOut = null;
                    this.showLogin = true
                }
            }
        },

        mounted() {
            if (this.getCountryFromCookie !== this.getCountryFromStore)  {
                this.$store.dispatch('setCountryNameFromCookie', this.$cookies.get('my-country'));
            }

            EventBus.$on('user-logged-out', (uid) => {
                this.loggedOut = uid;
            });

            EventBus.$on('login-success', () => {
                console.log("public vue reports logged in")
            });
        },

        created() {
            this.$store.dispatch( 'getLocation');
            this.$store.dispatch( 'setDefaultLanguage' );

            this.$http.interceptors.response.use(undefined, function (err) {
                return new Promise(function () {
                    if (err.status === 401 && err.config && !err.config.__isRetryRequest) {
                        this.$store.dispatch('logoutUser');
                    }
                    throw err;
                });
            });

            // initialise the user load test on page load
            if (this.loggedOut === null) {
                this.$store.dispatch( 'loadUser' );
            }
            this.handleLoginCheck();
        },

        destroyed() {
            //
        }
    }
</script>
