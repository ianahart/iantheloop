<template>
  <div class="newsfeed_sidebar_container">
    <div class="follow_suggestions_container">
      <FollowSuggestions
        @refill="refillSuggestions"
        :followSuggestions="followSuggestions"
      />
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import FollowSuggestions from './FollowSuggestions.vue';

  export default {
    name: 'Sidebar',
    props: {

    },
    components: {
      FollowSuggestions,
    },
    data () {
      return {
        debounceID: '',
      }
    },
    async created() {
      await this.retrieveFollowSuggestions();
    },
    beforeDestroy() {
      this.RESET_NEWSFEED_MODULE();
      clearTimeout(this.debounceID);
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
          'RETRIEVE_FOLLOW_SUGGESTIONS'
        ]
      ),
      async retrieveFollowSuggestions() {
        await this.RETRIEVE_FOLLOW_SUGGESTIONS();
      },
      async refillSuggestions() {
        this.debounce(async () => {
          await this.RETRIEVE_FOLLOW_SUGGESTIONS();
        }, 300);

      },
      debounce(fn, delay = 400) {
        return ((...args) => {
            clearTimeout(this.debounceID);

            this.debounceID = setTimeout(() => {
                this.debounceID = null;

                fn(...args);
            }, delay);
        })();
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
  }
  .follow_suggestions_container {
    box-sizing: border-box;
  }

  @media (max-width:600px) {
    .newsfeed_sidebar_container {
      flex-grow: 1;
      width:100%;
      max-width: 100%;
      margin: 0;
    }
  }
</style>