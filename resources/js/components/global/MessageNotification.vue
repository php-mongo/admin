<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    div.success-notification-container {
        position: fixed;
        z-index: 999999;
        left: 0;
        right: 0;
        top: 0;

        div.success-notification {
            background: $white;
            box-shadow: 0 0 4px 0 rgba(0,0,0,0.12), 0 4px 4px 0 rgba(0,0,0,0.24);
            border-left: 5px solid $messageBorder;
            border-right: 5px solid $messageBorder;
            min-height: 50px;
            line-height: 50px;
            margin: 85px auto auto auto;
            min-width: 400px;
            max-width: 800px;
            color: $noticeColor;
            font-family: "Lato", sans-serif;
            font-size: 16px;

            img {
                margin-right: 20px;
                margin-left: 20px;
            }
        }
    }

</style>

<template>
  <transition name="slide-in-top">
    <div class="success-notification-container" v-show="show">
      <div class="success-notification">
        <img src="/img/progressring.gif"/> {{ successMessage }}
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

        /*
        *   Sets up the component when mounted.
        */
        mounted() {
            /*
            *   On show success, show the notification.
            */
            EventBus.$on('show-message', function( data ) {
                this.successMessage = data.notification;
                this.show = true;

                /*
                After 15 seconds hide the notification.
                */
                setTimeout( function() {
                    this.show = false;

                }.bind(this), 15000);

            }.bind(this));

            /*
            *   Close the message prematurely
            */
            EventBus.$on( 'hide-message', function() {
                this.show = false;

            }.bind(this));
        }
    }
</script>
