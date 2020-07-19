<style lang="scss">
    @import '~@/abstracts/_variables.scss';
    .pma-main-panel {
        height: 100%;
        margin-left: 245px;
        padding: 20px 0 0 20px;
        width:auto;
    }
</style>

<template>
    <div class="pma-main-panel">
        <server-view></server-view>
        <php-mongo></php-mongo>
        <databases-view></databases-view>
        <database-view></database-view>
        <collection-view></collection-view>
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
        },
    }
</script>
