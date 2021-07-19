<template>
  <div class="chat_messages_container">
      <p class="chat_message">Messages go here</p>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'ChatMessages',
    props: {

    },
    components: {

    },
    data () {
      return {

      }
    },
    computed: {

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

  .chat_message {
    font-weight: 100;
    font-size: 0.65rem;;
  }

  @media(max-width: 600px) {
    .chat_messages_container {
      max-height: 240px;
    }
  }

</style>
