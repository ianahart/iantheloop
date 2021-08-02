<template>
  <div
    @mouseleave.prevent="handleMouseLeave"
    @mouseover.prevent="handleMouseOver"
    :class="`nav_message_notification ${notificationStyle}`"
  >
    <div class="nav_message_notification_content">
      <div v-if="notification.new_notifications" class="unread_notification_identifier"></div>
      <UserPicture
        :src="notification.profile_picture"
        :alt="notification.sender_name"
      />
    <p>You have some unread message(s) from <span>{{ notification.sender_name }}</span></p>
    </div>
    <div v-if="isNotificationHovered" class="nav_message_notification_actions">
      <div
        v-if="notification.new_notifications"
        @click="markReadNotification(notification)"
      >
        <CheckIcon
          className="icon__sm__dark"
        />
      </div>
      <div
        @click="removeNotification(notification)"
      >
        <TrashCanIcon
          className="icon__sm__dark"
        />
      </div>
    </div>
  </div>
</template>

<script>
// new_notifications:true
// notification_id:"234d47a4405ace49301a03f74ef05f3b"
// profile_picture:"https://hart-looped.s3.amazonaws.com/60e5cf4a0cf4elesly-2.jpeg"
// recipient_user_id:"17"
// sender_name:"Lesly Small"
// sender_user_id:"53"
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import UserPicture from './UserPicture.vue';
  import TrashCanIcon from '../Icons/TrashCanIcon.vue';
  import CheckIcon from '../Icons/CheckIcon.vue';

  export default {
    name: 'MessageNotification',
    props: {
      notification: Object,
    },
    components: {
      UserPicture,
      TrashCanIcon,
      CheckIcon,
    },
    data () {
      return {
        isNotificationHovered: false,
      }
    },

    mounted() {

    },

    computed: {
      ...mapState('messenger',
        [
          'isMessengerOpen'
        ]
      ),

      notificationStyle() {
        return this.notification.new_notifications ? 'nav_unread_messages' : 'nav_read_messages';
      }
    },

    methods: {

      ...mapMutations('notifications',
        [
          'MARK_NOTIFICATION_AS_READ'
        ]
      ),

      ...mapMutations('messenger',
        [
          'CLEAR_NOTIFICATIONS'
        ]
      ),

      ...mapActions('notifications',
        [
          'UPDATE_NOTIFICATIONS'
        ]
      ),

      handleMouseOver(e) {
        if (!this.isNotificationHovered) {
            this.isNotificationHovered = true;
        }

      },
      handleMouseLeave(e) {
        if (this.isNotificationHovered) {
            this.isNotificationHovered = false;
        }
      },

      async markReadNotification(notification) {
        this.MARK_NOTIFICATION_AS_READ(notification);
        await this.UPDATE_NOTIFICATIONS(
          {
            sender: notification.sender_user_id,
            recipient: notification.recipient_user_id,
            type: 'App/Notifications/UnreadMessage',
          }
        );

        if (this.isMessengerOpen) {
          this.CLEAR_NOTIFICATIONS(
            {
              sender: parseInt(notification.sender_user_id),
              notificationsRead: true
            });
        }
      },

      removeNotification(notification) {
        // delete
        console.log(`Removing notification from ${notification.sender_name}`);
      }
    },
  }


</script>


<style lang="scss">
  .nav_message_notification {
    box-sizing: border-box;
    cursor: pointer;
    position: relative;
  }

  .nav_message_notification_content {
    box-sizing: border-box;
    padding: 0.5rem;
    padding-bottom:0;
    display: flex;
    align-items: center;
    p {
      margin-left: 0.1rem;
      font-size: 0.8rem;
      font-family: 'Open Sans', sans-serif;
      margin: 1rem auto;
      text-align: center;
      color: #dcdcdc;

      span {
        font-weight: bold;
        font-family: "Secular One", sans-serif;
      }
    }
  }



  .nav_message_notification_actions {
    box-sizing: border-box;
    padding: 0.5rem;
    padding-top: 0;
    display: flex;
    justify-content: space-evenly;

    svg {
      height: 18px;
      border: 2px solid $themeLightBlue;
      border-radius: 0.25rem;
      padding: 2px;
      color: $themePink;

      &:hover {
        color: lighten($themeLightBlue, 10);
        background-color: darken($primaryBlack, 5);
      }


    }
  }
  .nav_read_messages {
    background-color: transparent;
  }

  .nav_unread_messages {
      background-color: darken($primaryBlack, 5);
  }
  .unread_notification_identifier {
    height: 8px;
    width: 8px;
    border-radius: 50%;
    background-color: #a9a5a5;
    position: absolute;
    top:5px;
    left:10px;
  }
</style>