<template>
  <div class="conversator_chat_window">
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
      <transition name="load-more-chat-messages" appear>
        <div v-if="loadChatMessagesBtn" class="load_more_chat_messages_btn">
          <button @click="loadMoreChatMessages">Older Messages...</button>
        </div>
      </transition>
      <ChatMessages
        v-if="chatMessagesLoaded"
        :chatMessages="chatMessages"
      />
      <div class="chat_message_input">
        <p v-if=" parseInt(this.recipientUserId) === parseInt(getUserId) && isTyping" class="typing_listener">{{ typingUser }} is typing something...</p>
        <input
          @change="recordChatMessage($event, getChatWindowUser)"
          @keyup.enter="sendChatMessage"
          @keydown="listenForTyping"
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
  import { debounce } from '../../helpers/moduleHelpers.js';

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

    data () {
      return {
        typingID: '',
        isTyping: false,
        recipientUserId: '',
        typingUser: '',
      }
    },

    created() {
      this.loadMoreChatMessages = debounce(this.loadMoreChatMessages, 350);
    },

    async mounted() {
      await this.getChatMessages();

      Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
      Echo.private(`chat.${this.conversationId}`)
      .listen('MessageSent', (e) => {
          this.ADD_CHAT_MESSAGE(e.message);
      })
      .listenForWhisper('typing', (e) => {
        this.isTyping = e.isTyping;
        this.recipientUserId = e.recipientUserId;
        this.typingUser = e.user;

        clearTimeout(this.typingID);
        this.typingID = setTimeout(() => {
          this.isTyping = false;
          this.recipientUserId = '';
          this.typingUser = '';

        }, 600);
        });
    },

    async beforeDestroy() {
      await this.UPDATE_USER_COLUMN(
        {
           curUserId: this.getUserId,
           column: 'cur_chat_window_user_id',
           value: 0
      });

      Echo.leave(`chat.${this.conversationId}`);
      this.SET_TOTAL_CHAT_MESSAGES(0);
      this.SET_MORE_CHAT_MESSAGES_BTN(false);
      this.SET_CONVERSATION_ID(null);
      clearTimeout(this.typingID);
    },

    computed: {
      ...mapState('conversator',
        [
          'chatMessage',
          'chatMessages',
          'chatMessagesLoaded',
          'conversationId',
          'loadChatMessagesBtn',
        ]
      ),
      ...mapGetters('conversator',
        [
          'getChatWindowUser',
        ]
      ),
      ...mapGetters('user',
        [
          'getUserId',
          'userName',
          'getToken'

        ]
      ),
      chatWindowUserStatus () {
        return this.getChatWindowUser.status === 'online' ? 'Active now' : 'Not active';
      },

      senderFirstName() {
        const name = this.userName.split(' ')[0];
        return name[0].toUpperCase() + name.slice(1).toLowerCase();
      },

    },
    methods: {
      ...mapMutations('conversator',
        [
          'CLOSE_CHAT_WINDOW',
          'RECORD_CHAT_MESSAGE',
          'RESET_CHAT_MESSAGE_DATA',
          'ADD_CHAT_MESSAGE',
          'SET_CONVERSATION_ID',
          'SET_MORE_CHAT_MESSAGES_BTN',
          'SET_TOTAL_CHAT_MESSAGES',
        ]
      ),
      ...mapActions('conversator',
        [
          'SEND_CHAT_MESSAGE',
          'GET_CHAT_MESSAGES',
        ]
      ),
      ...mapActions('user',
        [
          'UPDATE_USER_COLUMN'
        ]
      ),

      listenForTyping(e) {
        clearTimeout(this.typingID);
        if (e.keyCode === 13) {
          return;
        }
        let channel = Echo.private(`chat.${this.conversationId}`);
        this.typingID = setTimeout(() => {
            this.isTyping = true;
            channel.whisper('typing',
            {
              user: this.senderFirstName,
              isTyping: this.isTyping,
              recipientUserId: this.getChatWindowUser.id,
            }
          );
        }, 300);
      },

      async getChatMessages() {
        await this.GET_CHAT_MESSAGES();
      },

       recordChatMessage(e, user) {
        const message = {
          recipient: {
            recipient_user_id: user.id,
            recipient_name: user.formatted_name,
          },
          sender: {
            sender_user_id: this.getUserId,
            sender_name: this.userName,
            message: e.target.value,
          },
        }
        clearTimeout(this.typingID);
        this.RECORD_CHAT_MESSAGE(message);
      },

      async sendChatMessage() {

        clearTimeout(this.typingID);
        await this.SEND_CHAT_MESSAGE();
        this.isTyping = false;
      },
      closeChatWindow() {
        this.CLOSE_CHAT_WINDOW();
        this.RESET_CHAT_MESSAGE_DATA();
      },

      async loadMoreChatMessages(e) {
         try {
          await this.getChatMessages();
         } catch(e) {
         }
      },
    }
  }
</script>

<style lang="scss">

    .load-more-chat-messages-enter-active {
      transition: all .2s ease;

    }
    .load-more-chat-messages-leave-active {
      transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .load-more-chat-messages-enter, .load-more-chat-messages-leave-to {
      transform: translateX(-30px);
      opacity: 0;
    }



  .conversator_chat_window {
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
    p {
      margin: 0.1rem 0;
      text-align: left;
      font-size: 0.5rem;
      color: gray;
      font-weight: 100;
    }
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

  .load_more_chat_messages_btn {
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0.3rem 0;

    button {
      border: none;
      background: transparent;
      cursor: pointer;
      color: gray;
      font-size: 0.7rem;
      transition: all 0.3 ease-out;
      &:hover {
        color: lighten(gray, 5);
      }
    }

  }

  @media(max-width:600px) {
    .conversator_chat_window {
      right: 0;
      bottom: 330px;
    }
  }
</style>

