<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ServersMain.vue 1001 14/11/20, 11:55 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   ServersMain.vue
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
    .pma-servers-main {
        padding: 0;
        margin: 0;

        .pma-servers-panel {
            float: left;
            height: 100vh;
            width: auto;

            .servers-inner {
                table {
                    border: 1px solid $infoColor;
                    border-radius: 5px;
                    box-shadow: 2px 2px 5px $cccColor;
                }
                table th {
                    background-color: $tableHeaderBg;
                    color: $white;
                    font-size: 1.2rem;
                    padding: 4px;
                }
                table .server-info {
                    background-color: $infoBgColor;
                    padding: 4px;
                    text-align: center;
                }
                table.bordered th.bb {
                    border-bottom: 1px solid $infoColor;
                }
                table.bordered th.rb {
                    border-right: 1px solid $infoColor;
                }
                table.bordered td {
                    border-bottom: 1px solid $infoColor;
                    min-width: 100px;
                    padding: 0 12px 0 12px;
                    text-align: left;
                    position: relative;
                }
                table.bordered td.w50 {
                    min-width: 49.9%;
                    text-align: right;
                }
                table.bordered td.title {
                    width: 15rem;
                }
                table.bordered td.bb {
                    border-bottom: 1px solid $infoColor;
                }
                .activate-checkbox {
                    padding-left: 2rem;

                    input {
                        margin: 0;
                    }
                }
                p.field {
                    text-align: right;
                    width: 350px;

                    .input {
                        border-radius: 5px;
                        padding: 5px;
                        width: 100%;

                        input {
                            margin: 0 0 0 auto;
                            max-width: 266px !important;
                        }
                    }
                }
                .checkbox {
                    margin: 0 0 0 30px !important;
                    width: 1rem;
                }
            }
        }
    }
</style>

<template>
    <div id="pma-servers-main" class="pma-servers-main align-left">
        <servers-view></servers-view>
        <execute-view></execute-view>
        <status-view></status-view>
        <processes-view></processes-view>
        <command-view></command-view>
        <users-view v-bind:account="getUser"></users-view>
        <master-view></master-view>
    </div>
</template>

<script>
    import ServersView from "./ServersView";
    import ExecuteView from "./ExecuteView";
    import StatusView from "./StatusView";
    import ProcessesView from "./ProcessesView";
    import CommandView from "./CommandView";
    import UsersView from "./UsersView";
    import MasterView from "./MasterView";

    export default {
        name: "ServersMain",

        components: {
            ServersView,
            ExecuteView,
            StatusView,
            ProcessesView,
            CommandView,
            UsersView,
            MasterView
        },

        computed: {
            getUser() {
                return this.$store.getters.getUser
            },
        },
    }
</script>
