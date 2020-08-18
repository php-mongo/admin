<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      SiteFooter.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   SiteFooter.vue
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
    .footer-container {
        padding-top: 20px;

        .grid-container {
            max-width: 120rem;
        }

        ul {
            list-style: none;
            display: inline-block;
            margin-bottom: 0;

            li {
                line-height: 2rem;

                a.router-link-active {
                    padding: 2px;
                }
            }
        };

        .social-container {
            display: inline-block;
            .social-link {
                display: block;
                width: 230px;
                margin: 10px auto;
            }
        }

        .copyright {
            margin-top: 5px;

            .copyleft {
                margin: 0 auto;
                width: 98%;
            }
        }
    }
</style>

<template>
    <div class="footer-container">
        <div class="grid-container hide-for-small-only">
            <div class="grid-x grid-padding-x">
                <div class="large-4 medium-4 cell float-left">
                    <ul class="footer-menu">
                        <li>
                            <router-link :to="{ name: 'public-about' }">
                                <span :title="showLanguage('title', 'aboutPhpMongoAdminTitle')" v-text="showLanguage('nav','about_footer')"></span>
                            </router-link>
                        </li>
                        <li>
                            <a v-on:click="openLanguageSelector($event)" href="#"><span :title="showLanguage('title', 'changeLanguageTitle')" v-text="showLanguage('nav','language')"></span></a>
                        </li>
                    </ul>
                </div>
                <div class="large-8 medium-8 cell text-right">
                    <div class="grid-container hide-for-small-only">
                        <div class="grid-x grid-padding-x text-right copyright">
                            <div class="large-12 medium-11 float-right text-right copyleft" v-html="showLanguage('global', 'copyright')"></div>
                            <div class="large-12 medium-11 float-right text-right" v-show="userLoadStatus">{{ getUsersName() }} from {{ countryName }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-container hide-for-large hide-for-medium">
            <div class="grid-x grid-padding-x">
                <div class="small-12 float-left">
                    <ul class="footer-menu float-left">
                        <li>
                            <router-link :to="{ name: 'public-about' }">
                                <span :title="showLanguage('title', 'aboutPhpMongoAdminTitle')" v-text="showLanguage('nav','about_footer')"></span>
                            </router-link>
                        </li>
                        <li>
                            <span :title="showLanguage('title', 'changeLanguageTitle')" v-text="showLanguage('nav','language')" v-on:click="openLanguageSelector($event)"></span>
                        </li>
                    </ul>
                    <div class="small-4 float-right">
                        <span class="hide-loading pull-right login-button" v-show="!userLoadStatus">
					        <button v-on:click="loadLogin()" :title="showLanguage('title', 'loginTitle')" v-text="showLanguage('nav', 'login')"></button>
				        </span>
                    </div>
                </div>
                <div class="small-12 text-left float-left">
                    <div class="grid-container hide-for-large hide-for-medium">
                        <div class="grid-x grid-padding-x float-left">
                            <div class="small-12 float-left text-left" v-html="showLanguage('global', 'copyright')"></div>
                            <div class="small-12 float-left text-left" v-show="userLoadStatus">{{ getUsersName() }} from {{ countryName }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /*
    *   Imports the event bus.
    */
    import { EventBus } from '../../event-bus.js';

    export default {
        /*
        *   Defines this modules data requirements
        */
        data() {
            return {
                country: '',
                isLoggedIn: null
            }
        },

        computed: {
            /*
            * Retrieves the User Load Status from Vuex
            */
            userLoadStatus() {
                return (this.$store.getters.getUserLoadStatus === 2);
            },

            /*
            *   Determines if the user is a member
            */
            isMember() {
                var isMember = this.$cookie.get('app-member');
                return ((isMember && isMember.length >= 3) || this.userLoadStatus);
            },

            /*
            * Return the country name to the view
            */
            countryName() {
                return this.country;
            }
        },

        methods: {
            /*
            * Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                //return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Load the language modal
            */
            openLanguageSelector(event) {
                event.preventDefault();
                EventBus.$emit('show-language-selector');
            },

            /*
            *   Show registration modal
            */
            loadRegistration(event) {
                event.preventDefault();
                EventBus.$emit('prompt-registration');
            },

            /*
            *   Show logi modal
            */
            loadLogin(event) {
                event.preventDefault();
                EventBus.$emit('prompt-login');
            },

            /*
            *   Set the user logged in status
            */
            userLoggedIn() {
                this.isLoggedIn = this.$store.getters.isLoggedIn;
            },

            /*
            *   Get the users name if they are logged in
            */
            getUsersName() {
                if (this.isLoggedIn) {
                    return this.$store.getters.getUsersName;
                }
            },

            /*
            * get the country name of the user
            */
            getCountryNameValue() {
                this.country = this.$store.getters.getCountryName; // this.$cookie.get('my-country');
            }
        },

        /*
        *   This runs once you get on the horse
        */
        mounted() {
            this.getCountryNameValue();
            this.userLoggedIn();
        }
    }
</script>
