<template>
  <div class="forgot__password__container">
    <div class="forgot__password__form__container">
      <form @submit.prevent="sendEmail" class="forgot__password__form">
        <h1>Forgot Password</h1>
        <p class="forgot__password__message__sent">{{ messageSent }}</p>
        <p class="forgot__password__helper__text">Follow instructions provided in the email</p>
        <InputFieldLg
          v-for="(inputField, index) in forgotForm"
          :key="index"
          :field="inputField.field"
          :type="inputField.type"
          :errors="inputField.errors"
          :label="inputField.label"
          :value="inputField.value"
          :nameAttr="inputField.namaeAttr"
          form="forgotForm"
          :commitPath="'passwordRecovery/UPDATE_FIELD'"
        />
        <div class="forgot__password__btn__container">
          <button class="button__md">Send Reset Email</button>
        </div>
      </form>
    </div>
  </div>
</template>


<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  import InputFieldLg from '../components/forms/inputs/InputFieldLg';


  export default {

    name: 'ForgotPassword',

    props: {

    },

    components: {

      InputFieldLg,
    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },


    beforeDestroy () {

      this.RESET_MODULE();
    },

    computed: {
      ...mapState('passwordRecovery',
        [
          'forgotForm',
          'hasErrors',
          'messageSent',
        ]
      ),
    },



    methods: {

      ...mapMutations('passwordRecovery',
        [
          'UPDATE_FIELD',
          'CLEAR_ERROR_MSGS',
          'RESET_MODULE'
        ]
       ),

      ...mapActions('passwordRecovery',
        [
          'SEND_EMAIL',
        ]
      ),

      checkErrors () {

          this.forgotForm.forEach(({ field, value, label }) => {

            if (value.trim().length <= 0) {

              const error = label + ' is required';

              this.UPDATE_FIELD({field, value, error, form: 'forgotForm'})
            }
          })
      },

      async sendEmail () {

        this.CLEAR_ERROR_MSGS();

        this.checkErrors();

        if (!this.hasErrors) {

          await this.SEND_EMAIL();

        }
      },
    }
  }


</script>

<style lang="scss">

  @import '../../sass/general/_variables.scss';
  @import '../../sass/general/_buttons.scss';
  @import url('https://fonts.googleapis.com/css2?family=Secular+One&display=swap');


  .forgot__password__container {
    box-sizing: border-box;
    background-image: url('../../assets/forgot_password_bg.svg');
    width: 100%;
    height: 100%;
    background-size: cover;
    background-repeat: no-repeat;
    display: flex;
    justify-content: center;

  }

  .forgot__password__form__container {
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

  // change height from auto later on

  .forgot__password__form {
    width: 100%;
    height: auto;
    box-sizing: border-box;
    padding: 1rem;
    background-color: $primaryWhite;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    border-radius: 8px;
  }

  .forgot__password__helper__text {
     text-align: center;
     font-size: 0.8rem;
     color: $mainInputLabel;
  }

  .forgot__password__message__sent {
    color: darken($themeDarkGreen, 15);
    text-align: center;
  }

  .forgot__password__btn__container {
    display: flex;
    justify-content: center;
    margin: 3rem auto;

    button {
      width: 150px;
    }
  }

</style>