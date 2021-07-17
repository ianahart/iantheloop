<template>
  <transition name="messenger" appear>
  <div class="messenger_container">
    <Header
      :contactsCount="contacts_count"
    />
    <MessengerContacts />
  </div>
  </transition>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import Header from './Header.vue';
  import MessengerContacts from './MessengerContacts.vue';

  export default {
    name: 'MessengerContainer',
    props: {

    },
    components: {
      Header,
      MessengerContacts,
    },

    data () {
      return {

      }
    },
    created() {

    },
    mounted() {

    },

    beforeDestroy() {
       this.RESET_MESSENGER_MODULE();
    },

    computed: {
      ...mapState('messenger',
        [
          'contacts',
          'contacts_count'
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
  box-sizing: border-box;
  padding: 0.5rem;
  top: 200px;
  height: 500px;
  min-height: 400px;
  width: 200px;
  position: fixed;
  z-index: 30;
  background-color: $primaryBlack;
  overflow-x: hidden;
  overflow-y: auto;
  color: $primaryWhite;
  bottom: 50px;
  border-radius: 8px;
  right: 0;
  box-shadow: 0px 0px 11px 1px rgba(0,0,0,1);
}

@media (max-width: 600px) {
  .messenger_container {
    bottom: 115px;
    height: 300px
  }
}
</style>