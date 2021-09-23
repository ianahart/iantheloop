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
      v-if="unreadMessages.length > 0"
      class="nav_message_notifications"
    >
      <MessageNotification
        v-for="unreadMessage in unreadMessages"
        :key="unreadMessage.notification_id"
        :unreadMessage="unreadMessage"
      />
    </div>
    <div v-if="currentPageMessages !== 'end'" class="paginate_message_notifications">
      <button @click="loadMoreNotifications('App/Notifications/UnreadMessage')">More notifications...</button>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import CloseIcon from '../Icons/CloseIcon.vue';
  import MessageNotification from './MessageNotification.vue';

  export default {

    name: 'MessageNotifications',

    components: {
      CloseIcon,
      MessageNotification,
    },

    created () {
      this.loadMoreNotifications = debounce(this.loadMoreNotifications, 350);
    },

    async mounted() {
      await this.fetchNotifications('App/Notifications/UnreadMessage');
    },

    beforeDestroy() {
        this.CLEAR_MESSAGE_NOTIFICATIONS();
        this.MESSAGE_NOTIFICATIONS_LOADED(false);
        this.SET_CURRENT_PAGE_MESSAGES('reset');
    },

    computed: {
        ...mapState('notifications',
          [
            'messageNotificationsLoaded',
            'unreadMessages',
            'currentPageMessages'
          ]
      ),
    },

    methods: {

      ...mapMutations('notifications',
        [
          'CLOSE_MESSAGE_NOTIFICATIONS',
          'MESSAGE_NOTIFICATIONS_LOADED',
          'CLEAR_MESSAGE_NOTIFICATIONS',
          'SET_CURRENT_PAGE_MESSAGES',
        ]
      ),
      ...mapActions('notifications',
          [
            'FETCH_MESSAGE_NOTIFICATIONS'
          ]
      ),

      async loadMoreNotifications(type) {
          await this.FETCH_MESSAGE_NOTIFICATIONS(type);
      },

      closeMessageNotifications() {
          this.CLOSE_MESSAGE_NOTIFICATIONS();
      },

      async fetchNotifications(type) {
          await this.FETCH_MESSAGE_NOTIFICATIONS(type)
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
      color: $themePink;
      font-family: 'Secular One', sans-serif;
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

  .paginate_message_notifications {
    box-sizing: border-box;
    margin: 0.5rem 0 1rem 0;
    display: flex;
    justify-content: center;

    button {
      color: $themePink;
      font-style: italic;
      letter-spacing: 0.030rem;
      border: none;
      background-color: transparent;
      cursor: pointer;
      font-family: 'Open Sans', sans-serif;

      &:hover {
        color: lighten(gray, 5);
      }
    }
  }


  @media (max-width:600px) {
    .nav_message_notifications_container{
      right: 20px;
      max-width: 250px;
  }
}

</style>