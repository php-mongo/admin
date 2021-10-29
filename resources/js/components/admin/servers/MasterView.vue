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
    #pma-cluster-view {
        ul {
            list-style: none !important;
        }

        li::marker {
            display: none;
            color: #fff;
        }

        p {
            margin-bottom: 0.5rem;
            display: inline-block;
            width: 100%;
        }

        span {
            text-decoration: none;

            &::marker {
                display: none;
            }
        }

        .title {
            display: inline-block;
            min-width: 170px;
            font-weight: bold;
        }
    }
</style>

<template>
    <div id="pma-cluster-view" class="pma-servers-panel align-left" v-show="show">
        <div class="servers-inner">
            <h3 v-text="showLanguage('cluster', 'clusterOverview')"></h3>
            <ul v-if="replication">
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('global', 'ok')"></span>:</span>
                        {{ replication.ok }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'set')"></span>:</span>
                        {{ replication.set }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'clusterTime')"></span>:</span>
                        {{ replication.clusterTime }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'appliedOpTime')"></span>:</span>
                        {{ replication.appliedOpTime }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'votingMembersCount')"></span>:</span>
                        {{ replication.votingMembersCount }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'majorityVoteCount')"></span>:</span>
                        {{ replication.majorityVoteCount }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'operationTime')"></span>:</span>
                        {{ replication.operationTime }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'membersTitle')"></span>:</span>
                    </p>
                    <cluster-member v-for="(member, key) in replication.members" v-bind:key="key" v-bind:member="member"></cluster-member>
                </li>
            </ul>
            <p v-if="!replication && !error" v-text="showLanguage('cluster', 'loading')"></p>
            <p class="form-error" v-if="error" v-text="error"></p>
            <p class="text-info" v-if="noCluster" v-text="showLanguage('cluster', 'noCluster')"></p>
        </div>
    </div>
</template>

<script>
    /*
     * Import the Event bus
     */
    import { EventBus } from '../../../event-bus.js';

    import ClusterMember from "./ClusterMember";

    export default {
        name: "MasterView",

        components: {
            ClusterMember
        },

        /*
         *   Data required for this component
         */
        data() {
            return {
                error: null,
                index: 0,
                limit: 525,
                message: null,
                noCluster: null,
                replication: null,
                show: false,
            }
        },

        /*
         *   Defines the computed properties on the component.
         */
        computed: {
            //
        },

        /*
         *   Define methods for the server component
         */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key )
            },

            handleReplicationData() {
                let status = this.$store.getters.getReplicationDataStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index++;
                    setTimeout(() => {
                        this.handleReplicationData()
                    }, 50)
                }
                if (status === 2) {
                    this.replication = this.$store.getters.getReplicationData
                }
                if (status === 3) {
                    this.error = this.$store.getters.getServerErrorDataMessage;
                    if (this.error === 'not running with --replSet') {
                        this.noCluster = true;
                    }
                }
            },

            /*
             *   Show component
             */
            showComponent() {
                // load all server replication data for the current connection
                this.$store.dispatch('getReplicationData');
                this.show = true;
                this.handleReplicationData()
            },

            /*
             *   Hide component
             */
            hideComponent() {
                this.show = false
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
                this.hideComponent()
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-master', () => {
                this.showComponent()
            });
        }
    }
</script>
