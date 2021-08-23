<template>
  <div class="follow_suggestions">
    <h4>Suggestions</h4>
    <FollowSuggestion
      v-for="followSuggestion in followSuggestions"
      :key="followSuggestion.id"
      :followSuggestion="followSuggestion"
    />
    <div class="refill_suggestions_btn_container">
      <button v-if="endOfFollowSuggestionsCounter !== 2 && !isLoadingData" @click="refill">More Suggestions...</button>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations } from 'vuex';
  import FollowSuggestion from './FollowSuggestion.vue';

  export default {
    name: 'FollowSuggestions',
    props: {
      followSuggestions: Array,
    },
    components: {
      FollowSuggestion,
    },
    computed: {
      ...mapState('newsFeed',
        [
          'endOfFollowSuggestions',
          'endOfFollowSuggestionsCounter',
          'isLoadingData',
        ]
      ),
    },
    methods: {
      refill() {
        this.$emit('refill');
      },
    }
  };
</script>
<style lang="scss">
  .follow_suggestions {
    box-sizing: border-box;
    min-height: 200px;
    max-height: 275px;
    overflow-y: auto;
    p {
      font-family: 'Open Sans', sans-serif;
    }

    h4 {
      padding: 0.4rem;
      text-transform: uppercase;
      color: darken(darkgrey, 3);
      font-family: 'Secular One', sans-serif;
    }

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

  .refill_suggestions_btn_container {
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    margin: 1rem;

    button {
      cursor: pointer;
      border: none;
      background-color: transparent;
      font-family: 'Open Sans', sans-serif;
      font-style: italic;
      color: darken(darkgrey, 10);
    }
  }


</style>
