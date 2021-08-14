<template>
  <transition name="notification-alert" appear>
    <div v-if="isCurrentInteractionAlertActive" class="dynamic_interaction_alert">
      <div
        @click="dismissInteractionAlert"
        class="dismiss_interaction_alert_container"
      >
        <CloseIcon
          className="icon__sm__dark"
        />
      </div>
      <h3>New Notification</h3>
      <p>You have a new notification from <span><em>{{ currentInteractionAlert.sender_name }}</em></span></p>
    </div>
  </transition>
</template>

<script>

  import { mapState, mapMutations } from 'vuex';
  import CloseIcon from '../Icons/CloseIcon.vue';

  export default {

    name: 'InteractionAlert',
    props: {

    },
    components: {
      CloseIcon,
    },
    data() {
      return {
        timerID: '',
      }
    },
    computed: {
      ...mapState('notifications',
      [
          'isCurrentInteractionAlertActive',
          'currentInteractionAlert',
          'processQueue'
      ]
    ),
    },
    mounted() {
      this.initInteractionAlert();
    },
    beforeDestroy() {
      clearTimeout(this.timerID);

      if (this.isCurrentInteractionAlertActive) {
          this.dismissInteractionAlert();
      }
      this.SET_CURRENT_INTERACTION_ALERT(null);
    },

     methods: {

       ...mapMutations('notifications',
        [
          'SET_CURRENT_INTERACTION_ALERT_ACTIVE',
          'SET_CURRENT_INTERACTION_ALERT'
        ]
      ),

       dismissInteractionAlert(e) {
         this.SET_CURRENT_INTERACTION_ALERT_ACTIVE(false);
         clearTimeout(this.timerID);
       },

       initInteractionAlert() {
          setTimeout(() => {
            if (this.isCurrentInteractionAlertActive) {
              this.SET_CURRENT_INTERACTION_ALERT_ACTIVE(false);
            }
          }, 8000);
       },

    }
  }

</script>

<style lang="scss">

  .notification-alert-enter-active, .notification-alert-leave-active {
    transition: all 0.3s ease-out;
  }
  .notification-alert-enter, .notification-alert-leave-to {
    opacity: 0;
    transform: translateX(-75px);
  }

  .dynamic_interaction_alert {
    padding:0.25rem;
    position: absolute;
    top: 70px;
    left: 50px;
    box-sizing: border-box;
    box-shadow: 0px 0px 11px 1px black;
    background-color: $primaryBlack;
    z-index: 99;
    width: 275px;
    border-radius: 8px;
    overflow-wrap: break-word;
    word-wrap: break-word;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    -moz-hyphens: auto;
    hyphens: auto;
    svg {
      color: $themePink;
      width:20px;
      height:20px;
    }
    h3 {
      font-family: 'Secular One', sans-serif;
      font-weight: bold;
      color: $primaryWhite;
      font-size:0.9rem;
      margin: 0.1rem 0;
      text-align: center;
    }
    p {
      color: $primaryGray;
      font-size: 0.75rem;
      margin:4rem;
      text-align: center;
      font-family: 'Open Sans', sans-serif;

      span {
        font-weight: bold;
        font-size: 0.8rem;

      }
    }
  }

  .dismiss_interaction_alert_container {
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }

  @media(max-width: 600px) {
    .dynamic_interaction_alert {
      width: 235px;
    }
  }

</style>