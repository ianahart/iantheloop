<template>
  <div :class="`user_search_bar_container ${className}`">
    <input
      :value="searchValue"
      @keydown="handleInput"
      @focus="handleFocus"
      :type="type"
      :placeholder="placeholder"
     />
    <SearchIcon v-if="!searchActive"/>
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  import SearchIcon from '../Icons/SearchIcon.vue';
  import { debounce } from '../../helpers/moduleHelpers.js'

  export  default {
    name: 'SearchBar',
    props: {
      type: String,
      className: String,
      placeholder: String,
      searchValue: String,
      userId: Number,
    },
    components: {
     SearchIcon,
    },

    data() {
      return {
        inputFocused: false,
      }
    },

    created() {
      this.handleInput = debounce(this.handleInput, 450);
      this.handleFocus = debounce(this.handleFocus, 250);
    },

    computed: {
      ...mapState('userSearch',
        [
          'searchActive',
          'recentSearches',
          'searchIsFocused',
        ]
      ),
    },
    methods: {
      ...mapMutations('userSearch',
        [
          'SET_SEARCH_VALUE',
          'SET_VALIDATION_ERROR',
          'SET_SEARCH_ACTIVE',
          'CLEAR_SEARCH_RESULTS',
          'CLEAR_RECENT_SEARCHES',
          'SET_SEARCH_IS_FOCUSED',
        ]
      ),
     ...mapActions('userSearch',
       [
         'POPULATE_SEARCH_RESULTS',
         'RECENT_SEARCHES',
       ]
      ),

      async handleFocus() {
        if (!this.searchIsFocused) {
           this.SET_SEARCH_IS_FOCUSED(true);
           await this.RECENT_SEARCHES();
        }
        this.SET_SEARCH_ACTIVE(true);
      },

      validateInput(search) {
        const regex = /^[A-Za-z\s.-]+$/;
        return regex.test(search);
      },

      async handleInput (e) {
        if (this.recentSearches.length) {
          this.CLEAR_RECENT_SEARCHES();
        }
        const search = e.target.value;
        const passedValidation = this.validateInput(search);

        if (!passedValidation && search.length > 0) {
          this.SET_VALIDATION_ERROR('Only letters, spaces, hyphens, and periods permitted.');
          return;
        }
        if (!search.length) {
          this.CLEAR_SEARCH_RESULTS();
        }
        if (e.key.toLowerCase() !== 'backspace' && search.length >= 1) {
          this.SET_VALIDATION_ERROR('');
          this.SET_SEARCH_VALUE(search);
          await this.POPULATE_SEARCH_RESULTS({ userId: this.userId, initiator: 'key' });
        }
      },
    }
  }
</script>
<style lang="scss">
  .user_search_bar_container {
    box-sizing: border-box;
    width: 100%;
    position: relative;
    svg {
      position: absolute;
      top:8px;
      left:4px;
      width: 22px;
      height: 22px;
      color: gray;
    }
    input {
      box-sizing: border-box;
      padding-left: 1.6rem;
    }
  }
</style>