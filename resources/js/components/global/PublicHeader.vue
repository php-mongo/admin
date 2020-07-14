<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    nav.public-navigation {
        min-height: 40px;

        div.text-center {
            a {
                margin: 0 auto;
                display: inline-block;
                width: 100%;

                span.logo {
                    display: block;
                    font-size: 30px;
                    height: 40px;
                    padding: 3px 0 0 0;
                    font-weight: 400;
                    color: $white;
                    background-color: $buttonBackground;
                    border-radius: 7px;

                    &:hover{
                        color: white;
                        background-color: $buttonBackgroundHover;
                    }
                }
            }
        }

        ul.links {
            display: block;
            float: left;
            margin: 0;

            li {
                display: inline-block;
                list-style-type: none;
                margin-left: 7px;

                a {
                    font-weight: bold;
                    color: $white;
                    font-size: 12px;
                    line-height: 40px;
                    padding: 8px;
                    background-color: $menuButtonBackground;
                    border-color: $menuButtonBorder;
                    border-radius: 7px;

                    &:hover {
                        background-color: $buttonBackgroundHeaderHover;
                        border-color: $menuButtonBorderHover;
                    }
                }
            }

            li.member-button {
                a {
                    background-color: transparent;
                    border: 0;
                    padding: 0;

                    button {
                        background-color: $navBgColor;
                        color: $memberButtonColor;
                        border-radius: 7px;
                        padding: 9px;
                        cursor: pointer;

                        &:hover {
                            color: $bodyFontColor;
                            border: 1px solid $baseColor;
                            background-color: $iceColor;
                        }
                    }
                }
            }
        }

        div.right {
            float: right;
            color: $white;

            .country-flag {
                height: 48px;
                width: 48px;
                cursor: pointer;

                img {
                    margin-top: -10px;
                }
            }
        }
    }

    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        nav.public-navigation {
            div.text-center {
                a {
                    span.logo {
                        font-size: 20px;
                        padding-top: 8px;
                    }
                }
            }
        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 769px) and (max-width: 992px) {
        nav.public-navigation {
            div.text-center {
                a {
                    span.logo {
                        font-size: 25px;
                        padding-top: 4px;
                    }
                }
            }
        }
    }

    /* Large only - (min-width: 64em) and (max-width: 74.9375em) */
    @media (min-width: 993px) and (max-width: 2048px) {
        nav.public-navigation {
            div.text-center {
            }
        }
    }
</style>

<template>
    <nav class="public-navigation grid-container">
        <div class="grid-x">
            <div class="large-5 medium-5 small-6 cell text-left">
                <ul class="links">
                    <!-- public header links -->
                    <li>
                        <router-link :to="{ name: 'about' }">
                            <span v-text="showLanguage('nav','about')"></span>
                        </router-link>
                    </li>
                    <li v-show="isMember" class="member-button">
                        <span v-show="!userLoadStatus">
                            <a href="#" v-on:click="loadLogin($event)">
                                <button v-bind:title="showLanguage('title', 'loginTitle')" v-text="showLanguage('nav', 'login')"></button>
                            </a>
                        </span>
                    </li>
                </ul>
            </div>
            <div class="large-2 medium-2 hide-for-small-only cell text-center">
                <span class="logo"><img :alt="siteName" :title="siteName" src="/img/logo-pma.png" /></span>
            </div>
            <div class="large-5 medium-5 small-6 cell" style="position: relative;">
                <div class="right">
                    <!-- country flag display -->
                    <span v-on:click="openLanguageSelector()" class="country-flag ng-scope" v-show="hasCountryName">
                        <img v-bind:src="'/img/flags/icons/' + setCountryName + '.ico'" v-bind:alt="'Your country is ' + setCountryName" v-bind:title="showLanguage('title', 'countryIsTitle') + ' ' + setCountryName">
                    </span>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    /*
    *   Import the application JS config
    */
    import { MONGO_CONFIG } from "../../config.js";

    /*
    *   Imports the event bus.
    */
    import { EventBus } from '../../event-bus.js';

    export default {
        /*
        *   Data used with this component
        */
        data() {
            return {
                countryName: null,
                isLoggedIn: null,
                user: {}
            };
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Retrieves the User Load Status from Vuex
            */
            userLoadStatus() {
                return (this.$store.getters.getUserLoadStatus === 2);
            },

            /*
            *   Return the site name from config
            */
            siteName() {
                return MONGO_CONFIG.SITE_NAME;
            },

            hasCountryName() {
                return (this.countryName != null);
            },

            setCountryName() {
                return this.countryName;
            },

            isMember() {
                let isMember = this.$cookie.get('app-member');
                return ((isMember && isMember.length >= 3) || this.userLoadStatus);
            }
        },

        /*
        *   Defined methods
        */
        methods: {
            /*
            * Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                // return this.$trans( context, key );
                return this.$store.getters.getLanguageString( context, key );
            },

            openLanguageSelector() {
                EventBus.$emit('show-language-selector');
            },

            getCountryNameValue() {
                this.countryName = this.$store.getters.getCountryName;
            },

            loadLogin(event) {
                event.preventDefault();
                EventBus.$emit('prompt-login');
            },

            userLoggedIn() {
                this.isLoggedIn = this.$store.getters.isLoggedIn;
            },

            /*
            * Retrieves the User from Vuex
            */
            getUser() {
                this.user = this.$store.getters.getUser;
            }
        },

        mounted() {
            this.getCountryNameValue();
            this.userLoggedIn();
            this.getUser();
        }
    }
</script>
