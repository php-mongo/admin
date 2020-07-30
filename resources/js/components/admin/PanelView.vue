<style lang="scss">
    @import '~@/abstracts/_variables.scss';
    .pma-main-panel {
        height: 95vh;
        margin-left: 245px;
        overflow-x: auto;
        padding: 20px 0 20px 20px;
        width:auto;

        .pma-main-inner {
            margin: 0;
            width: 88vw;
        }
    }
</style>

<template>
    <div id="pma-main-panel" class="pma-main-panel" ref="pmapanel">
        <div class="pma-main-inner" ref="pmainner">
            <server-view></server-view>
            <php-mongo></php-mongo>
            <databases-view></databases-view>
            <database-view></database-view>
            <collection-view></collection-view>
        </div>
    </div>
</template>

<script>
    /*
    *   Import components for the Panel View
    */
    import ServerView from "./server/ServerView";
    import PhpMongo from "./phpmongo/PhpMongo";
    import DatabasesView from "./databases/DatabasesView";
    import DatabaseView from "./database/DatabaseView";
    import CollectionView from "./database/collection/CollectionView";

    export default {
        /*
        *   Register the components to be used within the main panel views.
        */
        components: {
            ServerView,
            PhpMongo,
            DatabasesView,
            DatabaseView,
            CollectionView
        }
    }
</script>
