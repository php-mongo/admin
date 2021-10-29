<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Repair.vue 1001 28/9/20, 10:25 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Repair.vue
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
    <div id="pma-repair" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('repair', 'title')"></h3>
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
        name: "Repair",

        /*
         *  Component data container
         */
        data() {
            return {
                actionMessage: null,
                database: 'n/a',
                errorMessage: null,
                index: 0,
                limit: 55, // limit the status check iterations
                show: false
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

            launchRepair() {
                EventBus.$emit('action-confirmation', { notification: 'Run the repair process on ' + this.database + ' database?', element: 'repair' })
            },

            clear() {
                this.actionMessage = '';
                this.errorMessage  = '';
            },

            runRepair() {
                this.clear();
                this.$store.dispatch('repairDb', { database: this.database });
                this.handleRepair();
            },

            handleRepair() {
                let status = this.$store.getters.getRepairStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleRepair();
                    }, 250);
                }
                if (status === 2) {
                    // gtg
                    this.actionMessage = this.showLanguage('repair', 'success');
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('repair', 'error');
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
            EventBus.$on('show-database-repair', () => {
                this.showComponent();
                this.getDatabase();
                this.launchRepair();
            });

            EventBus.$on('confirm-action-repair', () => {
                this.runRepair();
            })
        },
    }
</script>
