<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    .top-nav-wrapper {
        background-color: $darkGreyColor;
        padding-left: 50px;
    }
    nav.top-navigation {
        min-height: 33px;
        max-width: 99%;

        ul.links {
            display: inline-block;
            margin: 0;

            li {
                display: inline-block;
                list-style-type: none;
                margin-left: 7px;

                span {
                    font-weight: bold;
                    font-size: 12px;
                    line-height: 33px;
                    color: $white;

                    &:hover {
                        color: $linkColor;
                        text-decoration: underline;
                    }
                }
            }

            li.member-button {
                a {
                    background-color: transparent;
                    border: 0;
                    padding: 0;

                    button {
                        color: $memberButtonColor;
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

        ul.right {
            position: absolute;
            right: 20px;
            top: 0;

            .country-flag {
                height: 33px;
                width: 33px;
                cursor: pointer;

                img {
                    margin-top: -5px;
                }
            }
        }
    }

    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        nav.top-navigation {
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
        nav.top-navigation {
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

    }
</style>

<template>
    <div class="top-nav-wrapper">
        <nav class="top-navigation">
            <ul class="links">
                <li class="host-link text-left">
                    <img src="/img/icon/servers.png" /> <span class="pma-link" v-on:click="loadHome($event)">Localhost</span>
                </li>
            </ul>
            <ul class="links right">
                <li>
                    <router-link :to="{ name: 'about' }">
                        <span class="pma-link" v-text="showLanguage('nav','about')"></span>
                    </router-link>
                </li>
                <!--display user-->
                <li v-show="isMember" class="username">
                    <span v-show="userLoadStatus" >
                        {{usersName}}
                    </span>
                </li>
                <li v-show="isMember" class="member-button">
                    <span class="pma-link" v-on:click="runLogout($event)" v-bind:title="showLanguage('title', 'logoutTitle')" v-text="showLanguage('nav', 'logout')"></span>
                </li>
            </ul>
        </nav>
    </div>
</template>
<script>
    /*
    *   Import the application JS config
    */
    import { MONGO_CONFIG } from "../../../config.js";

    /*
    *   Imports the event bus.
    */
    import { EventBus } from '../../../event-bus.js';

    /*
    *   Imports filters.
    */
    import TextFilter from '../../global/TextFilter.vue';

    export default {
        /*
        *   Data used with this component
        */
        data() {
            return {
                countryName: null,
                searchActive: null,
                isLoggedIn: null,
                userName: null,
                user: {}
            };
        },

        /*
        *   Registers child components with the component.
        */
        components: {
            TextFilter
        },

        /*
        *   Defines the computed properties on the component.
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

            /*
            *   Check for country name
            */
            hasCountryName() {
                return (this.countryName != null);
            },

            /*
            *   Apply country name - not currently used
            */
            setCountryName() {
                return this.countryName;
            },

            /*
            *   Check for App membership
            */
            isMember() {
                let isMember = this.$cookie.get('app-member');
                return ((isMember && isMember.length >= 3) || this.userLoadStatus);
            },

            /*
            *   Render the username
            */
            usersName() {
                return this.$store.getters.getUsersName;
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

            /*
            *   Open language selector
            */
            openLanguageSelector() {
                EventBus.$emit('show-language-selector');
            },

            /*
            *   Get the users country name
            */
            getCountryNameValue() {
                this.countryName = this.$store.getters.getCountryName; // this.$cookie.get('my-country');
            },

            /*
            *   Run the logout sequence
            */
            runLogout(event) {
                event.preventDefault();
                let uid = this.$store.getters.getUserAuthStatus;
                this.$store.dispatch('logoutUser', { uid: uid });
                this.completeLogout();
            },

            completeLogout() {
                console.log("is the logout completed?");
            },

            /*
            *   Checks whether the user is logged in
            */
            userLoggedIn() {
                this.isLoggedIn = this.$store.getters.isLoggedIn;
            },

            /*
            * Retrieves the User from Vuex
            */
            getUser() {
                this.user = this.$store.getters.getUser;
                this.userName = this.$store.getters.getUsersName;
            },

            /*
            * Load the home default views
            */
            loadHome() {
                EventBus.$emit('hide-collection-lists');
                EventBus.$emit('hide-panels');
                // force a reload of the databases
                this.$store.dispatch('loadDatabases');
                //EventBus.$emit('clear-active-nav');
                this.$store.dispatch('setActiveNav', null);
                EventBus.$emit('show-server');
                EventBus.$emit('show-mongo');
            }
        },

        /*
        * Methods to run when component is mounted
        */
        mounted() {
            this.userLoggedIn();
            this.getUser();
        }
    }
</script>
