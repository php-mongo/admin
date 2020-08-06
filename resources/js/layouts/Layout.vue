<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Layout.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Layout.vue
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

    div#app-layout {
        background-color: inherit;
    }
</style>

<template>
    <div id="app-layout" class="off-canvas-wrapper-inner" ref="appLayout">

        <success-notification></success-notification>

        <error-notification></error-notification>

        <message-notification></message-notification>

        <delete-confirmation></delete-confirmation>

        <router-view></router-view>

        <no-results-found></no-results-found>

        <setup-modal></setup-modal>

        <login-modal></login-modal>

        <language-modal></language-modal>

    </div>
</template>

<script>
    /*
    *   Import components
    */
    import Navigation from '../components/admin/top/TopNav.vue';

    import SuccessNotification from "../components/global/SuccessNotification.vue";

    import ErrorNotification from "../components/global/ErrorNotification.vue";

    import MessageNotification from "../components/global/MessageNotification";

    import DeleteConfirmation from "../components/global/DeleteConfirmation";

    import NoResultsFound from "../components/admin/NoResultsFound";

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
            DeleteConfirmation,
            NoResultsFound,
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
                console.log("scrolling...");
                console.log(event.target.scrollHeight);
                console.log(event.target.scrollTop);
            },

            handleScroll() {
                const viewportHeight = window.innerHeight;
                const docHeight = document.body.offsetHeight;
                const scrollTop = document.documentElement.scrollTop;
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
