<template>
  <div class="user_search_component_container">
    <SearchBar
      type="text"
      placeholder="Search for user, company..."
      className="forms__input__field_lg"
      :searchValue="searchValue"
      :userId="getUserId"
    />
    <SearchHistory
      v-if="searchActive"
      :validationError="validationError"
    />
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex';

  import SearchBar from './SearchBar.vue';
  import SearchHistory from './SearchHistory.vue';

  export default {
    name: 'UserSearch',
    components: {
      SearchBar,
      SearchHistory,
    },
    computed: {
        ...mapState('userSearch',
        [
          'searchValue',
          'searchActive',
          'validationError'
        ]
      ),
      ...mapGetters('user',
        [
          'getUserId'
        ]
      ),
    },
    methods: {

    },
  }
</script>

<style lang="scss">
  .user_search_component_container {
    box-sizing: border-box;
    width: 85%;
    position: relative;
  }
</style>
