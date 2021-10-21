<template>
  <div :style="security.is_proceed_modal_open ? { filter: 'blur(5px)' } : ''" class="settings_security_container">
    <h2 class="settings_option_title">Manage Security &#38; Login</h2>
    <div class="settings_option_title_decoration"></div>
    <div class="settings_security_option">
      <div class="settings_security_option_row">
        <h4>Remember Me</h4>
        <p class="settings_security_option_text">Toggling this switch on will allow you to bypass logging in with your email and password. This feature will expire in a roughly a month for security reasons or until you turn this feature off.</p>
      </div>
      <div class="settings_security_option_column">
        <ToggleBtn
         @toggle="handleToggle"
         :isToggled="security.remember_me"
         :data="{prop: Object.keys(security)[0], value: security.remember_me}"
       />
      </div>
      <div class="settings_security_option_row">
        <div class="settings_security_password_header">
          <h4>Change Password</h4>
          <p v-if="security.is_password_updated && security.password_updated_on !== null">&check; Password last updated at {{ security.password_updated_on }}</p>
        </div>
        <div v-if="!security.is_password_form_showing" class="settings_security_option_column">
           <p class="settings_security_option_text">Change your password to be more secure. Your new password cannot be the same as your old password.</p>
           <button @click="openForm" class="settings_security_password_trigger">Change Password...</button>
        </div>
        <div v-if="security.is_password_form_showing" class="settings_security_option_column">
          <div class="settings_security_close_icon" @click="closeForm">
            <CloseIcon />
          </div>
          <form class="settings_security_password_form">
            <InputFieldLg
                v-for="(inputField, index) in security.password_form" :key="index"
                :field="inputField.field"
                :type="inputField.type"
                :errors="inputField.errors"
                :label="inputField.label"
                :value="inputField.value"
                form="security"
                :nameAttr="inputField.nameAttr"
                commitPath=""
                @inputchange="handleInputChange"
              />
            <button class="settings_security_btn" @click.prevent="openProceedModal">Proceed</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import ToggleBtn from '../forms/buttons/ToggleBtn.vue';
  import CloseIcon from '../Icons/CloseIcon.vue';
  import InputFieldLg from '../forms/inputs/InputFieldLg.vue';

  export default {
    name: 'Security',
    components: {
      ToggleBtn,
      CloseIcon,
      InputFieldLg,
    },

    async created() {

      await this.RETRIEVE_SECURITY_SETTINGS();
      this.handleToggle = debounce(this.handleToggle, 250);
    },

    beforeDestroy() {
      this.CLEAR_SECURITY();
    },

    computed: {
      ...mapState('settings', ['security']),
      ...mapGetters('settings', ['getPasswords']),
    },

    methods: {
      ...mapMutations('settings',[
          'SET_REMEMBER_ME',
          'UPDATE_SECURITY_PROP',
          'UPDATE_SECURITY_PASSWORD_FORM',
          'CLEAR_SECURITY',
          'CLEAR_PASSWORD_FORM_ERRORS',
          ]),
      ...mapActions('settings', ['UPDATE_REMEMBER_ME', 'RETRIEVE_SECURITY_SETTINGS']),

      async handleToggle({ data }) {
        try {
          this.SET_REMEMBER_ME(!data.value);
          await this.UPDATE_REMEMBER_ME({ prop: 'remember_me' });
        } catch(e) {
        }
      },

      openForm() {
        this.UPDATE_SECURITY_PROP({ prop: 'is_password_form_showing', value: true });
      },

      closeForm() {
        this.UPDATE_SECURITY_PROP({ prop: 'is_password_form_showing', value: false });
      },

      handleInputChange(input) {
        this.UPDATE_SECURITY_PASSWORD_FORM({ field: input.field, value: input.value, error: []});
        this.validateInput(input);
        this.UPDATE_SECURITY_PASSWORD_FORM(input);
      },

     checkForEmptyFields() {
       return {
         empty_fields: this.security.password_form.some(field => field.value.trim().length === 0),
         existing_errors: this.security.password_form.some(field => field.errors.length > 0),
       }
     },

     applyErrorForEmptyFields(input = null) {
       if (input !== null) {
          this.UPDATE_SECURITY_PASSWORD_FORM({ field: input.field, value: input.value, error: `${input.field.split('_').join(' ')} may not be left empty `});
          return;
       }
       this.security.password_form.forEach(input => this.UPDATE_SECURITY_PASSWORD_FORM({ field: input.field, value: input.value, error: `${input.field.split('_').join(' ')} may not be left empty `}));
     },

     validateInput(input) {
       const newPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/;
        if (input.value.trim().length === 0) {
           this.applyErrorForEmptyFields(input);
           return;
        }
        if (!newPasswordRegex.test(input.value) && input.field === 'password') {
           this.UPDATE_SECURITY_PASSWORD_FORM({ field: input.field, value: input.value, error: 'Please look above at what the characters the password must include' });
           return;
        }
        if (input.field === 'password_confirmation') {
          if (this.getPasswords.password.length && this.getPasswords.password_confirmation.length && this.getPasswords.password_confirmation.trim() !== this.getPasswords.password) {
            this.UPDATE_SECURITY_PASSWORD_FORM({ field: input.field, value: input.value, error: 'Your new password and confirm password do not match' });
          }
        }
     },

     validateForm() {
       this.security.password_form.forEach(field => this.validateInput(field));
     },

     openProceedModal() {
        this.CLEAR_PASSWORD_FORM_ERRORS();
        this.validateForm();

        const { empty_fields, existing_errors } = this.checkForEmptyFields();
        if (!empty_fields && !existing_errors) {
          this.UPDATE_SECURITY_PROP({ prop: 'is_proceed_modal_open', value: true });
        }
     },
    }
  }
</script>

<style lang="scss">
  .settings_security_container {
    box-sizing: border-box;
    margin: 0.3rem;
  }

  .settings_security_password_trigger {
    border: 1px solid #817f80;
    border-radius: 20px;
    padding: 0.5rem 0.3rem;
    max-width: 300px;
    cursor: pointer;
    background-color: transparent;
    color: #817f80;
    font-family: 'Open Sans',sans-serif;
    text-align: left;
  }

  .settings_security_option {
    flex-direction: column;
    h4 {
      color: #fcfcfc;
      padding-right: 1rem;
    }
  }

  .settings_security_option_text {
      color: #817f80;
      font-size: 0.9rem;
      line-height: 1.6;
      margin-top: 1.3rem;
  }

  .settings_security_option,
  .settings_security_option_row,
  .settings_security_option_column {
    box-sizing: border-box;
    display: flex;
  }


  .settings_security_option,
  .settings_security_option_column {
    flex-direction: column;
  }
  .settings_security_option_row {
    justify-content: flex-start;
    margin: 0.75rem 0;
  }

  .settings_security_password_form {
     input {
       background-color: #313132;
       border: 1px solid #313132;
       color: darken($primaryWhite,3);
     }
  }

  .settings_security_password_header {
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    p {
      color: #34cb34;
      padding-right: 0.1rem;
      font-size: 0.8rem;
      margin-top: 0.1rem;
      margin-bottom: 0;
    }
  }

  .settings_security_close_icon {
    display: flex;
    justify-content: flex-end;
    svg {
      color: $primaryWhite;
      cursor: pointer;
      width: 30px;
      height: 30px;
    }
  }
</style>
