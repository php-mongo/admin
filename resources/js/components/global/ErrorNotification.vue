<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    div.error-notification-container{
        position: fixed;
        z-index: 999999;
        left: 0;
        right: 0;
        top: 25vh;

        div.error-notification  {
            background: $white;
            box-shadow: 0 0 4px 0 rgba(0,0,0,0.12), 0 4px 4px 0 rgba(0,0,0,0.24);
            border-left: 5px solid $errorBorder;
            border-right: 5px solid $errorBorder;
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
                height: 20px;
            }
        }
    }

</style>

<template>
  <transition name="slide-in-top">
    <div class="error-notification-container" v-show="show">
      <div class="error-notification">
        <img src="/img/error.svg"/> {{ errorMessage }}
          <button>Confirm deletion</button>
          <button>Cancel</button>
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
    data(){
      return {
        errorMessage: '',
        show: false,
        id: null
      }
    },

    computer: {
        confirmDeletion() {
            this.$emit('confirm-deletion', this.id);
        }
    },

    /*
      When mounted, bind the show error event.
    */
    mounted(){
      EventBus.$on('show-deletion', ( data ) => {
        this.errorMessage = data.notification;
        this.show = true;
        this.id   = data;
        // let timer = data.timer ? data.timer : 5000;

        /*
          Default display duration is 5 seconds.
        */
        //setTimeout( function() {
        //  this.show = false;
        //
        //}.bind(this), timer);

      });
    }
  }
</script>
