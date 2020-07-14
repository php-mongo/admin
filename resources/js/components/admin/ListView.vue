<style lang="scss">

</style>

<template>
    <article id="adListView" class="row grid-container hide-loading" v-show="(showList)">
        <div class="grid-x grid-margin-x small-up-12 medium-up-12 large-up-12 adListView list-block left">
            <list-card v-for="ad in latest" :key="(ad.id + 1)" v-bind:ad="ad"></list-card>
        </div>
    </article>
</template>

<script>
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../event-bus.js';

    /*
    *   Import components for the Gallery View
    */
    import ListCard from '../../components/admin/ListCard.vue';

    export default {
        /*
        *   Register the components to be used by the home page.
        */
        components: {
            ListCard
        },

        /*
        *   Data required for this component
        */
        data() {
            return {
                display: false
            }
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Get the latest ads
            */
            latest() {
                return this.$store.getters.getLatest;
            },

            /*
            *   Returns the show and hide value
            */
            showList() {
                return this.display;
            }
        },

        /*
        * get on ur bikes and ride !!
        */
        mounted() {
            EventBus.$on('change-list', function (data) {
                if (data.show === true) {
                    this.display = true;
                }
                if (data.hide === true) {
                    this.display = false;
                }
            }.bind(this));
        }
    }
</script>
