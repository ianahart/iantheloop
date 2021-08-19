<template>
  <div class="follow_suggestions">
    <h5>Follow Suggestions Here</h5>
    <button v-if="endOfFollowSuggestionsCounter !== 2" @click="refill">More Suggestions</button>
    <FollowSuggestion
      v-for="followSuggestion in followSuggestions"
      :key="followSuggestion.id"
      :followSuggestion="followSuggestion"
    />
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
  border: 1px solid blue;
}
</style>
