<template>
   <div
    v-if="result"
    class="search_history_search_result"
    >
    <div class="search_result_user_information_container">
      <svg v-if="type === 'recent'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 recent_search_icon" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
      </svg>
      <div
          @click="goToProfile"
          role="button"
          :style="{paddingTop: `${this.type === 'recent' ? '0' : '0.5rem'}`}"
          class="search_result_user_information"
      >
        <div>
          <img
            v-if="result.profile_picture"
            :src="result.profile_picture"
            :alt="`profile picture of ${result.full_name}`"
          />
          <DefaultProfileIcon v-else/>
          <p
            v-if="type === 'new'"
            v-html="$options.filters.highlight(result.full_name, this.searchValue)"
          >
         </p>
         <p v-else>{{ result.full_name }}</p>
        </div>
        <div
          @click.stop="removeRecentSearch"
          ref='deleteSearch'
          v-if="type === 'recent'" class="delete_recent_search"
        >
          <CloseIcon />
        </div>
      </div>
      <div class="search_result_secondary_user_information">
        <p
          v-if="type === 'new'"
          v-html="$options.filters.highlight(capitalizedCompany, this.searchValue)"
        >
        </p>
        <p v-else>{{ capitalizedCompany }}</p>
        <div v-if="result.cur_user_following && type === 'new'" class="search_result_following_status">
          <p><CheckIcon /> Following</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapMutations, mapGetters, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';
  import CheckIcon from '../Icons/CheckIcon.vue';
  import CloseIcon from '../Icons/CloseIcon.vue';

  export default {
    name: 'Result',
    props: {
      result: Object,
      type:String,
      searchValue: String,
    },
    components: {
      DefaultProfileIcon,
      CheckIcon,
      CloseIcon,

    },

    created() {
      this.removeRecentSearch = debounce(this.removeRecentSearch, 300);
    },

    filters: {
      highlight (value, searchValue) {
        if (!value || !searchValue) {
          return '';
        }
        const match = value.toLowerCase().search(searchValue.toLowerCase());
        let partial = match > -1 ? value.slice(match, match + searchValue.length) : '';
        return value.replace(partial, `<b>${partial}</b>`);
      }
    },
    computed: {
      ...mapGetters('user',
        [
          'getUserId'
        ]
      ),
      capitalizedCompany() {
        return this.result.company.split(' ').map((word) => word.toUpperCase().slice(0,1) + word.toLowerCase().slice(1)).join(' ');
      },
    },
    methods: {

      ...mapActions('userSearch',
        [
          'SAVE_SEARCH_RESULT',
          'REMOVE_RECENT_SEARCH',
        ]
      ),
      async removeRecentSearch () {
        try {
         await this.REMOVE_RECENT_SEARCH({
           id: this.result.id,
           searched_user_id: this.result.searched_user_id,
           searcher_user_id: this.result.searcher_user_id,
         });
        } catch(e) {
        }
      },
      async goToProfile(e) {
        try {
          if (parseInt(this.$route.params?.id) !== parseInt(this.result.searched_user_id)) {
            this.$router.push({ name: 'Profile', params: { id: this.result.searched_user_id } });
         }
          await this.SAVE_SEARCH_RESULT({
              user_id: this.getUserId,
              profile_id: this.result.profile_id,
              searched_user_id: this.result.searched_user_id,
              search_value: this.searchValue
            });
          this.$emit('clear');
        } catch(e) {
        }
      },
    }
  }
</script>

<style lang="scss">
 .search_history_search_result {
    box-sizing: border-box;
    transition: all 0.25s ease-in-out;
    border-bottom: 1px solid #424141;
    &:hover {
      background-color: rgba(0,0,0,0.5);
    }
  }

  .search_result_user_information_container {
    box-sizing: border-box;
    p {
      font-size: 0.8rem;
      font-family: 'Open Sans', sans-serif;
      color: lighten(gray, 12);
    }
  }

  .search_result_user_information_container .recent_search_icon {
      color: darken($primaryWhite, 5);
      background-color: transparent;
      padding-left: 0.3rem;
      padding-bottom: 0.2rem;
      height: 22px;
      width: 22px;
  }

 .search_result_user_information {
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    box-sizing: border-box;
    padding: 0.5rem 0.5rem 0 0.5rem;
    div:first-of-type {
      display: flex;
      justify-content: flex-start;
    }
    div:last-of-type {
      display: flex;
      justify-content: flex-end;;
    }
    p {
      font-size: 0.85rem;
      color: darken($primaryWhite,2);
      b {
        color: $themePink;
      }
    }
    img, svg {
      border-radius: 50%;
      height: 40px;
      width: 40px;
      margin-right: 0.35rem;
    }
    svg {
      color: $themePink;
      background-color: $themeLightBlue;

    }
  }
  .search_result_secondary_user_information{
     display: flex;
     justify-content: space-between;
     padding: 0 0.5rem 0.5rem 0.5rem;
     word-break: break-all;
     p {
       margin:0;
       b {
         color: $themePink;
       }
     }
  }

  .delete_recent_search {
    display: flex;
    align-items: center;
    svg {
      width: 22px;
      height: 22px;
      color: darken($primaryWhite, 5);
      background-color: transparent;
      pointer-events: none;
      path {
        pointer-events: none;
      }
    }
  }

  .search_result_following_status{
      background-color: lightgray;
      border-radius: 8px;
      p, svg{
        color: #535354;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      p {
        padding: 0.3rem 0.2rem;
        border-radius: 8px;
      }
      svg {
        width: 16px;
        height: 16px;
      }
  }
</style>