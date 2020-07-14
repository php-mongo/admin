<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    #language-modal {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba( 0, 0, 0, .8 );
        z-index: 100;

        div.language-box {
            width: 100%;
            max-width: 720px;
            min-width: 480px;
            background-color: transparent;
            -webkit-box-shadow: 0 1px 3px rgba(50,50,50,0.08);
            box-shadow: 0 1px 3px rgba(50,50,50,0.08);
            font-size: 16px;
            position: absolute;
            margin-top: 15px;
            top: 50%;
            left: 50%;
            height: 100%;
            transform: translate(-50%, -50%);
            z-index: 101;
            overflow: auto;
            scroll-behavior: smooth;

            div.language-label {
                color: $whiteFont;
                font-family: "Lato", sans-serif;
                font-weight: bold;
                text-transform: uppercase;
                text-align: center;
                width: 100%;
                border-radius: 10px 10px 0 0;
                padding-top: 8px;
                height: 35px;

                h3 {
                    font-size: 1.0rem;

                    img {
                        float: right;
                        margin: 2px 15px 0 0;
                        border-radius: 3px;
                        cursor: pointer;
                    }
                }
            }

            img.close-menu-icon{
                float: right;
                cursor: pointer;
            }

            div.language-content {
                width: 100%;
                padding: 2rem;
                position: relative;
                text-align: center;
                background-color: $formBackgroundColor;

                .languageSelector {
                    border-radius: 4px;
                }

                p.location {
                    padding-left: 25rem;
                    text-align: left;

                    .title {
                        display: inline-block;
                        min-width: 170px;
                        text-align: right;
                    }

                    .data {
                        display: inline-block;
                        max-width: 90%;
                        min-width: 100px;
                        text-align: left;
                        vertical-align: top;
                    }
                }

                #languageSelection {
                    margin: 5px auto;
                    padding: 10px;
                    background-color: $white;
                    list-style: none;
                    width: auto;
                    max-width: 200px;

                    border-radius: 10px;
                    -moz-border-radius: 10px;

                    li {
                        line-height: 1.4rem;
                        padding: 5px;
                        margin-bottom: 5px;
                        text-align: right;
                        cursor: pointer;

                        &.active {
                            background-color: $innerColor;
                            color:  $whiteFont;
                        }

                        &:hover {
                            background-color: $listColor;
                            color: $whiteFont;
                        }
                    }

                    &.hidden {
                        display: none;
                    }
                }
            }

            .language-buttons {
                background-color: $formBackgroundColor;
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
                padding: 0 10px 50px 0;

                button {
                    border-radius: 7px;
                }
            }
        }
    }

    /* Small only */
    @media screen and (max-width: 768px) {
        div#language-modal {
            div.language-box {
                width: 95%;
            }
        }
    }

    /* Medium only */
    @media (min-width: 769px) and (max-width: 992px) {
        div#language-modal {
            div.language-box {
                max-width: 840px;
                min-width: 640px;
            }
        }
    }

    /* Large only */
    @media (min-width: 993px) and (max-width: 2048px) {
        div#language-modal {
            div.language-box {
                max-width: 1240px;
                min-width: 840px;
            }
        }
    }
</style>

