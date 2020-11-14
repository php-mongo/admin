<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Profile.vue 1001 28/9/20, 10:24 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Profile.vue
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
    #pma-profile {
        ul {
            list-style: none;
            margin-left: 0;

            li {
                display: inline-block;
                padding: 10px 20px;
                border-bottom: 1px solid $cccColor;
                /* border-bottom-color: $cccColor; */
                border-left: 1px solid $cccColor;
                /* border-left-color: $cccColor; */
            }
        }
        .result {
            border: 1px solid $cccColor;
            margin: 5px;
            padding: 5px;
        }
    }
</style>

<template>
    <div id="pma-profile" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('profile', 'title')"></h3>
        </div>
        <div>
            <ul>
                <li>
                    <span class="pma-link" @click="showTab('profile')" v-text="showLanguage('profile', 'profile')"></span>
                </li>
                <li>
                    <span class="pma-link" @click="showTab('form')" v-text="showLanguage('profile', 'setLevel')"></span>
                </li>
                <li>
                    <span class="pma-link" @click="clearLevel" v-text="showLanguage('profile', 'clear')"></span>
                </li>
            </ul>
        </div>
        <div class="header">
            <p class="msg" v-show="errorMessage || actionMessage">
                <span class="error">{{ errorMessage }}</span>
                <span class="action">{{ actionMessage }}</span>
            </p>
        </div>
        <div v-show="activeTab === 'form'" class="profile-form">
            <form class="panel-form">
                <p>
                    <label for="set-level" v-text="showLanguage('profile', 'selectLevel')"></label>
                    <select id="set-level" v-model="form.level">
                        <option value="0" v-text="showLanguage('profile','option-0')"></option>
                        <option value="1" v-text="showLanguage('profile','option-1')"></option>
                        <option value="2" v-text="showLanguage('profile','option-2')"></option>
                    </select>
                </p>
                <p>
                    <label for="milliseconds" v-text="showLanguage('profile', 'milliseconds')"></label>
                    <input id="milliseconds" type="number" v-model="form.milliseconds">
                </p>
                <p>
                    <br>
                    <button class="button" @click="saveProfile" v-text="showLanguage('profile', 'save')"></button>
                </p>
            </form>
        </div>
        <div v-if="profile">
            <div v-show="activeTab === 'profile' && profile.length >= 1" class="profile-results">
                <div class="result" v-for="(doc, index) in profile" :key="index" :doc="doc">
                    <p class="date"><strong v-text="showLanguage('profile', 'date')"></strong> {{ getDate( doc['ts']['$date']['$numberLong'])}}</p>
                    <p v-html="prepDoc( JSON.stringify(doc) )"></p>
                    <p>&nbsp;</p>
                </div>
            </div>
            <div v-show="activeTab === 'profile' && profile.length === 0" class="profile-results">
                <div class="result">
                    <p><strong v-text="showLanguage('profile', 'empty')"></strong></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        name: "Profile",

        /*
         *  Component data container
         */
        data() {
            return {
                actionMessage: null,
                activeTab: 'profile',
                currentLevel: JSON.parse(sessionStorage.getItem('level')) || 0,
                errorMessage: null,
                index: 0,
                limit: 75, // limit the status check iterations
                show: false,
                form: {
                    database: null,
                    level: 0,
                    milliseconds: 100,
                    useCurrentDatabase: true
                },
                profile: null
            }
        },

        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key, str ) {
                if (str) {
                    return this.$store.getters.getLanguageString( context, key ).replace("%s", str);
                }
                return this.$store.getters.getLanguageString( context, key );
            },

            prepDoc( doc ) {
                return this.$convObj().jsonH(doc);
            },

            getDate( ts ) {
              return new Date(ts);
            },

            showTab(tab) {
                this.clear();
                this.activeTab = tab;
            },

            clearLevel() {
                this.currentLevel = 0;
                this.form.level = 0;
                this.form.milliseconds = 100;
                this.saveProfile();
                this.actionMessage = 'Level reset to 0';
            },

            clear() {
                this.actionMessage = '';
                this.errorMessage = '';
                this.index = 0;
            },

            /*
            *   The database will already be loaded, therefore we should be able to retrieve data when 'show' is triggered'
            */
            getDatabase() {
                this.data = this.$store.getters.getDatabase;
                if (this.data) {
                    this.form.database = this.data.db.databaseName;
                }
            },

            setLevels() {
                let level = this.$store.getters.getLevel;
                if (level) {
                    this.currentLevel      = level.was;
                    this.form.level        = level.was;
                    this.form.milliseconds = level.slowms;
                }
            },

            saveProfile() {
                this.clear();
                this.$store.dispatch('saveDbProfile', { database: this.form.database, params: this.form });
                this.handleSaveProfile();
            },

            handleSaveProfile() {
                let status = this.$store.getters.getSaveProfileStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleSaveProfile();
                    }, 250);
                }
                if (status === 2) {
                    // gtg
                    this.actionMessage = this.showLanguage('profile', 'success');
                    // ToDo: we really need to request an update from the server - the result does not contain the new levels etc
                    this.getProfile();
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('profile', 'error');
                }
            },

            getProfile() {
                this.index = 0;
                this.$store.dispatch('getDbProfile', { database: this.form.database });
                this.handleProfile();
            },

            handleProfile() {
                let status = this.$store.getters.getProfileStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleProfile();
                    }, 250);
                }
                if (status === 2) {
                    // gtg
                    this.profile = this.$store.getters.getProfile;
                    this.setLevels();
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('profile', 'errorFetching');
                }
            },

            /*
            *   Show component
            */
            showComponent() {
                this.show = true;
            },

            /*
            *   Hide component
            */
            hideComponent() {
                this.show = false;
            },
        },

        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent();
            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels',() => {
                this.hideComponent();
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-profile', () => {
                this.showComponent();
                this.getDatabase();
                this.getProfile();
            });
        },
    }
</script>
