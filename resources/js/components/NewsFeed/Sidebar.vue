<template>
  <div class="newsfeed_sidebar_container">
    <div class="follow_suggestions_container">
      <FollowSuggestions
        @refill="refillSuggestions"
        @reject="handleReject"
        @follow="handleFollow"
        :followSuggestions="followSuggestions"
      />
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import FollowSuggestions from './FollowSuggestions.vue';

  export default {
    name: 'Sidebar',
    components: {
      FollowSuggestions,
    },
    async created() {
      await this.retrieveFollowSuggestions();
      this.refillSuggestions = debounce(this.refillSuggestions, 350);
    },
    beforeDestroy() {
      this.RESET_NEWSFEED_MODULE();
    },
    computed: {
      ...mapState('newsFeed',
        [
          'followSuggestions'
        ]
      ),
    },
    methods: {
      ...mapMutations('newsFeed',
        [
          'RESET_NEWSFEED_MODULE',
        ]
      ),
      ...mapActions('newsFeed',
        [
          'RETRIEVE_FOLLOW_SUGGESTIONS',
          'UPDATE_FOLLOW_SUGGESTION',
        ]
      ),

      async retrieveFollowSuggestions() {
        await this.RETRIEVE_FOLLOW_SUGGESTIONS();
      },

      async refillSuggestions() {
          await this.RETRIEVE_FOLLOW_SUGGESTIONS();
      },

      async handleReject(payload) {
        await this.UPDATE_FOLLOW_SUGGESTION(payload);
      },

      async handleFollow(payload) {
        await this.UPDATE_FOLLOW_SUGGESTION(payload);
      },
    },
  }
</script>

<style lang="scss">
  .newsfeed_sidebar_container {
    margin: 0.5rem;
    box-sizing: border-box;
    border-radius: 0.25rem;
    background-color: #3b3b441f;
    flex-grow: 1;
    max-width: 500px;
    max-height: 90%;
    height: 600px;
  }
  .follow_suggestions_container {
    box-sizing: border-box;
  }

  @media (max-width:600px) {
    .newsfeed_sidebar_container {
      flex-grow: 1;
      width:100%;
      max-width: 100%;
      margin: 0.5rem auto;
      height: 300px;
    }
  }
</style>