<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Drop.vue 1001 28/9/20, 10:26 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Drop.vue
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
    /* @import '~@/abstracts/_variables.scss'; */

</style>

<template>
    <div id="pma-database-drop" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('dropdb', 'title')"></h3>
        </div>
        <div class="header">
            <p class="msg" v-show="errorMessage || actionMessage">
                <span class="error">{{ errorMessage }}</span>
                <span class="action">{{ actionMessage }}</span>
            </p>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        name: "Drop",

        /*
         *  Component data container
         */
        data() {
            return {
                show: false,
                actionMessage: null,
                errorMessage: null,
                database: null,
                index: 0,
                limit: 55
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

            /*
            *   The database will already be loaded, therefore we should be able to retrieve data when 'show' is triggered'
            */
            getDatabase() {
                this.data = this.$store.getters.getDatabase;
                if (this.data) {
                    this.database = this.data.db.databaseName;
                }
            },

            dropDatabase() {
                EventBus.$emit('delete-confirmation', { notification: this.showLanguage('dropdb', 'confirmDelete', this.database), id: this.database, element: 'dropdb' });
            },

            runDrop(db) {
                if (db === this.database) {
                    this.$store.dispatch('deleteDatabase', [this.database]);
                    this.handleDropDb();
                }
            },

            handleDropDb() {
                let status = this.$store.getters.getDeleteDatabaseStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleDropDb();
                    }, 250);
                }
                if (status === 2) {
                    // gtg
                    EventBus.$emit('show-success', { notification: this.showLanguage('dropdb', 'success', this.database ) });
                    EventBus.$emit('hide-panels');
                    EventBus.$emit('show-databases');
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('dropdb', 'error');
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

            EventBus.$on('confirm-delete-dropdb',(db) => {
                this.runDrop(db);
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-drop', () => {
                this.showComponent();
                this.getDatabase();
                this.dropDatabase();
            });
        },
    }
</script>
