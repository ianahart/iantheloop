<template>
  <div class="messenger_chat_window">
    <div class="chat_window_header">
      <div class="chat_window_user_indentiy">
        <ProfilePicture
          :userId="getChatWindowUser.id"
          :profilePicture="getChatWindowUser.profile.profile_picture"
          :alt="getChatWindowUser.full_name"
          :status="getChatWindowUser.status"
        />
        <div class="chat_window_user_indentiy_column">
          <h5>{{ getChatWindowUser.formatted_name }}</h5>
          <p>{{ chatWindowUserStatus }}</p>
        </div>
      </div>
      <div
        @click="closeChatWindow"
        class="chat_window_actions"
      >
        <CloseIcon
          className="icon__xsm__light"
        />
      </div>
    </div>
    <div class="chat_window_divider"></div>
    <div class="chat_window_content">
      <ChatMessages />
      <div class="chat_message_input">
        <input
          @change="recordChatMessage($event, getChatWindowUser)"
          @keyup.enter="sendChatMessage"
          :value="chatMessage.recipient.message"
          placeholder="Write a message..."
          type="text"
          name="chat_message"
        />
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import ProfilePicture from './ProfilePicture.vue';
  import CloseIcon from '../Icons/CloseIcon.vue';
  import ChatMessages from './ChatMessages.vue';

  export default {
    name: 'ChatWindow',
    props: {

    },
    components: {
      ProfilePicture,
      CloseIcon,
      ChatMessages,
    },


    computed: {
      ...mapState('messenger',
        [
          'chatMessage'
        ]
      ),
      ...mapGetters('messenger',
        [
          'getChatWindowUser',
        ]
      ),
      ...mapGetters('user',
        [
          'getUserId',
          'userName',
        ]
      ),
      chatWindowUserStatus () {
        return this.getChatWindowUser.status === 'online' ? 'Active now' : 'Not active';
      },

    },
    methods: {
      ...mapMutations('messenger',
        [
          'CLOSE_CHAT_WINDOW',
          'RECORD_CHAT_MESSAGE',
        ]
      ),
      ...mapActions('messenger',
        [
          'SEND_CHAT_MESSAGE'
        ]
      ),

       recordChatMessage(e, user) {
        const message = {
          recipient: {
            recipientID: user.id,
            recipientName: user.formatted_name,
            message: e.target.value,
          },
          sender: {
            senderID: this.getUserId,
            senderName: this.userName,
          },
        }
        this.RECORD_CHAT_MESSAGE(message);


      },

      async sendChatMessage() {
               await this.SEND_CHAT_MESSAGE();
      },
      closeChatWindow() {
        this.CLOSE_CHAT_WINDOW();
      }
    }
  }
</script>

<style lang="scss">
  .messenger_chat_window {
    border-radius: 8px;
    box-sizing: border-box;
    position: absolute;
    right: 217px;
    bottom:0;
    box-shadow: 0px 0px 11px 1px rgba(0,0,0,1);
    background-color: darken($primaryBlack, 5);
    min-height: 200px;
    width: 275px;
  }

  .chat_window_header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.1rem 0;
  }

  .chat_window_user_indentiy {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0.1rem;
  }

  .chat_window_user_indentiy_column {
    display: flex;
    flex-direction: column;
    justify-content: center;
    margin-left: 0.1rem;
    h5 {
      text-align: center;
      font-family: 'Open Sans', sans-serif;
      padding-left: 0.2rem;
      font-size: 0.65rem;
      font-weight: 100;
      margin:0;
      margin-bottom: 0.1rem;
    }

    p {
      margin: 0.1rem 0;
      text-align: left;
      font-family: 'Open Sans', sans-serif;
      padding-left: 0.2rem;
      font-size: 0.65rem;
      font-weight: 100;
    }
  }

  .chat_window_actions {
    svg {
      cursor: pointer;
      width: 20px;
    }
  }
  .chat_window_divider {
    border-bottom:  1px solid $primaryBlack;
    margin: 0.1rem 0;
  }

  .chat_message_input {
    box-sizing: border-box;
    padding: 0.1rem 0.3rem;
    margin: 0 auto;
    margin-bottom: 0.1rem;
    input {
      border-radius: 8px;
      border: none;
      background-color: lighten($primaryBlack, 4);
      height: 30px;
      width: 80%;
      color: darken($primaryWhite, 5);

      &::placeholder {
        color: darken($primaryWhite, 5);
        font-style: italic;
        font-size: 0.65rem;
      }
    }
  }

  @media(max-width:600px) {
    .messenger_chat_window {
      right: 0;
      bottom: 330px;
    }
  }
</style>

