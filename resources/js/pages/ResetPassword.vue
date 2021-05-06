<template>
  <div class="reset__password__container">
    <div class="reset__password__form__container">
      <form @submit.prevent="resetPassword" class="reset__password__form">
        <h1>Reset Password</h1>
        <p class="link__expired">{{ globalResetError }}</p>
        <router-link v-if="globalResetError.length" class="expired__retry" :to="{name: 'ForgotPassword'}">Retry</router-link>
        <InputFieldLg
          v-for="(inputField, index) in resetForm"
          :key="index"
          :field="inputField.field"
          :type="inputField.type"
          :errors="inputField.errors"
          :label="inputField.label"
          :value="inputField.value"
          :nameAttr="inputField.nameAttr"
          form="resetForm"
          :commitPath="'passwordRecovery/UPDATE_FIELD'"
        />
        <div class="reset__password__btn__container">
          <button class="button__md">Submit</button>
        </div>
      </form>
    </div>
  </div>
</template>


<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  import InputFieldLg from '../components/forms/inputs/InputFieldLg';


  export default {

    name: 'ResetPassword',


    components: {

      InputFieldLg,
    },

    mounted () {

      this.getResetToken(this.$route.query.token);
    },

    beforeDestroy () {

      this.RESET_MODULE();
    },

    computed: {
      ...mapState('passwordRecovery',
        [
          'resetForm',
          'hasErrors',
          'globalResetError',
          'formSubmitted'
        ]
      ),
    },



    methods: {

      ...mapMutations('passwordRecovery',
        [
          'UPDATE_FIELD',
          'CLEAR_ERROR_MSGS',
          'RESET_MODULE',
          'GET_RESET_TOKEN',
        ]
       ),

      ...mapActions('passwordRecovery',
        [
          'RESET_PASSWORD',
        ]
      ),

      checkErrors () {

          this.resetForm.forEach(({ field, value, label }) => {

            if (value.trim().length <= 0) {

              const error = label + ' is required';

              this.UPDATE_FIELD({field, value, error, form: 'resetForm'})
            }
          })
      },

      getResetToken (query) {

        this.GET_RESET_TOKEN(query);
      },

      async resetPassword () {

        this.CLEAR_ERROR_MSGS();
        this.checkErrors();

        if (!this.hasErrors) {

          await this.RESET_PASSWORD();
          if (this.formSubmitted) {

            this.$router.push('/login');
          }
        }
      },
    }
  }


</script>

<style lang="scss">

  @import url('https://fonts.googleapis.com/css2?family=Secular+One&display=swap');


  .reset__password__container {
    box-sizing: border-box;
    background-image: url('../../assets/reset_password_bg.svg');
    width: 100%;
    height: 100%;
    background-size: cover;
    background-repeat: no-repeat;
    display: flex;
    justify-content: center;

  }

  .reset__password__form__container {
    width: 448px;
    margin-bottom: 3rem;
    box-sizing: border-box;
    margin-top: 10rem;

   h1 {
     font-size: 3rem;
     font-weight: 500;
     margin-bottom: 0;
     font-family: 'Secular One', sans-serif;
     letter-spacing: -3px;
     color: $themeBlue;
     text-align: center;
   }
  }


  .reset__password__form {
    width: 100%;
    height: auto;
    box-sizing: border-box;
    padding: 1rem;
    background-color: $primaryWhite;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    border-radius: 8px;
  }

  .reset__password__helper__text {
     text-align: center;
     font-size: 0.8rem;
     color: $mainInputLabel;
  }

  .reset__password__btn__container {
    display: flex;
    justify-content: center;
    margin: 3rem auto;

    button {
      width: 150px;
    }
  }

  .link__expired {

    color: $error;
    text-align: center;
    font-size: 0.85rem;
  }

  .expired__retry {
    display: block;
    color: $mainInputLabel;
    text-align: center;
    font-size: 0.9rem;
  }

</style>

