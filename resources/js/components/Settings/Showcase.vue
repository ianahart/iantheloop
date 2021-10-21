<template>
   <div class="settings_showcase">
     <transition name="block-list" appear mode="in-out">
      <div v-if="isBlockListOpen" class="settings_showcase_modal">
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
            <p class="no_current_blocked_users_message">You currently do not have any users blocked.</p>
          </div>
        </div>
      </div>
     </transition>
     <div v-if="security.is_proceed_modal_open" class="settings_showcase_modal">
       <div class="security_settings_change_password_consent">
         <div class="security_settings_change_password_consent_heading">
          <h3>Change Password</h3>
          <div @click="closePasswordForm">
            <CloseIcon />
          </div>
         </div>
         <p>After changing your password you will be logged out. You can then log in using your updated password.</p>
         <div class="security_settings_change_password_btns">
           <button @click.stop="handlePasswordChange" class="settings_security_btn">Change</button>
           <button @click.stop="closePasswordForm" class="settings_security_btn">Cancel</button>
         </div>
       </div>
     </div>
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
      this.handlePasswordChange = debounce(this.handlePasswordChange,300);
    },

    computed: {
      ...mapState('settings',
        [
          'currentSidebarOption',
          'isBlockListOpen',
          'blockedUsers',
          'blockedUsersLoading',
          'security',
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
         'UPDATE_SECURITY_PROP',
         'UPDATE_SECURITY_PASSWORD_FORM',
         'RESET_SETTINGS_MODULE',
      ]
    ),
    ...mapActions('settings', ['GET_BLOCKED_USERS', 'CHANGE_PASSWORD']),

    async loadBlockedUsers() {
      try {
        await this.GET_BLOCKED_USERS();
      }catch(e) {
      }
    },

    closePasswordForm() {
        this.UPDATE_SECURITY_PROP({ prop: 'is_proceed_modal_open', value: false });
        this.UPDATE_SECURITY_PROP({ prop: 'is_password_form_showing', value: false });
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



     async handlePasswordChange() {
       try {
         await this.CHANGE_PASSWORD();
         if (this.security.is_form_submitted) {
           this.$router.push({ name: 'Login' });
           this.RESET_SETTINGS_MODULE();
         }
       } catch(e) {
       }
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

  .no_current_blocked_users_message {
    text-align: center;
    color: gray;
  }

  .privacy_blocked_user_list_container,
  .security_settings_change_password_consent {
     margin: 0 auto;
    padding: 0.5rem;
    border-radius: 6px;
    box-sizing: border-box;
    background-color: #1e1e1e;
  }

  .privacy_blocked_user_list_container {
    width: 90%;
  }

  .security_settings_change_password_consent {
    box-sizing: border-box;
    width: 400px;
    p {
      color: $primaryWhite;
      background-color: #323232;
      border-radius: 0.25rem;
      padding: 0.5rem;
      text-align: center;
    }
  }

  .security_settings_change_password_consent_heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    h3 {
      color: darken($primaryWhite,10);
    }
  }

  .security_settings_change_password_btns {
    display: flex;
    justify-content: space-evenly;
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

  @media(max-width:600px) {
    .security_settings_change_password_consent {
      width: 100%;
    }
  }
</style>