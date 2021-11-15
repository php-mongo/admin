<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      MessageNotification.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   MessageNotification.vue
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

    div.notification-container {

        div.message-notification {
            background: $white;
            box-shadow: 0 0 4px 0 rgba(0,0,0,0.12), 0 4px 4px 0 rgba(0,0,0,0.24);
            border-left: 5px solid $messageBorder;
            border-right: 5px solid $messageBorder;
            min-height: 50px;
            line-height: 60px;
            margin: 1.1rem auto auto auto;
            min-width: 400px;
            max-width: 920px;
            color: $noticeColor;
            font-family: "Lato", sans-serif;
            font-size: 16px;

            img {
                margin-right: 20px;
                margin-left: 20px;
            }

            .close {
                float: right;
                border: 1px solid $lighterGrey;
                font-weight: bolder;
                margin: 0;
                cursor: pointer;
            }
        }
    }

</style>

<template>
  <transition name="slide-in-top">
    <div class="notification-container" v-show="show">
      <div class="success-notification">
        <img src="img/progressring.gif"/> {{ successMessage }}
        <img class="close" v-on:click="close" src="img/close-icon.svg">
      </div>
    </div>
  </transition>
</template>

<script>
    /*
    Imports the Event Bus to pass events on tag updates
    */
    import { EventBus } from '../../event-bus.js';

    export default {
        /*
        *    Defines the data used by the component.
        */
        data() {
            return {
                successMessage: '',
                show: false
            }
        },

        methods: {
            close() {
                this.show = false;
            }
        },

        /*
        *   Sets up the component when mounted.
        */
        mounted() {
            /*
            *   On show success, show the notification.
            */
            EventBus.$on('show-message', ( data ) => {
                this.successMessage = data.notification;
                this.show = true;

                /*
                After 15 seconds hide the notification.
                */
                setTimeout( function() {
                    this.show = false;

                }.bind(this), 10000);

            });

            /*
            *   Close the message prematurely
            */
            EventBus.$on( 'hide-message', () => {
                this.show = false;

            });
        }
    }
</script>
