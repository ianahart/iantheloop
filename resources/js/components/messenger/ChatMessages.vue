<template>
  <div v-if="chatMessagesLoaded" class="chat_messages_container">
    <ChatMessage
      v-for="chatMessage in chatMessages"
      :key="chatMessage.id"
      :chatMessageData="chatMessage"
      :curUserId="getUserId"
   />
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import ChatMessage from './ChatMessage.vue';

  export default {

    name: 'ChatMessages',
    props: {
      chatMessages: Array,
    },
    components: {
      ChatMessage,
    },
    data () {
      return {

      }
    },

    computed: {
      ...mapState('messenger',
        [
           'chatMessagesLoaded'
        ]
      ),
      ...mapGetters('user',
        [
          'getUserId'
        ]
      ),
    },
    methods: {

    }
  }
</script>

<style lang="scss">

  .chat_messages_container {
    box-sizing: border-box;
    display: flex;
    flex-direction: column-reverse;
    padding: 0.4rem;
    min-height: 200px;
    max-height: 275px;
    box-sizing: border-box;
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

  @media(max-width: 600px) {
    .chat_messages_container {
      max-height: 240px;
    }
  }

</style>
