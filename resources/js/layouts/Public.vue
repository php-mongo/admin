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

    div.public-view {
        background-color: $navBgColor;

        header.header-content {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 80px;
            background: url('/img/header-background-dark-blue.png') repeat-x 0 -22px;
            padding-top: 16px;
            z-index: 2;

            .grid-container {
                max-width: 99%;
            }
        }
        .main-content {
            padding: 30px 0 10px 0;
            margin-top: 0;
        }
    }
</style>

<template>
    <div id="app-layout" class="public_view off-canvas-wrapper-inner" ref="appLayout">
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

        method: {
            getCountryFromCookie() {
                return this.$cookie.get('my-country');
            },

            getCountryFromStore() {
                return this.$store.getters.getCountryName;
            },

            weAreScrolling(event) {
             //   console.log(event.target.scrollHeight);
              //  console.log(event.target.scrollTop);
            }
        },

        mounted() {
            if (this.getCountryFromCookie !== this.getCountryFromStore)  {
                this.$store.dispatch('setCountryNameFromCookie', this.$cookie.get('my-country'));
            }
        },

        created() {
            this.$store.dispatch( 'getLocation');
            this.$store.dispatch( 'setDefaultLanguage' );

            this.$http.interceptors.response.use(undefined, function (err) {
                return new Promise(function (resolve, reject) {
                    if (err.status === 401 && err.config && !err.config.__isRetryRequest) {
                        this.$store.dispatch('logoutUser');
                    }
                    throw err;
                });
            });

            this.$store.dispatch( 'loadUser' );

            window.addEventListener('scroll', this.weAreScrolling);

        },

        destroyed() {
            window.removeEventListener('scroll', this.weAreScrolling);
        }
    }
</script>
