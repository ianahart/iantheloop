<template>
  <transition name="messenger" appear>
  <div v-if="messengerLoaded" class="messenger_container">
    <Header
      :contactsCount="contactsCount"
    />
   <MessengerContacts
      v-if="!getServerErrors.length"
    />
    <p class="no_contacts_explanation" v-if="getServerErrors.length">A contact is someone that you are following and they are following you back.</p>
      <ChatWindow
        v-if="isChatWindowOpen"
        :key="chatWindowReload"

      />
  </div>
  </transition>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import Header from './Header.vue';
  import MessengerContacts from './MessengerContacts.vue';
  import ChatWindow from './ChatWindow.vue';

  export default {
    name: 'MessengerContainer',
    props: {

    },
    components: {
      Header,
      MessengerContacts,
      ChatWindow,
    },

    data () {
      return {

      }
    },
    created() {

    },



    beforeDestroy() {
       this.RESET_MESSENGER_MODULE();
    },

    computed: {
      ...mapState('messenger',
        [
          'contacts',
          'contactsCount',
          'messengerLoaded',
          'isChatWindowOpen',
          'chatWindowReload'
        ]
      ),
      ...mapGetters('messenger',
        [
          'getServerErrors',
        ]
      ),
    },

    methods: {
      ...mapMutations('messenger',
        [
          'RESET_MESSENGER_MODULE'
        ]
      ),
    }
  }
</script>

<style lang="scss">
.messenger-enter-active, .messenger-leave-active {
  transition: all 0.3s ease-out;
}
.messenger-enter, .messenger-leave-to {
  opacity: 0;
  transform: translateX(50px);
}

.messenger_container {
    padding: 0.5rem;
    transform: translateY(78.5%);
    width: 200px;
    position: fixed;
    z-index: 30;
    background-color: #3b3b44;
    color: #fcfcfc;
    border-radius: 8px;
    right: 0;
     box-shadow: 0px 0px 11px 1px rgba(0,0,0,1);
}
.no_contacts_explanation {
  font-size: 0.7rem;
  text-align: center;
  color: darken($primaryWhite, 5);
}

@media (max-width: 600px) {
  .messenger_container {
    transform: translateY(100%);
    min-height: 315px;
  }
}
</style>