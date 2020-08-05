<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Admin.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      Admin.vue
  - @subpackage   Id
  - @link         https://github.com/php-mongo/admin PHP MongoDB Admin
  - @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
  - @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
  - @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
  -  php-mongo-admin - License conditions:
  -  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
  -  This web application is available as Free Software and has no implied warranty or guarantee of usability.
  -  See licence.txt for the complete licensing outline.
  -  See COPYRIGHT.php for copyright notices and further details.
  -->

<style lang="scss">
    @import '~@/abstracts/_variables.scss';
    .main-content {
        height: 100vh;
        margin: 0;
        overflow: hidden;
        padding: 0;
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
                show: true
            }
        },

        /*
        *   This component triggers the Ads download from the API
        */
        mounted() {
            this.$store.dispatch( 'loadDatabases' );

            EventBus.$on('no-results-found', function( data ){
                this.errorMessage = data.notification;
                this.show = false;

            }.bind(this));
        }
    }
</script>
