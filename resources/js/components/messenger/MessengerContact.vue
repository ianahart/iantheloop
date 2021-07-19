<template>
  <div @click="openChatWindow(contact)" class="messenger_contact_list_item">
    <ProfilePicture
      :profilePicture="contact.profile.profile_picture"
      :alt="contact.full_name"
      :status="contact.status"
      :userId="contact.id"
    />
    <h5>{{ contact.formatted_name }}</h5>
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

    },

    methods: {
      ...mapMutations('messenger',
        [
          'OPEN_CHAT_WINDOW'
        ]
      ),
      openChatWindow(contact) {
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
      flex-grow: 1;
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
</style>