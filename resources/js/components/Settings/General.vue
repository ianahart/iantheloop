<template>
  <div class="settings_general_container">
    <h2 class="settings_option_title">Manage General</h2>
    <div class="settings_option_title_decoration"></div>
    <div class="general_settings_options">
      <div class="general_settings_option">
        <p class="general_settings_option_danger">Danger Zone</p>
        <div class="general_settings_option_heading">
          <h4>Delete Account</h4>
          <p>All of your most senstive data will be removed for your privacy. Only a subset of information will reside as it is needed to make sure this application runs smoothly. <em>e.g. messages between you and a contact of yours</em>. </p>

        </div>
        <form class="general_settings_delete_form" @submit.prevent="handleDeleteAccount">
          <p v-if="general.type_test.toLowerCase() !== general.answer.toLowerCase()">Please type this into the box below:</p>
          <p v-if="general.type_test.toLowerCase() !== general.answer.toLowerCase()">{{ general.answer }}</p>
          <div class="general_settings_delete_form_controls">
            <input v-model="general.type_test" @keyup="handleKeyDown" type="text"/>
            <p v-if="general.type_test.toLowerCase() === general.answer.toLowerCase()">If you choose to delete your account there is no undoing this action.</p>
            <button :disabled="general.type_test.toLowerCase() !== general.answer.toLowerCase()" type="submit">Delete</button>
            <button @click.stop="handleCancel">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters,  mapMutations, mapActions } from 'vuex';
  export default {
    name: 'General',

    created() {
      this.SET_TYPE_TEST_ANSWER(`permanently delete ${this.userName.toLowerCase().trim()}`);
    },

    beforeDestroy() {
      this.SET_TYPE_TEST('');
      this.SET_TYPE_TEST_ANSWER('');
      this.SET_TYPE_TEST_SUCCESS(false);
    },

    computed: {
      ...mapState('settings', ['general']),
      ...mapGetters('user', ['userName']),
    }
    ,
    methods:{
      ...mapMutations('settings', ['SET_TYPE_TEST_ANSWER', 'SET_TYPE_TEST', 'SET_TYPE_TEST_SUCCESS']),
      ...mapMutations('user', ['REMOVE_TOKEN']),
      ...mapActions('settings', ['DELETE_USER_ACCOUNT']),

      handleKeyDown(e) {
        this.SET_TYPE_TEST(e.target.value);
      },

      async handleDeleteAccount(e) {
        try {
          await this.DELETE_USER_ACCOUNT();
          if (this.general.success) {
            this.REMOVE_TOKEN();
            this.$router.push({ name: 'Login' });
          }
        } catch(e) {
        }
      },

      handleCancel() {
         this.SET_TYPE_TEST('');
      },
    },
  }

</script>

<style lang="scss">
  .settings_general_container {
    box-sizing: border-box;
  }

  .general_settings_options {
    box-sizing: border-box;
  }

  .general_settings_option{
    display: flex;
    flex-direction: column;
  }
  .general_settings_option_danger {
    color: #EED202;
    margin-bottom: 0;
    border-radius: 20px;
    padding: 0.4rem;
    border: 1px solid #EED202;
    width: 100px;
  }

  .general_settings_delete_form {
     p {
       text-align: center;
       font-size: 0.85rem;
       color: #817f80;
       &:last-of-type {
         color: $error;
         font-weight: bold;
       }
     }
  }

  .general_settings_delete_form_controls {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;

    button {
        margin-top: 1.3rem;
        max-width: 120px;
        cursor: pointer;
        border-radius: 10px;
        width: 30%;
        padding: 0.3rem;
        &:first-of-type {
          background-color: transparent;
          border: 1px solid lighten($error,9);
          color: $error;
          margin-right: 1rem;
        }
        &:last-of-type {
          background-color: transparent;
          border: 1px solid gray;
          color: $primaryWhite;
        }
        &:hover {
          opacity: 0.5;
          background-color: #282727;
        }
      }

     input {
       width: 95%;
       border-radius: 12px;
       border: 1px solid #817f80;
       height: 35px;
       background-color: transparent;
       color: darken($primaryWhite, 5);
       outline: lighten($themePink, 10);
     }
  }

  .general_settings_option_heading {
    box-sizing: border-box;
    display: flex;
    flex-direction: flex-start;
    margin: 0.75rem 0;
    h4 {
      color: #fcfcfc;
      padding-right: 1rem;
    }
    p {
      color: #817f80;
      font-size: 0.9rem;
      line-height: 1.6;
    }
  }

</style>