<template>
    <div id="language-modal" v-show="show" v-on:click="closeDialogOutside($event)">
        <div class="language-box" v-on:click.stop="">
            <div class="language-label"><h3><span v-text="showLanguage('lang', 'title')"></span><img v-on:click="closeThisDialog()" title="Close" alt="Close" src="/img/close-icon-white.svg"></h3></div>
            <div class="language-content">
                <div class="large-11 medium-11 small-12">
                    <p class="text-info" v-text="showLanguage('lang', 'intro')"></p>
                    <hr>
                    <p class="location"><strong class="title" v-text="showLanguage('lang', 'yourCountry')"></strong>	<span class="data">{{ location.country_name }}</span></p>
                    <p class="location"><strong class="title" v-text="showLanguage('lang', 'yourState')"></strong>		<span class="data">{{ location.region }}</span></p>
                    <p class="location"><strong class="title" v-text="showLanguage('lang', 'yourCity')"></strong>			<span class="data">{{ location.city }}</span></p>
                    <p class="location"><strong class="title" v-text="showLanguage('lang', 'yourPostcode')"></strong>	<span class="data">{{ location.postal }}</span></p>
                    <p class="location"><strong class="title" v-text="showLanguage('lang', 'yourIP')"></strong>	      <span class="data">{{ location.ip }}</span></p>
                    <hr>
                    <p class="text-info" v-text="showLanguage('lang', 'intro2')"></p>
                    <p><strong class="title" v-text="showLanguage('lang', 'yourLanguage')"></strong>	<span class="data">{{ currentLanguageText }}</span></p>
                    <span class="button primary languageSelector" v-on:click="showLanguageSelector()" v-text="showLanguage('lang', 'changeLanguage')"></span>
                    <ul id="languageSelection" class="hidden" ref="languageSelection">
                        <li :class="getCurrentClass('en')" v-on:click="setSelectedLanguage('en')"><span class="float-left" v-text="showLanguage('lang', 'english')"></span> <img :alt="showLanguage('lang', 'english')" src="/img/flags/au.gif" /></li>
                        <li :class="getCurrentClass('zh')" v-on:click="setSelectedLanguage('zh')"><span class="float-left" v-text="showLanguage('lang', 'chinese')"></span> <img :alt="showLanguage('lang', 'chinese')" src="/img/flags/cn.gif" /></li>
                    </ul>
                </div>
            </div>
            <div class="language-buttons">
                <button v-on:click="closeThisDialog()" class="button secondary float-right" type="button" v-text="showLanguage('global', 'close')"></button>
            </div>
        </div>
    </div>
</template>

<script>
    /*
    *   Imports the event bus.
    */
    import { EventBus } from '../../event-bus.js';

    /*
    *   Import the app config
    */
    import { MONGO_CONFIG } from '../../config.js';

    export default {
        /*
        *   Data for the masses !!
        */
        data() {
            return {
                show: false,
                language: 'en',
                currentLanguage: 'en',
                currentLanguageText: MONGO_CONFIG.LANGUAGES['en'],
                location: {},
                currentLocation: {}
            }
        },

        /*
        *   I like getting mounted !!
        */
        mounted() {
            this.currentLanguage = this.$store.getters.getLanguage;

            EventBus.$on('show-language-selector', function() {
                // this seems to be the best option for loading the location data for this modal
                this.location = this.$store.getters.getLocation;
                this.currentLocation = this.$store.getters.getCurrentLocation;
                this.show = true;

            }.bind(this));
        },

        /*
        *   Defines the computed properties.
        */
        computed: {
            /*
            *    Gets whether or not the popout should be shown or not.
            */
            showPopOut() {
                return this.show;
            },

            /*
            *   Determines if we should show the popout.
            */
            displayLanguage() {
                return this.showPopOut;
            },

            /*
            *    Retrieves the User from Vuex
            */
            user() {
                return this.$store.getters.getUser;
            },

            /*
            *   Retrieves the User Load Status from Vuex
            */
            userLoadStatus() {
                return this.$store.getters.getUserLoadStatus;
            },

            /*
            *   Return the local value for watching
            */
            currentLanguageValue() {
                return this.currentLanguage;
            }
        },

        methods: {
            /*
            * Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                //return this.$trans(context, key);
                return this.$store.getters.getLanguageString( context, key );
            },

            getCurrentClass: function(lang) {
                if (lang === this.currentLanguage) {
                    return 'active';
                }
                return '';
            },

            /*
            *   Get current language from store
            */
            getCurrentLanguage() {
                this.currentLanguage = this.$store.getters.getCurrentActivePage;
            },

            showLanguageSelector() {
                this.$jqf(this.$refs.languageSelection).toggle( 'hidden' );
            },

            /*
            *   Set the selected language into the application store
            */
            setSelectedLanguage( lang ) {
                console.log('setting language to: ' + lang);
                this.$store.dispatch('setLanguage', lang );
                this.currentLanguage = lang;
            },

            /*
            *   Get the current location defined for the application
            */
            getCurrentLocation() {
                this.currentLocation = this.$store.getters.getCurrentLocation;
            },

            /*
            *   Get the user location defined by IpInfo
            */
            getLocation() {
                this.location = this.$store.getters.getLocation;
            },

            /*
            *   Its time to close up shop
            */
            closeThisDialog() {
                this.show = false;
            },

            /*
            *   Do the right thing!
            */
            closeDialogOutside( event ) {
                if ($(event.target).is('#language-modal')) {
                    this.closeThisDialog();
                }
            }
        },

        watch: {
            currentLanguageValue: function() {
                this.currentLanguageText = MONGO_CONFIG.LANGUAGES[ this.currentLanguage ];
            }
        }
    }
</script>
