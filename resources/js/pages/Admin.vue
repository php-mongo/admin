<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Admin.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Admin.vue
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
    .main-content {
        height: 100vh;
        margin: 0;
        overflow: hidden;
        padding: 0;

        /*.header {
            font-weight: 800;
            font-size: 1.1rem;
        }*/

        nav {
            ul {
                li {
                    &:hover {
                        background-color: $lighterGrey;
                        color: $bodyFontColor;
                    }
                }
            }
        }

        table {
            td {
                input[type="submit"], input[type="button"] {
                    &:hover {
                        background-color: $lightGrey;
                        color: $bodyFontColor;
                    }
                }
            }
        }
    }
</style>

<template>
    <div id="main-page-content" class="off-canvas-content main-content" v-show="show">
        <dbs-view></dbs-view>
        <top-view></top-view>
        <panel-view></panel-view>
        <div style="clear: both;"></div>
    </div>
</template>

<script>
    /*
    *   Imports the Event Bus to pass events on tag updates
    */
    import { EventBus } from '../event-bus.js';

    /*
    *   Imports components
    */
    import DbsView from '../components/admin/dbs/DbsView';
    import TopView from '../components/admin/top/TopView';
    import PanelView from "../components/admin/PanelView";

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            DbsView,
            TopView,
            PanelView
        },

        data() {
            return {
                errorMessage: null,
                index: 0,
                limit: 100,
                show: true
            }
        },

        methods: {
            handleCheckUser() {
                let status = this.$store.getters.getUserLoadStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index++;
                    setTimeout(() => {
                        this.handleCheckUser()
                    }, 100)
                }
                if (status === 2) {
                    console.log("user loaded - try to fetch databases");
                    this.$store.dispatch( 'loadDatabases' )
                }
                if (status === 3) {
                    // user not authorized or other error
                }
            },
        },

        /*
        *   This component triggers the Ads download from the API
        */
        mounted() {
            this.handleCheckUser()
        }
    }
</script>
