<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Top.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Top.vue
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

    .top-nav-wrapper {
        background-color: $darkGreyColor;
        padding-left: 50px;
    }
    nav.top-navigation {
        min-height: 33px;
        max-width: 99%;
        background-color: inherit;

        ul.links {
            display: inline-block;
            margin: 0;

            li {
                display: inline-block;
                list-style-type: none;
                margin-left: 7px;
                vertical-align: top;
                position: relative;
                background-color: inherit;

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

                .top-error {
                    background-color: $offWhite;
                    border-radius: 2px;
                    color: $red;
                    cursor: pointer;
                    padding: 4px;

                    &:hover {
                        text-decoration: none;
                    }
                }

                &:hover {
                    background-color: inherit;
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
            background-color: inherit;

            li.actions {
                padding-right: 10px;
            }

            .country-flag {
                height: 33px;
                width: 33px;
                cursor: pointer;

                img {
                    margin-top: -5px;
                }
            }

            .nav-collapse {
                cursor: pointer;

                img {
                    width: 1.2rem;
                }
            }

            p.roles-list {
                background-color: inherit;
                margin: 0;
                position: absolute;
                left: -52px;
                min-width: 320px;
                padding-left: 10px;

                .role-item-wrapper {
                    display: inline-block;
                    width: 100%
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
                    <img src="img/icon/servers.png" /> <span class="pma-link" v-on:click="loadHome($event)">{{ getDbHost }}</span>
                </li>
                <li v-if="error !== null">
                    <span class="top-error" @click="$emit('clearError')">{{ getError }}</span>
                    <span class="text-info pma-link" @click="$emit('clearError')" v-text="showLanguage('nav', 'close')"></span>
                </li>
            </ul>
            <ul class="links right">
                <!--display user-->
                <li v-show="isMember">
                    <span class="pma-link" v-show="userLoadStatus" @click="showRoles" :title="showLanguage('title', 'navUsernameTitle')">
                        {{usersName}}
                    </span>
                    <p class="roles-list">
                        <span class="role-item-wrapper" v-for="(role, key) in rolesList" v-bind:key="key" v-bind:role="role">
                            <span class="roles-item">Database: {{ role.db }}, Role: {{ role.role }}</span>
                        </span>
                    </p>
                </li>
                <li><span class="pma-link">|</span></li>
                <li>
                    <router-link :to="{ name: 'about' }">
                        <span class="pma-link" v-text="showLanguage('nav','about')"></span>
                    </router-link>
                </li>
                <li><span class="pma-link">|</span></li>
                <li v-show="isMember" class="member-button">
                    <span class="pma-link" v-on:click="runLogout($event)" v-bind:title="showLanguage('title', 'logoutTitle')" v-text="showLanguage('nav', 'logout')"></span>
                </li>
                <li class="actions" v-on:click="collapseNav">
                    <span class="nav-collapse" v-show="collapsed" :title="showLanguage('nav', 'collapse')"><img src="img/sort-asc.svg" alt="Collapse nav" /> </span>
                    <span class="nav-collapse" v-show="!collapsed" :title="showLanguage('nav', 'expand')"><img src="img/sort-desc.svg" alt="Expand nav" /> </span>
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

    import router from '../../../routes';

    export default {
        /*
         *  handle error display
         */
        props: ['user','error'],

        /*
        *   Data used with this component
        */
        data() {
            return {
                collapsed: false,
                countryName: null,
                isLoggedIn: null,
                rolesList: null,
                searchActive: null,
                showRolesList: false,
                index: 0,
            };
        },

        /*
        *   Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Retrieves the User Load Status from Vuex
            */
            userLoadStatus() {
                return (this.$store.getters.getUserLoadStatus === 2)
            },

            /*
            *   Return the site name from config
            */
            siteName() {
                return MONGO_CONFIG.SITE_NAME
            },

            /*
            *   Display the current DB connection hostname
            */
            getDbHost() {
                let host = this.$store.getters.getDbHost;
                return !host || host === 'localhost' ? 'Localhost' : host
            },

            /*
            *   Check for country name
            */
            hasCountryName() {
                return (this.countryName != null)
            },

            /*
            *   Apply country name - not currently used
            */
            setCountryName() {
                return this.countryName
            },

            /*
            *   Check for App membership
            */
            isMember() {
                let isMember = this.$cookies.get('app-member');
                return ((isMember && isMember.length >= 3) || this.userLoadStatus)
            },

            /*
            *   Render the username
            */
            usersName() {
                return this.user.name
            },

            /*
            *   Renders the error to DOM
            */
            getError() {
                return this.error.error ?
                    "Alert: " + this.error.error :
                    this.error.message ?
                        "Alert: " + this.error.message :
                        'Unhandled errors occurred'
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
                return this.$store.getters.getLanguageString( context, key )
            },

            collapseNav() {
                this.collapsed = !this.collapsed;
                this.$emit('collapseNav', this.collapsed)
            },

            /*
            *   Get the users country name
            */
            getCountryNameValue() {
                this.countryName = this.$store.getters.getCountryName
            },

            /*
            * Load the home default views
            */
            loadHome() {
                EventBus.$emit('hide-collection-lists');
                EventBus.$emit('hide-panels');
                // force a reload of the databases
                this.$store.dispatch('loadDatabases');
                this.$store.dispatch('setActiveNav', null);
                EventBus.$emit('show-server');
                EventBus.$emit('show-mongo')
            },

            /*
            *   Run the logout sequence
            */
            runLogout(event) {
                event.preventDefault();
                let uid = this.$store.getters.getUserAuthStatus;
                this.$store.dispatch('logoutUser', { uid: uid });
                this.completeLogout(uid)
            },

            completeLogout(uid) {
                let status = this.$store.getters.getUserLogoutStatus;
                if (status === 1 && this.index < 25) {
                    this.index++;
                    setTimeout(() => {
                        this.completeLogout(uid)
                    }, 100)
                }
                if (status === 2) {
                    EventBus.$emit('show-success', { notification: this.showLanguage('auth', 'logout-success') });
                    setTimeout(() => {
                        router.push( { name: 'public' } )
                    }, 2000)
                    EventBus.$emit('user-logged-out', uid)
                }
                if (status === 3) {
                    EventBus.$emit('show-error', { notification: 'An unknown error has interrupted the logout process' })
                }
            },

            showRoles() {
                this.showRolesList = !this.showRolesList;
                this.rolesList = this.showRolesList ? this.$store.getters.getUserRoles : null
            },

            /*
            *   Open language selector
            */
            openLanguageSelector() {
                EventBus.$emit('show-language-selector')
            },
        },
    }
</script>
