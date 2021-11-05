<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ClusterMember.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources\js\components
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
    .cluster-member {
        width: 95% !important;
        height: auto !important;
        margin-left: 100px;

        ul.member-data {
            list-style: none;
        }

        li::marker {
            display: none;
            color: #fff;
        }

        span {
            text-decoration: none;

            &::marker {
                display: none;
            }
        }

        hr {
            width: 100%;
            margin: 0 0 15px 0;
        }
    }
</style>

<template>
    <div class="cluster-member pma-servers-panel align-left" v-show="show">
        <div class="server-inner">
            <u class="member-data" v-if="member">
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('global', 'name')"></span>:</span>
                        {{ member.name }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'members.health')"></span>:</span>
                        {{ member.health }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'members.stateStr')"></span>:</span>
                        {{ member.stateStr }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'members.configTerm')"></span>:</span>
                        {{ member.configTerm }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'members.configVersion')"></span>:</span>
                        {{ member.configVersion }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'members.lastHeartbeatDate')"></span>:</span>
                        {{ member.lastHeartbeatDate }}
                    </p>
                </li>
                <li>
                    <p>
                        <span class="u-pull-left title"><span v-text="showLanguage('cluster', 'members.lastHeartbeatMessage')"></span>:</span>
                        {{ member.lastHeartbeatMessage }}
                    </p>
                </li>
            </u>
            <hr>
        </div>
    </div>
</template>

<script>
    /*
     * Import the Event bus
     */
    import { EventBus } from '../../../event-bus.js';

    export default {
        name: "ClusterMember",

        props: ['member'],

        data() {
            return {
                show: false
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
                this.show = false
            }
        },

        /*
        *    get on ur bikes and ride !!
        */
        mounted() {
            this.showComponent()
        }
    }
</script>
