<template>
  <div v-if="chatMessageData" class="chat_message_container">
    <div :class="`chat_message_content ${userStyles}`">
      <ProfilePicture
        :profilePicture="chatMessageData.profile_picture"
        :alt="chatMessageData.sender_name"
        :userId="chatMessageData.sender_user_id"
      />
      <p class="chat_message_text">{{ chatMessageData.message }}</p>
    </div>
    <div :class="`chat_message_sent_text ${dateStyles}`">
      <p>{{ chatMessageData.message_sent }}</p>
    </div>

  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import ProfilePicture from './ProfilePicture.vue';


  export default {

    name: 'ChatMessage',
    props: {
      chatMessageData: Object,
      curUserId: Number,
    },
    components: {
      ProfilePicture,
    },

    computed: {
      userStyles() {
        return this.chatMessageData.sender_user_id === this.curUserId ? 'sender_user_styles' : 'recipient_user_styles';
      },
      dateStyles() {
        return this.chatMessageData.sender_user_id === this.curUserId ? 'sender_user_date_styles' : 'recipient_user_date_styles';
      }
    },
    methods: {

    }
  }
</script>

<style lang="scss">

  .chat_message_container {
    box-sizing: border-box;
  }

  .chat_message_content {
    box-sizing: border-box;
    display: flex;
    margin: 0.1rem;

    img {
      width: 20px;
      height: 20px;
    }
    svg {
      height: 20px;
      width: 20px;
    }
  }

 .chat_message_text {
      margin:0;
      margin-bottom: 0.1rem;
      font-size: 0.6rem;
      font-weight: 100;
      color: $primaryWhite;
  }

.chat_message_sent_text{
    box-sizing: border-boxs;
    p {
      font-size: 0.5rem;
      margin: 0.1rem 0;
      color: gray;
      font-weight: 100;
    }
  }

.recipient_user_styles {
   justify-content: flex-start;

   p {
     padding-left:0.3rem;
     padding-top: 0.2rem;
   }
}

.recipient_user_date_styles {
  display: flex;
  justify-content: flex-start;
}

.sender_user_date_styles {
  display: flex;
  justify-content: flex-end;
}

.sender_user_styles {
  flex-direction: row-reverse;
  p {
    padding-right: 0.3rem;
    padding-top: 0.2rem;
  }
}
</style>