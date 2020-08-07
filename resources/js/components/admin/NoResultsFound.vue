<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      NoResultsFound.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   NoResultsFound.vue
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

    div.no-results-notification-container{
        position: fixed;
        z-index: 999999;
        left: 0;
        right: 0;
        top: 0;
        text-align: center;

        div.noresult-notification{
            background: $white;
            box-shadow: 0 0 4px 0 rgba(0,0,0,0.12), 0 4px 4px 0 rgba(0,0,0,0.24);
            border-left: 5px solid $red;
            height: 50px;
            line-height: 50px;
            margin: auto;
            min-width: 400px;
            max-width: 640px;
            margin-top: 150px;
            color: $errorColor;
            font-family: "Lato", sans-serif;
            font-size: 16px;

            img{
                margin-right: 20px;
                margin-left: 20px;
                height: 20px;
            }
        }
    }

</style>

<template>
    <transition name="slide-in-top">
        <div class="no-results-notification-container" v-show="show">
            <div class="noresult-notification">
                <img src="/img/error.svg"/> {{ errorMessage }}
            </div>
        </div>
    </transition>
</template>

<script>
    /*
    *   Imports the Event Bus to pass events on tag updates
    */
    import { EventBus } from '../../event-bus.js';

    export default {
        /*
          Defines the data used by the component.
        */
        data(){
            return {
                errorMessage: 'No data found - please try again later',
                show: false
            }
        },

        /*
          When mounted, bind the show error event.
        */
        mounted(){
            EventBus.$on('no-results-found', ( data ) => {
                this.errorMessage = data.notification;

                this.show = true;

                /*
                  Hide the error notification after 3 seconds.
                */
                setTimeout( function(){
                    this.show = false;

                }.bind(this), 30000);

            });
        }
    }
</script>
