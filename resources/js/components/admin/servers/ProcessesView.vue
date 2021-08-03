<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ServersView.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   ServersView.vue
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
    #pma-processes-view {
        table {
            max-width: 97%;

            thead {
                .title {
                    font-weight: 800;
                    background-color: $cccColor;
                }
                .trbg {
                   background-color: $tableRowBg;
                }
            }
            tbody {
                .tdrb {
                    td {
                        border-right: 1px solid $cccColor;
                    }
                }
            }
        }
    }
</style>

<template>
    <div id="pma-processes-view" class="pma-servers-panel align-left" v-show="show">
        <div class="servers-inner">
            <div>
                <div v-if="errorMessage">{{ errorMessage }}</div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="11" class="title"><span v-text="showLanguage('server', 'processList')"></span> (db.$cmd.sys.inprog.find({$all:1}))</th>
                        </tr>
                        <tr class="trbg">
                            <td v-text="showLanguage('server', 'processId')"></td>
                            <td v-text="showLanguage('server', 'processThreadId')"></td>
                            <td v-text="showLanguage('server', 'processNameSpace')"></td>
                            <td v-text="showLanguage('server', 'processDescription')"></td>
                            <td v-text="showLanguage('server', 'processClient')"></td>
                            <td v-text="showLanguage('server', 'processActive')"></td>
                            <td v-text="showLanguage('server', 'processLockType')"></td>
                            <td v-text="showLanguage('server', 'processWaiting')"></td>
                            <td v-text="showLanguage('server', 'processSeconds')"></td>
                            <td v-text="showLanguage('server', 'processOperation')"></td>
                            <td v-text="showLanguage('server', 'processClientMetadata')"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!processes">
                            <td colspan="11">
                                <span v-text="showLanguage('server', 'processesNone')"></span>
                            </td>
                        </tr>
                        <tr class="tdrb" v-for="(process, index) in processes" :key="index" v-bind:process="process">
                            <td v-html="getString(process.connectionId)"></td>
                            <td v-html="getString(process.threadId)"></td>
                            <td v-html="getString(process.ns)"></td>
                            <td v-html="getString(process.desc)"></td>
                            <td v-html="getString(process.client)"></td>
                            <td v-html="getString(process.active)"></td>
                            <td v-html="getString(process.locks)"></td>
                            <td v-html="getString(process.waitingForLock)"></td>
                            <td v-html="getString(process.secs_running)"></td>
                            <td v-html="getString(process.opid)"></td>
                            <td v-html="getString(process.clientMetadata)"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    /*
     * Import the Event bus
     */
    import { EventBus } from '../../../event-bus.js';

    export default {
        name: "ProcessesView",

        /*
         *   Data required for this component
         */
        data() {
            return {
                show: false,
                processes: [],
                errorMessage: null,
                index: 0,
                limit: 55
            }
        },

        /*
         *   Define methods for the server component
         */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            getString(object) {
                return this.$convObj().jsonH(JSON.stringify(object));
            },

            getProcesses() {
                this.$store.dispatch('getServerProcesses');
                this.handleProcesses();
            },

            handleProcesses() {
                let status = this.$store.getters.getLoadServerProcesses;
                if (status === 1 && this.index < this.limit) {
                    this.index+=1;
                    setTimeout(() => {
                        this.handleProcesses();
                    }, 200);
                }
                if (status === 2) {
                    console.log("status: " + status);
                    this.processes = this.$store.getters.getServerProcesses;
                }
                if (status === 3) {
                    this.errorMessage = this.showLanguage('server', 'processError')
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
            }
        },

        /*
        *    get on ur bikes and ride !!
        */
        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent();
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-processes', () => {
                this.showComponent();
                this.getProcesses();
            });
        }
    }
</script>
