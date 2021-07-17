<template>
  <div v-if="contacts.length" class="messenger_contacts_list">
    <MessengerContact
      v-for="contact in contacts"
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
    },

    methods: {

    }
  }
</script>

<style lang="scss">

  .messenger_contacts {
    box-sizing: border-box;
  }

</style>