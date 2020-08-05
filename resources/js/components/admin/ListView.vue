<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ListView.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      ListView.vue
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
            EventBus.$on('change-list', (data) => {
                if (data.show === true) {
                    this.display = true;
                }
                if (data.hide === true) {
                    this.display = false;
                }

            });
        }
    }
</script>
