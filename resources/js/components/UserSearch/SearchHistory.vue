<template>
  <transition name="fade-search-history" appear>
    <div ref="searchHistory" class="search_history_container">
      <p class="search_history_validation_error">{{ validationError }}</p>
      <div class="search_history_content_container">
        <div
          @click="removeAllRecentSearches"
          v-if="recentSearches.length && !searchResults.length"
          class="clear_recent_searches_btn_container"
        >
          <TrashCanIcon />
          <button>Clear all</button>
        </div>
        <div v-if="searchResults.length && !recentSearches.length" class="search_history_search_results">
         <Result
           v-for="searchResult in searchResults"
           :key="searchResult.searched_user_id"
           :result="searchResult"
           :searchValue="searchValue"
           @clear="clearHistory"
           type="new"
          />
          <div v-if="paginationSearchURL !== null" class="search_pagination_btn_container">
            <button @click="loadMoreResults">See more results...</button>
          </div>
        </div>
        <div v-if="recentSearches.length && !searchResults.length" class="search_history_recent_searches">
          <Result
           v-for="recentSearch in recentSearches"
           :key="recentSearch.searched_user_id"
           :result="recentSearch"
           :searchValue="''"
           @clear="clearHistory"
           type="recent"
          />
          <div class="search_pagination_btn_container">
            <button
              v-if="paginationRecentSearchURL !== null && paginationRecentSearchURL !== ''"
              @click="loadMoreRecentSearches">More recent searches...</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
  import { mapState, mapMutations, mapActions, mapGetters } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import Result from './Result.vue';
  import TrashCanIcon from '../Icons/TrashCanIcon.vue';

  export default {
    name: 'SearchHistory',
    components: {
      Result,
      TrashCanIcon,
    },
    props: {
      validationError:String,
    },

    created() {
       this.loadMoreResults = debounce(this.loadMoreResults, 350);
       this.loadMoreRecentSearches = debounce(this.loadMoreRecentSearches, 350);
       this.removeAllRecentSearches = debounce(this.removeAllRecentSearches, 350);
    },
    mounted() {
      window.addEventListener('click', this.closeSearchHistory);
    },
    beforeDestroy() {
      window.removeEventListener('click', this.closeSearchHistory);
    },
    computed: {
      ...mapState('userSearch',
        [
          'searchResults',
          'recentSearches',
          'searchValue',
          'paginationSearchURL',
          'paginationRecentSearchURL',
        ]
      ),
      ...mapGetters('user',
        [
          'getUserId'
        ]
      ),
    },
    methods: {
      ...mapMutations('userSearch',
        [
          'SET_SEARCH_ACTIVE',
          'CLEAR_SEARCH_RESULTS',
          'SET_SEARCH_VALUE',
          'CLEAR_RECENT_SEARCHES',
          'SET_PAGINATION_RECENT_SEARCH_URL',
          'SET_SEARCH_IS_FOCUSED',
        ]
      ),
      ...mapActions('userSearch',
        [
          'POPULATE_SEARCH_RESULTS',
          'RECENT_SEARCHES',
          'REMOVE_RECENT_SEARCH'
        ]
      ),

      closeSearchHistory(e) {
          if (!this.$refs.searchHistory.contains(e.target) && e.target.tagName.toLowerCase() !== 'input') {
            this.clearHistory();
          }
      },

      async loadMoreRecentSearches() {
         try {
            await this.RECENT_SEARCHES();
         } catch(e) {
         }
      },

      async loadMoreResults() {
         try {
           await this.POPULATE_SEARCH_RESULTS({ userId: this.getUserId, initiator: 'btn' });
         } catch(e) {
         }
      },

      async removeAllRecentSearches() {
        try{
           await this.REMOVE_RECENT_SEARCH({
           id: null,
           searched_user_id: null,
           searcher_user_id: this.getUserId,
         });
        }catch(e) {
        }
      },

      clearHistory() {
        this.SET_SEARCH_ACTIVE(false);
        this.SET_SEARCH_VALUE('');
        this.CLEAR_SEARCH_RESULTS();
        this.CLEAR_RECENT_SEARCHES();
        this.SET_PAGINATION_RECENT_SEARCH_URL('');
        this.SET_SEARCH_IS_FOCUSED(false);
      },
    },
  }
</script>

<style lang="scss">

  .fade-search-history-enter-active,
  .fade-search-history-leave-active {
    transition: all 0.3s;
  }
  .fade-search-history-enter,
  .fade-search-history-leave-to {
    opacity: 0;
  }

  .search_history_container {
    word-break: break-all;
    box-sizing: border-box;
    border-radius: 2px;
    z-index: 1;
    width: 100%;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    background-color: #3b3b44;
    position: absolute;
    overflow-y: auto;
    top:60px;
    left: 0;
    max-width: 400px;
    min-height: 200px;
    max-height: 205px;
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

  .search_history_content_container {
    box-sizing: border-box;
    word-break: break-all;
  }

  .clear_recent_searches_btn_container {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin: 0.7rem 0.5rem 0 0;
    margin-left: auto;
    cursor: pointer;
    width: 120px;
    svg {
      color: #9f9f9f;
      height: 22px;
      width: 22px;
      background-color: transparent;
    }
    button {
      color: #9f9f9f;
      border:none;
      background-color: transparent;
      cursor: pointer;
    }
  }

  .search_history_container p.search_history_validation_error {
    font-size: 0.75rem;
    color: darken(darkgrey, 7);
    margin: 0;
    text-align: center;
    margin-top: 0.3rem;
  }

  .search_history_search_results {
    box-sizing: border-box;
  }

  .search_pagination_btn_container {
    display: flex;
    margin: 1.2rem 0;
    justify-content: center;
    button {
      font-style: italic;
      color: #9f9f9f;
      border: none;
      background-color: transparent;
      cursor: pointer;
    }
  }


  @media(max-width:600px) {
    .search_history_container {
      width: 100%;
      min-height: 170px;
      max-height: 175px;
      max-width: 100%;
    }
  }
</style>
