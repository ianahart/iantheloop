<template>
  <div v-if="contacts.length" class="messenger_contacts_list">
    <MessengerContact
      v-for="contact in getContacts"
      :key="contact.id"
      :contact="contact"
    />

  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import MessengerContact from './MessengerContact.vue';

  export default {

    name: 'MessengerContacts',
    props: {

    },
    components: {
      MessengerContact,
    },

    data () {

      return {

      }
    },

    computed: {
      ...mapState('messenger',
        [
          'contacts'
        ]
      ),
      ...mapGetters('messenger',
        [
          'getContacts'
        ]),
    },

    methods: {

    }
  }
</script>

<style lang="scss">

  .messenger_contacts_list {
    box-sizing: border-box;
    min-height: 375px;
    max-height: 400px;
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
  }

  @media(max-width:600px) {
    .messenger_contacts_list {
          max-height: 275px;
          min-height: 0;
    }
  }

</style>