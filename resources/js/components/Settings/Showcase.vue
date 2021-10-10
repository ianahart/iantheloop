<template>
   <div class="settings_showcase">
     <transition name="block-list" appear mode="in-out">
      <div v-if="isBlockListOpen" class="privacy_blocked_user_list_modal">
        <Loader v-if="blockedUsersLoading" />
        <div v-if="!blockedUsersLoading" class="privacy_blocked_user_list_container">
          <div class="privacy_blocked_user_list_header">
            <h4>Currently Blocked Users</h4>
            <div @click="handleBlockListClose">
              <CloseIcon />
            </div>
          </div>
          <div v-if="blockedUsers.length" class="privacy_blocked_user_list">
            <BlockedUsers
              @paginate="handlePagination"
            />
          </div>
          <div v-else>
            <p>You currently do not have any users blocked.</p>
          </div>
        </div>
      </div>
     </transition>
     <div
       v-if="currentSidebarOption.trim() === ''"
       class="settings_default_option"
      >
       <h3>Manage Your Settings Here</h3>
     </div>
     <General
       v-if="currentSidebarOption.toLowerCase() === 'general'"
     />
     <Security
       v-if="currentSidebarOption.toLowerCase() === 'security'"
     />
     Security />
     <Privacy
       v-if="currentSidebarOption.toLowerCase() === 'privacy'"
       @blocklistopen="handleBlockListOpen"
     />
   </div>
</template>

<script>
  import { mapActions, mapMutations, mapState } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import General from './General.vue';
  import Security from './Security.vue';
  import Privacy from './Privacy.vue';
  import CloseIcon from '../Icons/CloseIcon.vue';
  import BlockedUsers from './BlockedUsers.vue';
  import Loader from '../Misc/Loader.vue';


  export default {
    name: 'Showcase',
    components: {
      Privacy,
      General,
      Security,
      CloseIcon,
      BlockedUsers,
      Loader,
    },

    created() {
      this.handlePagination = debounce(this.handlePagination, 300);
    },

    computed: {
      ...mapState('settings',
        [
          'currentSidebarOption',
          'isBlockListOpen',
          'blockedUsers',
          'blockedUsersLoading',
        ]
      ),
    },

    methods: {
     ...mapMutations('settings',
       [
         'SET_IS_BLOCKLIST_OPEN',
         'SET_USER_TO_BLOCK',
         'SET_BLOCKED_USERS',
         'SET_BLOCKED_USERS_URL',
         'SET_BLOCKED_USERS_LOADING',
      ]
    ),
    ...mapActions('settings', ['GET_BLOCKED_USERS']),

    async loadBlockedUsers() {
      try {
        await this.GET_BLOCKED_USERS();
      }catch(e) {
      }
    },

    handleBlockListClose () {
       this.SET_IS_BLOCKLIST_OPEN(false);
       this.SET_BLOCKED_USERS_URL('');
       this.SET_BLOCKED_USERS({ blocked_users: [], action: 'clear' });
    },

    async handlePagination() {
      try {
       await this.GET_BLOCKED_USERS();
      } catch(e) {
      }
    },

     async handleBlockListOpen(payload) {
       this.SET_USER_TO_BLOCK(null);
       this.SET_IS_BLOCKLIST_OPEN(payload);
       this.SET_BLOCKED_USERS_LOADING(true);
       await this.loadBlockedUsers();
     },
    },
  }
</script>

<style lang="scss">

  .block-list-enter-active, .block-list-leave-active {
      transition: all 0.35s;
    }
  .block-list-enter, .block-list-leave-to  {
      opacity: 0;
      transform: perspective(100px) translateZ(30px);
    }

  .settings_showcase {
    box-sizing: border-box;
    background-color: #000;
    max-width: 980px;
    margin: 0 auto;
    padding-top: 4.75rem;
    position: relative;
    overflow: hidden;
  }

  .settings_default_option {
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: center;
    h3 {
      color: $primaryWhite;
      font-family: 'Secular One',sans-serif;
      text-align: center;
    }
  }

  .privacy_blocked_user_list_modal {
    box-sizing: border-box;
    overflow: hidden;
    display: flex;
    align-items: center;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(225,225,225, 0.6);
    z-index: 1;

  }

  .privacy_blocked_user_list_container {
    box-sizing: border-box;
    background-color: #1e1e1e;
    width: 90%;
    margin: 0 auto;
    padding: 0.5rem;
    border-radius: 6px;
  }

  .privacy_blocked_user_list {
    box-sizing: border-box;
  }
  .privacy_blocked_user_list_header {
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    margin: 0 auto;
    width: 90%;
    h4 {
      color: darken($primaryWhite,5);
      letter-spacing: 0.2px;
      font-family:  'Open Sans', sans-serif;
    }
    svg {
      width: 30px;
      height: 30px;
      color: darken($primaryWhite, 5);
      background-color: transparent;
    }
  }

  @media(max-width:1350px) {
    .settings_showcase {
      max-width: 80%;
    }
  }

    @media(max-width:900px) {
    .settings_showcase {
      max-width: 95%;
      width: 95%;
      margin: 0 auto;
      flex-grow: 0;
    }
    .privacy_blocked_user_list_modal {
      display: block;

    }
    .privacy_blocked_user_list_container {
      margin-top: 3rem;
    }
  }
</style>