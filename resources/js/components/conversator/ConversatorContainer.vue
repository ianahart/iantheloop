<template>
  <transition name="conversator" appear>
  <div v-if="conversatorLoaded" class="conversator_container">
    <Header
      :contactsCount="contactsCount"
    />
   <ConversatorContacts
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
  import ConversatorContacts from './ConversatorContacts.vue';
  import ChatWindow from './ChatWindow.vue';

  export default {
    name: 'ConversatorContainer',
    props: {

    },
    components: {
      Header,
      ConversatorContacts,
      ChatWindow,
    },

    data () {
      return {

      }
    },

    beforeDestroy() {
      Echo.leave(`unreadmessage.${this.getUserId}`);
      if (this.conversationId !== null) {
        Echo.leave(`chat.${this.conversationId}`);
      }
       this.RESET_CONVERSATOR_MODULE();
    },

    computed: {
      ...mapState('conversator',
        [
          'contacts',
          'contactsCount',
          'conversatorLoaded',
          'isChatWindowOpen',
          'chatWindowReload',
          'conversationId',
        ]
      ),
      ...mapGetters('conversator',
        [
          'getServerErrors',
        ]
      ),
      ...mapGetters('user',
        [
          'getUserId'
        ]
      ),
    },

    methods: {
      ...mapMutations('conversator',
        [
          'RESET_CONVERSATOR_MODULE'
        ]
      ),
    }
  }
</script>

<style lang="scss">
.conversator-enter-active, .conversator-leave-active {
  transition: all 0.3s ease-out;
}
.conversator-enter, .conversator-leave-to {
  opacity: 0;
  transform: translateX(50px);
}

.conversator_container {
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
  .conversator_container {
    transform: translateY(100%);
    min-height: 315px;
  }
}
</style>