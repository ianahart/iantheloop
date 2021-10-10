<template>
<transition name="user-list" mode="out-in" appear>
  <div ref="userList" v-if="active && canShowUsers" class="users_list_container">
    <div
      @click="selectUserToBlock(user)"
      class="incremental_search_result"
      v-for="user in users"
      :key="user.id"
    >
      <div class="users_list_user_info">
          <div class="users_list_main_user_info">
            <ProfilePicture :src="user.profile.profile_picture" :alt="user.full_name" />
            <p>{{ user.full_name }}</p>
          </div>
          <div class="users_list_sub_user_info">
            <div v-if="user.is_cur_user_following || user.is_user_follower" class="users_list_user_stats">
              <div v-if="user.is_cur_user_following" class="users_list_user_stat">
                <CheckIcon />
                <p>You're Following</p>
            </div>
            <div v-if="user.is_user_follower" class="users_list_user_stat">
              <CheckIcon />
              <p>Follower</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="more_users_list_btn_container">
      <button v-if="pagination.current_page !== pagination.last_page" @click.stop="handleMoreUsers">More Users...</button>
    </div>
  </div>
  </transition>
</template>


<script>
  import { mapState, mapMutations, mapGetters, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import ProfilePicture from './ProfilePicture.vue';
  import CheckIcon from '../Icons/CheckIcon.vue';


  export default {
     name: 'UserList',
     components: {
       ProfilePicture,
       CheckIcon,
     },
     props: {
       users:Array,
       pagination: Object,
       active: Boolean,
       type: String,
     },

     created() {
       this.handleMoreUsers = debounce(this.handleMoreUsers, 300);
     },

      mounted() {
      window.addEventListener('click', this.closeSearch);
    },

    beforeDestroy() {
      window.removeEventListener('click', this.closeSearch);
    },

     computed: {
      ...mapGetters('settings', ['getActiveInput']),
      canShowUsers() {
        return this.users.length && this.getActiveInput.type === this.type;
      },
     },

     methods: {
      ...mapMutations('settings', [
        'RESET_SEARCH_PAGINATION',
        'SET_SEARCH_RESULTS',
        'CLEAR_BLOCK_INPUTS',
        'SET_USER_TO_BLOCK'
      ]),
      ...mapActions('settings', ['SEARCH_NETWORK']),

      selectUserToBlock(user) {
        user.type = this.getActiveInput.type;
        this.SET_USER_TO_BLOCK(user);
      },


      closeSearch(e) {
          if (this.$refs.userList) {
            if (this.getActiveInput.type !== e.target.name) {
              this.clearList();
            }
            if (!this.$refs.userList.contains(e.target) && e.target.tagName.toLowerCase() !== 'input') {
              this.clearList();
            }
          }
        },

      clearList() {
          this.RESET_SEARCH_PAGINATION();
          this.SET_SEARCH_RESULTS({ searches: { data: [] }, initiator: 'clear' });
          if (this.getActiveInput !== undefined) {
             this.CLEAR_BLOCK_INPUTS({ type: this.getActiveInput.type });
          }
      },

       async handleMoreUsers() {
         try {
           await this.SEARCH_NETWORK({ activeInput: this.getActiveInput, initiator: 'click' });
         }catch(e) {
         }
       },
     },
  }
</script>


<style lang="scss">
  .user-list-enter-active, .user-list-leave-active {
    transition: all  0.5s;
  }
  .user-list-enter, .user-list-leave-to  {
    opacity: 0;
  }

  .incremental_search_error {
    color: #fff;
    font-size: 0.8rem;
    text-align: center;
    font-weight: bold;
  }

  .users_list_container {
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    border-radius: 8px;
    border: 1px solid #252526;
    width: 60%;
    overflow-y: auto;
    max-height: 200px;
    min-height: 200px;
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

  .incremental_search_result {
     width: 100%;
     padding: 0.5rem;
     box-sizing: border-box;
     flex-direction: column;
     cursor: pointer;
     display: flex;
     align-items: center;
     &:hover {
      background-color: rgba(200, 200, 200, 0.6);
     }
     p {
       color: #fff;
       font-family: 'Open Sans', sans-serif;
       font-size: 0.85rem;
       margin-left: 1rem;
     }
  }

  .users_list_user_info {
    box-sizing: border-box;
    word-break: break-all;
  }

  .users_list_main_user_info {
      box-sizing: border-box;
      display: flex;
      align-items: center;
    }

  .users_list_sub_user_info {
    box-sizing: border-box;
    width: 80%;
    margin-left: auto;
  }


  .users_list_user_stats {
    width: 100%;
    box-sizing: border-box;
  }

  .users_list_user_stat {
    box-sizing: border-box;
    background-color: darken($themePink, 5);
    padding: 0.15rem 0.2rem;
    justify-content: center;
    display: flex;
    align-items: center;
    border-radius: 8px;
    margin: 0.25rem 0;
    width: 100%;
    color: darken($primaryWhite, 5);
    svg {
      height: 16px;
      width: 16px;
      margin-right: 0.1rem;
    }
    p {
      text-align: center;
      margin:0;
      font-size: 0.6rem;
      width: 100%;
    }
  }

  .more_users_list_btn_container {
    box-sizing: border-box;
    margin: 1.5rem auto;
    button {
      cursor: pointer;
      border: none;
      background-color: transparent;
      color: #817f80;
      font-style: italic;
      font-size: 0.75rem;
      text-align: center;
    }

  }

  @media(max-width:600px) {
   .users_list_container {
     margin: 2rem auto;
     width: 100%;
     max-width: 100%;
   }
  }
</style>
