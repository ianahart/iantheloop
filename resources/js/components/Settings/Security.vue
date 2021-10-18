<template>
  <div class="settings_security_container">
    <h2 class="settings_option_title">Manage Security &#38; Login</h2>
    <div class="settings_option_title_decoration"></div>
    <div class="settings_security_option">
      <div class="settings_security_option_row">
        <h4>Remember Me</h4>
        <p>Toggling this switch on will allow you to bypass logging in with your email and password. This feature will expire in a roughly a month for security reasons or until you turn this feature off.</p>
      </div>
      <div class="settings_security_option_column">
        <ToggleBtn
         @toggle="handleToggle"
         :isToggled="security.remember_me"
         :data="{prop: Object.keys(security)[0], value: security.remember_me}"
       />
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import ToggleBtn from '../forms/buttons/ToggleBtn.vue';
  export default {
    name: 'Security',
    components: {
      ToggleBtn,
    },

    async created() {
      await this.RETRIEVE_SECURITY_SETTINGS();
      this.handleToggle = debounce(this.handleToggle, 250);
    },

    computed: {
      ...mapState('settings', ['security']),
    },

    methods: {
      ...mapMutations('settings',['SET_REMEMBER_ME']),
      ...mapActions('settings', ['UPDATE_REMEMBER_ME', 'RETRIEVE_SECURITY_SETTINGS']),

      async handleToggle({ data }) {
        try {
          this.SET_REMEMBER_ME(!data.value);
          await this.UPDATE_REMEMBER_ME({ prop: 'remember_me' });
        } catch(e) {
        }
      }
    }
  }
</script>

<style lang="scss">
  .settings_security_container {
    box-sizing: border-box;
    margin: 0.3rem;
  }

  .settings_security_option {
    flex-direction: column;
    h4 {
      color: #fcfcfc;
      padding-right: 1rem;
    }
    p {
      color: #817f80;
      font-size: 0.9rem;
      line-height: 1.6;
      margin-top: 1.3rem;
    }
  }
  .settings_security_option
  .settings_security_option_row,
  .settings_security_option_column {
    box-sizing: border-box;
    display: flex;
  }
  .settings_security_option,
  .settings_security_opiton_column {
    flex-direction: column;
  }
  .settings_security_row {
    justify-content: flex-start;
  }
</style>

