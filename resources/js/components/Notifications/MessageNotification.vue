<template>
  <div
    @mouseleave.prevent="handleMouseLeave"
    @mouseover.prevent="handleMouseOver"
    :class="`nav_message_notification ${notificationStyle}`"
  >
    <div class="nav_message_notification_content">
      <div v-if="unreadMessage.new_notifications" class="unread_notification_identifier"></div>
      <UserPicture
        :src="unreadMessage.profile_picture"
        :alt="unreadMessage.sender_name"
      />
    <p>You have some unread message(s) from <span>{{ unreadMessage.sender_name }}</span></p>
     <p v-if="unreadMessage.latest_read_at !== null" id="nav_message_notification_read_at">{{ unreadMessage.latest_read_at }}</p>
    </div>
    <div v-if="isNotificationHovered" class="nav_message_notification_actions">
      <div
        v-if="unreadMessage.new_notifications"
        @click="markReadNotification(unreadMessage)"
      >
        <CheckIcon
          className="icon__sm__dark"
        />
      </div>
      <div
        @click="removeNotification(unreadMessage)"
      >
        <TrashCanIcon
          className="icon__sm__dark"
        />
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import UserPicture from './UserPicture.vue';
  import TrashCanIcon from '../Icons/TrashCanIcon.vue';
  import CheckIcon from '../Icons/CheckIcon.vue';

  export default {
    name: 'MessageNotification',
    props: {
      unreadMessage: Object,
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
      ...mapState('conversator',
        [
          'isConversatorOpen'
        ]
      ),

      notificationStyle() {
        return this.unreadMessage.new_notifications ? 'nav_unread_messages' : 'nav_read_messages';
      }
    },

    methods: {

      ...mapMutations('notifications',
        [
          'MARK_NOTIFICATION_AS_READ',
        ]
      ),

      ...mapMutations('conversator',
        [
          'CLEAR_NOTIFICATIONS',
        ]
      ),

      ...mapActions('notifications',
        [
          'UPDATE_MESSAGE_NOTIFICATIONS',
          'DELETE_MESSAGE_NOTIFICATIONS',
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

      async markReadNotification(unreadMessage) {
        this.MARK_NOTIFICATION_AS_READ(unreadMessage);
        await this.UPDATE_MESSAGE_NOTIFICATIONS(
          {
            sender: unreadMessage.sender_user_id,
            recipient: unreadMessage.recipient_user_id,
            type: 'App/Notifications/UnreadMessage',
          }
        );

        this.clearConversatorNotifications(unreadMessage);
      },

      clearConversatorNotifications(unreadMessage) {
           if (this.isConversatorOpen) {
            this.CLEAR_NOTIFICATIONS(
              {
                sender: parseInt(unreadMessage.sender_user_id),
                notificationsRead: true
              });
            }
      },

      async removeNotification(unreadMessage) {

        await this.DELETE_MESSAGE_NOTIFICATIONS(
          {
            sender: unreadMessage.sender_user_id,
            recipient: unreadMessage.recipient_user_id,
            type: 'App/Notifications/UnreadMessage',
          }
        );
          this.clearConversatorNotifications(unreadMessage);
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
    flex-wrap: wrap;
    p {
      margin-left: 0.1rem;
      font-size: 0.8rem;
      font-family: 'Open Sans', sans-serif;
      margin: 0.5rem auto 0.2rem auto;
      text-align: center;

      span {
        font-weight: bold;
        font-family: "Secular One", sans-serif;
      }
    }
  }

 #nav_message_notification_read_at {
    text-align: left;
    margin:0;
    font-size: 0.65rem;
    color: $themePink;
    margin-bottom: 0.25rem;
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
    color: #B6B4C1;
    font-style: italic;
  }

  .nav_unread_messages {
      background-color: darken($primaryBlack, 5);
      color: #dcdcdc;
      font-style: normal;
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