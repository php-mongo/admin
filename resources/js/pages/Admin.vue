<style lang="scss">
    @import '~@/abstracts/_variables.scss';
    .main-content {
        padding: 0;
        margin: 0;
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
