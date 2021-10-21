<template>
  <div class="blocked_users_container">
      <div class="blocked_users_list">
        <div
         v-for="blockedUser in blockedUsers"
         :key="blockedUser.blocked_by_list.privacy_id"
        >
        <div class="blocked_users_list_item_unblock_all_type">
            <button @click="unblockAll(blockedUser)">Unblock All</button>
        </div>
        <div class="blocked_users_list_item">
          <div class="blocked_users_list_item_user">
            <ProfilePicture
              :src="blockedUser.profile.profile_picture"
              :alt="blockedUser.full_name"
            />
            <div>
              <p>{{ blockedUser.full_name }}</p>
              <p><span><em>Blocked on:</em></span> {{ blockedUser.blocked_by_list.blocked_date }}</p>
            </div>
          </div>
          <table class="blocked_user_chart">
            <tr>
              <th>Messages</th>
              <th>Profile</th>
              <th>Stories</th>
            </tr>
            <tr>
              <td>
                <div class="block_user_chat_data_cell">
                  <CheckIcon v-if="blockedUser.blocked_by_list.blocked_messages" />
                  <div v-else class="block_user_chat_empty_data_cell"></div>
                  <ToggleBtn
                    @toggle="handleToggle"
                    :isToggled="blockedUser.blocked_by_list.blocked_messages"
                    :data="{id: blockedUser.id, privacy_id: blockedUser.blocked_by_list.privacy_id, type: 'blocked_messages', user: blockedUser.blocked_by_list}"
                  />
                </div>
              </td>
              <td>
                <div class="block_user_chat_data_cell">
                  <CheckIcon v-if="blockedUser.blocked_by_list.blocked_profile" />
                  <div v-else class="block_user_chat_empty_data_cell"></div>
                  <ToggleBtn
                     @toggle="handleToggle"
                    :isToggled="blockedUser.blocked_by_list.blocked_profile"
                    :data="{id: blockedUser.id ,privacy_id: blockedUser.blocked_by_list.privacy_id, type: 'blocked_profile' ,user: blockedUser.blocked_by_list}"
                  />
                </div>
              </td>
              <td>
                <div class="block_user_chat_data_cell">
                 <CheckIcon v-if="blockedUser.blocked_by_list.blocked_stories" />
                 <div v-else class="block_user_chat_empty_data_cell"></div>
                 <ToggleBtn
                    @toggle="handleToggle"
                   :isToggled="blockedUser.blocked_by_list.blocked_stories"
                   :data="{id: blockedUser.id, privacy_id: blockedUser.blocked_by_list.privacy_id, type: 'blocked_stories' ,user: blockedUser.blocked_by_list}"
                 />
                </div>
              </td>
            </tr>
          </table>
        </div>
        </div>
    </div>
    <div class="blocked_users_paginate_btn_container">
      <button v-if="blockedUserURL !== null" @click="emitLoad">See more...</button>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import ProfilePicture from './ProfilePicture.vue';
  import CheckIcon from '../Icons/CheckIcon.vue';
  import ToggleBtn from '../forms/buttons/ToggleBtn.vue';


 export default {
   name: 'BlockedUsers',
   components: {
     ProfilePicture,
     CheckIcon,
     ToggleBtn,
   },

   created () {
     this.handleToggle = debounce(this.handleToggle, 300);
   },

   beforeDestroy() {
     this.SET_BLOCKED_USERS({ blocked_users: [], action: 'clear' });
   },
   computed: {
     ...mapState('settings', [ 'blockedUsers', 'blockedUserURL' ]),
   },
   methods: {
     ...mapMutations('settings',['UPDATE_BLOCKED_TYPE', 'SET_BLOCKED_USERS']),
     ...mapActions('settings',['UPDATE_BLOCKED_USER', 'UNBLOCK_USER']),

     emitLoad() {
       this.$emit('paginate');
     },

     async handleToggle({ data, is_toggled }) {
       try {
         await this.UPDATE_BLOCKED_USER({ data, is_toggled });
       } catch(e) {
       }
     },

     async unblockAll(blockedUser) {
       try {
         await this.UNBLOCK_USER(blockedUser);
       } catch(e) {
       }
     }
   }
 }

</script>
<style lang="scss">
  .blocked_users_container {
    box-sizing: border-box;
    background-color: #3a3a3a;
    overflow-y: auto;
    max-height: 375px;
    min-height: 350px;
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

  .blocked_users_list_container {
    box-sizing: border-box;
  }

  .blocked_users_list {
    box-sizing: border-box;
  }

  .blocked_users_list_item {
    border-bottom: 1px solid #4c4c4a;
    box-sizing: border-box;
    padding: 0 0.5rem 0.5rem 0.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .blocked_users_list_item_user {
    display: flex;
    align-items: center;
    box-sizing: border-box;
    div:nth-child(2) {
      display: flex;
      flex-direction: column;
      p:first-of-type {
        font-size: 0.8rem;
        color: darken($primaryWhite, 5);
      }
      p:last-of-type {
        font-size: 0.7rem;
        color: #8d8788;
        span em {
           color: $primaryWhite;
        }
      }
    }
    p {
      font-family: 'Open Sans', sans-serif;
      margin: 0.1rem;
    }
    img, svg {
      width: 50px;
      height:50px;
      border-radius: 50%;
      margin-right: 0.65rem;
    }
    svg {
      color: $themePink;
      background-color: $themeLightBlue;
    }
  }

  .blocked_users_list_item_unblock_all_type {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.25rem;
    margin-right: 0.25rem;
    button {
      border: 1px solid #fb4d70;
      border-radius: 20px;
      padding: 0.25rem;
      font-size: 0.7rem;
      color: darken($primaryWhite, 15);
      background-color: transparent;
      cursor: pointer;
      &:hover {
        background-color: #1e1e1e;
        color: darken($primaryWhite, 5);
        border: 1px solid #6c717b;
      }
    }
  }

  .blocked_users_paginate_btn_container {
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    margin: 1.5rem 0;
    button {
      cursor: pointer;
      background-color: transparent;
      color: $themePink;
      font-style: italic;
      font-size: 0.8rem;
      border:none;
    }
  }

  .blocked_user_chart {
    box-sizing: border-box;
    border-collapse: collapse;
    margin-left: 1rem;
    border-spacing: 0;
    width: 100%;
    th, td {
      text-align: left;
      font-size: 0.7rem;
      color: darken($primaryWhite, 7);
    }
    }

    .block_user_chat_data_cell {
      box-sizing: border-box;
      svg {
        height: 20px;
        width: 20px;
        color: rgb(30, 158, 30);
        background-color: transparent;
      }
    }
    .block_user_chat_empty_data_cell {
      height: 24px;
      width: 20px;
      background-color: transparent;
    }


  @media(max-width:600px) {
    .blocked_users_container {
      max-height: 200px;
      min-height: 200px;
    }
    .blocked_users_list_item {
      flex-direction: column;
    }
    .blocked_user_chart {
      width: 95%;
      margin-left: 0;
      padding-left: 0.25rem;
    }
  }

</style>