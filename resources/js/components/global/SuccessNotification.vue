<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    div.success-notification-container {
        position: fixed;
        z-index: 999999;
        left: 0;
        right: 0;
        top: 25vh;

        div.success-notification {
            background: $white;
            box-shadow: 0 0 4px 0 rgba(0,0,0,0.12), 0 4px 4px 0 rgba(0,0,0,0.24);
            border-left: 5px solid $successBorder;
            border-right: 5px solid $successBorder;
            min-height: 50px;
            line-height: 60px;
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
        <img src="/img/success.svg"/> {{ successMessage }}
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
      Defines the data used by the component.
    */
    data() {
      return {
        successMessage: '',
        show: false
      }
    },

    /*
      Sets up the component when mounted.
    */
    mounted(){
      /*
        On show success, show the notification.
      */
      EventBus.$on('show-success', function( data ) {
        this.successMessage = data.notification;
        this.show = true;
        let timer = data.timer ? data.timer : 7000;

        /*
        *  Default display duration is 7 seconds
        */
        setTimeout( function() {
          this.show = false;

        }.bind(this), timer);

      }.bind(this));
    }
  }
</script>
