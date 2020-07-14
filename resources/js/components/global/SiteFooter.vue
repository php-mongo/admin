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
                    <uL class="footer-menu">
                        <li>
                            <router-link :to="{ name: 'contact' }">
                                <span :title="showLanguage('title', 'contactus')" v-text="showLanguage('nav','contact')"></span>
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'about' }">
                                <span :title="showLanguage('title', 'aboutPhpMongoAdmin')" v-text="showLanguage('nav','about_footer')"></span>
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'privacy' }">
                                <span :title="showLanguage('title', 'privacyPolicy')" v-text="showLanguage('nav','privacy')"></span>
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'terms' }">
                                <span :title="showLanguage('title', 'termsConditions')" v-text="showLanguage('nav','terms')"></span>
                            </router-link>
                        </li>
                        <li>
                            <a v-on:click="openLanguageSelector($event)" href="#"><span :title="showLanguage('title', 'changeLanguage')" v-text="showLanguage('nav','language')"></span></a>
                        </li>
                    </uL>
                </div>
                <div class="large-8 medium-8 cell text-right">
                    <div class="social-container text-right">
				        <p>&nbsp;</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-container hide-for-small-only">
            <div class="grid-x grid-padding-x text-right copyright">
                <!--<p><strong>{{ isABot }} - {{ ageConfirmed }} - {{ hasConfirmed() }}</strong></p>-->
                <div class="large-12 medium-11 float-right text-right copyleft" v-html="showLanguage('global', 'copyright')"></div>
                <div class="large-12 medium-11 float-right text-right" v-show="userLoadStatus">{{ getUsersName() }} from {{ countryName }}</div>
            </div>
        </div>
        <div class="grid-container hide-for-large hide-for-medium">
            <div class="grid-x grid-padding-x">
                <div class="small-12 float-left">
                    <uL class="footer-menu float-left">
                        <li>
                            <router-link :to="{ name: 'contact' }">
                                <span :title="showLanguage('title', 'contactus')" v-text="showLanguage('nav','contact')"></span>
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'about' }">
                                <span :title="showLanguage('title', 'aboutPhpMongoAdmin')" v-text="showLanguage('nav','about_footer')"></span>
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'privacy' }">
                                <span :title="showLanguage('title', 'privacyPolicy')" v-text="showLanguage('nav','privacy')"></span>
                            </router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'terms' }">
                                <span :title="showLanguage('title', 'termsConditions')" v-text="showLanguage('nav','terms')"></span>
                            </router-link>
                        </li>
                        <li>
                            <span :title="showLanguage('title', 'changeLanguage')" v-text="showLanguage('nav','language')" v-on:click="openLanguageSelector($event)"></span>
                        </li>
                    </uL>
                    <div class="small-4 float-right">
				        <span v-show="!isMember" class="register-button pull-right">
						    <button v-on:click="loadRegistration()" :title="showLanguage('title', 'registerTitle')" v-text="showLanguage('nav', 'register')"></button>
				        </span>
                        <span class="hide-loading pull-right login-button" v-show="!userLoadStatus">
					        <button v-on:click="loadLogin()" :title="showLanguage('title', 'manageData')" v-text="showLanguage('nav', 'login')"></button>
				        </span>
                    </div>
                </div>
                <div class="small-12 text-left float-left">
                    <div class="small-12 u-padding-flush">
                        <p>&nbsp;</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-container hide-for-large hide-for-medium">
            <div class="grid-x grid-padding-x float-left">
                <div class="small-12 float-left text-left" v-html="showLanguage('global', 'copyright')"></div>
                <div class="small-12 float-left text-left" v-show="userLoadStatus">{{ getUsersName() }} from {{ countryName }}</div>
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
