<template>
  <div class="nav_message_notifications_container">
    <header class="nav_message_notifications_header">
      <h4>Messages</h4>
      <div
        @click="closeMessageNotifications"
        class="nav_message_notifications_close"
      >
        <CloseIcon />
      </div>
    </header>
    <div class="nav_message_notifications_divider"></div>
    <div
      v-if="notifications.length > 0"
      class="nav_message_notifications"
    >
      <MessageNotification
        v-for="notification in notifications"
        :key="notification.notification_id"
        :notification="notification"
      />
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import CloseIcon from '../Icons/CloseIcon.vue';
  import MessageNotification from './MessageNotification.vue';

  export default {

    name: 'MessageNotifications',

    components: {
      CloseIcon,
      MessageNotification,
    },


    async mounted() {
      await this.fetchNotifications('App\Notifications\UnreadMessage');
    },

    beforeDestroy() {
        this.CLEAR_MESSAGE_NOTIFICATIONS();
        this.MESSAGE_NOTIFICATIONS_LOADED(false);
    },

    computed: {
        ...mapState('notifications',
          [
            'messageNotificationsLoaded',
            'notifications',
          ]
      ),
    },

    methods: {

      ...mapMutations('notifications',
        [
          'CLOSE_MESSAGE_NOTIFICATIONS',
          'MESSAGE_NOTIFICATIONS_LOADED',
          'CLEAR_MESSAGE_NOTIFICATIONS',
        ]
      ),
      ...mapActions('notifications',
          [
            'FETCH_NOTIFICATIONS'
          ]
      ),

      closeMessageNotifications() {
          this.CLOSE_MESSAGE_NOTIFICATIONS();
      },

      async fetchNotifications(type) {
          await this.FETCH_NOTIFICATIONS(type)
      },
    },
  }


</script>

<style lang="scss">
  .nav_message_notifications_container {
    background-color: rgba(50, 50, 50, 0.9);
    box-shadow: 0px 0px 11px 1px black;
    border-radius: 8px;
    position: absolute;
    top: 50px;
    right: 60px;
    z-index: 19;
    box-sizing: border-box;
    min-width: 250px;
    max-width: 350px;
    min-height: 200px;
    max-height: 275px;
    overflow-y: auto;

       &::-webkit-scrollbar {
          width: 12px;
       }
       &::-webkit-scrollbar-track {
          background: darken($primaryBlack, 10);
          border-radius: 8px;
      }
      &::-webkit-scrollbar-thumb {
          background-color: $themePink;
          border-radius: 20px;
          border: 3px solid darken($primaryBlack, 10);
      }

     h5 {
      text-align: center;
      margin: 0.1rem 0;
      color: $primaryWhite;
      letter-spacing: 0rem;
    }
  }

  .nav_message_notifications_header {
    display: flex;
    align-items: center;

    h4 {
      flex-grow: 1;
      text-align: center;
      margin: 0.1rem 0;
      color: $primaryWhite;
      font-family: "Secular One", sans-serif;
      letter-spacing: 0.03rem;;
    }
}

  .nav_message_notifications_divider {
    box-sizing: border-box;
    width: 100%;
    margin: 0 auto;
    border-bottom: 2px solid #323232;
  }

  .nav_message_notifications_close {
      margin-right: 0.3rem;
      margin-top: 0.5rem;
      svg {
        width: 25px;
        height: 25px;
        color: #fff;
    }
  }

  @media (max-width:600px) {
    .nav_message_notifications_container{
      right: 20px;
      max-width: 250px;
  }
}

</style>