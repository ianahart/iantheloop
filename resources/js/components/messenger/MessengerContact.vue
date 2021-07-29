<template>
  <div @click="openChatWindow(contact)" class="messenger_contact_list_item">
    <ProfilePicture
      :profilePicture="contact.profile.profile_picture"
      :alt="contact.full_name"
      :status="contact.status"
      :userId="contact.id"
    />
    <div class="messenger_contact_name">
      <p v-if="contact.unread_messages_count > 0" class="messenger_contact_unread_messages_count">
        {{ contact.unread_messages_count }}
      </p>
      <h5>{{ contact.formatted_name }}</h5>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import ProfilePicture from './ProfilePicture.vue';


  export default {
    name: 'MessengerContact',
    props: {
      contact: Object,
    },
    components: {
      ProfilePicture,
    },

    data () {

      return {

      }
    },
    computed: {
      ...mapState('messenger',
        [
          'isChatWindowOpen'
        ]
      ),
    },

    methods: {
      ...mapMutations('messenger',
        [
          'OPEN_CHAT_WINDOW',
          'CLOSE_CHAT_WINDOW',
          'RELOAD_CHAT_WINDOW'
        ]
      ),
      openChatWindow(contact) {
        this.RELOAD_CHAT_WINDOW();

        if (this.isChatWindowOpen) {

          this.CLOSE_CHAT_WINDOW();

        }
          this.OPEN_CHAT_WINDOW(contact);
      }
    }
  }

</script>

<style lang="scss">

  .messenger_contact_list_item {
    box-sizing: border-box;
    display: flex;
    padding: 0.1rem;
    align-items: center;
    justify-content: space-between;
    margin: 0.1rem auto;
    border-radius: 8px;
    transition: all 0.1ss ease-out;
    cursor: pointer;
    &:hover {
      background-color: darken($primaryBlack, 5);
    }

    h5 {
      margin: 0.1rem 0;
      // flex-grow: 1;
      text-align: center;
      font-family: 'Open Sans', sans-serif;
      padding-left: 0.2rem;
      font-size:0.65rem;
      font-weight: 100;

      &:hover {
        color: #fff;
      }
    }
  }

  .messenger_contact_name {
    position: relative;
    flex-grow: 1;
  }

  .messenger_contact_unread_messages_count {
    position: absolute;
    background-color: $error;
    color: $primaryWhite;
    right: 95%;
    top: -15px;
    margin: 0;
    font-size: 0.6rem;
    padding: 0.5rem;
    box-sizing: border-box;
    border-radius: 50%;
    height: 15px;
    width: 15px;
    display: flex;
    justify-content: center;
    align-items: center;

  }
</style>