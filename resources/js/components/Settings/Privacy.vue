<template>
  <div :style="isBlockListOpen ? { filter: 'blur(5px)' } : ''" class="privacy_container">
    <h2 class="settings_option_title">Manage Privacy</h2>
    <div class="settings_option_title_decoration"></div>
    <p @click="emitBlockListOpen" class="blocked_user_list_modal_trigger">Edit List...</p>
    <div class="privacy_settings_option">
      <div class="privacy_settings_content">
        <h4>Block Messages</h4>
        <p>When you block a particular user from sending messages to you, you will not be able to send messages to them. They will not see you in their conversator and you will not see them in your conversator.</p>
      </div>
      <Input
        :type="inputs.blocked_messages.type"
        :error="inputs.blocked_messages.error"
        :value="inputs.blocked_messages.value"
        :active="inputs.blocked_messages.active"
      />
      <div class="privacy_user_list_wrapper">
        <UserList
        v-if="searchResults.length"
        :users="searchResults"
        :pagination="searchPagination"
        :active="inputs.blocked_messages.active"
        :type="inputs.blocked_messages.type"
      />
     </div>
    </div>
    <div class="privacy_settings_option">
      <div class="privacy_settings_content">
        <h4>Block Profile</h4>
        <p>When you block access for a particular user they will no longer be able to see your profile.</p>
      </div>
     <Input
       :type="inputs.blocked_profile.type"
       :error="inputs.blocked_profile.error"
       :value="inputs.blocked_profile.value"
       :active="inputs.blocked_profile.active"
     />
     <div class="privacy_user_list_wrapper">
      <UserList
        :users="searchResults"
        :pagination="searchPagination"
        :active="inputs.blocked_profile.active"
        :type="inputs.blocked_profile.type"
      />
     </div>
    </div>
    <div class="privacy_settings_option">
      <div class="privacy_settings_content">
        <h4>Block Stories</h4>
        <p>When you block access for a particular user they will no longer be able to see your stories in their feed and you will no longer see their stories in your feed.</p>
      </div>
     <Input
       :type="inputs.blocked_stories.type"
       :error="inputs.blocked_stories.error"
       :value="inputs.blocked_stories.value"
       :active="inputs.blocked_stories.active"
     />
     <div class="privacy_user_list_wrapper">
      <UserList
        :users="searchResults"
        :pagination="searchPagination"
        :active="inputs.blocked_stories.active"
        :type="inputs.blocked_stories.type"
      />
     </div>
    </div>
  </div>
</template>

<script>

  import { mapGetters, mapMutations, mapState } from 'vuex';

  import Input from './Input.vue';
  import UserList from './UserList.vue';

  export default {
    name: 'Privacy',
    components: {
      Input,
      UserList,
    },

    computed: {
      ...mapState('settings',[
        'isBlockListOpen',
        'inputs',
        'searchResults',
        'searchPagination'
      ]),
      ...mapGetters('settings', ['getActiveInput']),
    },

    beforeDestroy() {
      this.SET_IS_BLOCKLIST_OPEN(false);
    },
    methods: {
      ...mapMutations('settings', ['SET_IS_BLOCKLIST_OPEN']),

      emitBlockListOpen() {
        this.$emit('blocklistopen', true);
      }
    },
  }

</script>

<style lang="scss">
  .privacy_container {
    box-sizing: border-box;
    margin: 0.3rem;
  }

  .blocked_user_list_modal_trigger {
    text-decoration: underline;
    cursor: pointer;
    font-size: 0.9rem;
    text-align: right;
    color: #817f80;
  }

  .privacy_settings_option {
    box-sizing: border-box;
    padding: 0.5rem;
    display: flex;
    flex-direction: column;

  }

  .privacy_user_list_wrapper {
     margin-top: 1rem;
    box-sizing: border-box;
    display: flex;
    width: 100%;
    justify-content: center;
    align-items: center;
}

  .privacy_settings_content {
     box-sizing: border-box;
     justify-content: flex-start;
     display: flex;
    h4 {
      color: $primaryWhite;
      padding-right: 1rem;
    }
    p {
      color: #817f80;
      font-size: 0.9rem;
      line-height: 1.6;
      margin-top: 1.3rem;
    }
  }

  @media(max-width:600px) {
    .privacy_settings_option {
      flex-direction: column;
      align-items: center;
    }
    .privacy_settings_content {
      flex-direction: column;
      h4 {
        text-align: center;
      }
    }
  }
</style>