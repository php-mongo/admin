<style lang="scss">
    @import '~@/abstracts/_variables.scss';
    .pma-top-panel {
        background-color: $navBgColor;
        margin-left: 245px;
        padding-bottom: 2px;
        position: relative;
        top: 0;
        z-index: 88;

        .pma-link {
            color: $linkColor;
            cursor: pointer;
        }
    }

    /* Small only - (max-width: 39.9375em) */
    @media screen and (max-width: 769px) {
        .pma-top {

        }
    }

    /* Medium only - (min-width: 40em) and (max-width: 63.9375em) */
    @media (min-width: 769px) and (max-width: 992px) {
        .pma-top {

        }
    }

    /* Large only - (min-width: 64em) and (max-width: 74.9375em) */
    @media (min-width: 993px) and (max-width: 2048px) {
        .pma-top {

        }
    }
</style>

<template>
    <div id="pma-top-panel" class="pma-top-panel">
        <top></top>
        <top-nav></top-nav>
    </div>
</template>
<script>
    /*
    *   Import components for the Gallery View
    */
    import Top  from "./Top";
    import TopNav from "./TopNav";

    export default {
        /*
       *   Registers child components with the component.
       */
        components: {
            Top,
            TopNav
        }
    }
</script>
