<template>
  <div class="create__account">
    <div class="create__account__container">
      <Header />
      <form class="create__account__form small__form" @submit.prevent="submitForm">
        <div class="create__account__input__container_md">
          <InputFieldMd
            v-for="(inputField, index) in mediumFields" :key="index"
            :field="inputField.field"
            :type="inputField.type"
            :errors="inputField.errors"
            :label="inputField.label"
            :value="inputField.value"
            :commitPath="'createAccount/UPDATE_FIELD'"
          />
        </div>
        <div class="create__account__input__container_lg">
          <InputFieldLg
            v-for="(inputField, index) in largeFields" :key="index"
            :field="inputField.field"
            :type="inputField.type"
            :errors="inputField.errors"
            :label="inputField.label"
            :value="inputField.value"
            :commitPath="'createAccount/UPDATE_FIELD'"
          />
        </div>
        <div class="create__account__form__agreement">
          <CheckBox
            url="https://www.freeprivacypolicy.com/live/644bb30b-ae07-4de5-a9e1-c8230ad37f58"
          />
        </div>
        <div class="create__account__button__container">
          <button class="button__md" type="submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

  import { mapGetters, mapState, mapActions, mapMutations } from 'vuex';

  import Header from '../components/Register/Header.vue';
  import InputFieldMd from '../components/forms/inputs/InputFieldMd.vue';
  import InputFieldLg from '../components/forms/inputs/InputFieldLg.vue';
  import CheckBox from '../components/forms/CheckBox.vue';

  export default {

    name: 'CreateAccount',

    props: {

    },

    components: {

      Header,
      InputFieldMd,
      InputFieldLg,
      CheckBox,
    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },

    computed: {

      ...mapState('createAccount',
        [
          'form',
          'hasErrors',
          'isChecked',
          'isSubmitted'
        ]
      ),

     ...mapGetters('createAccount',
      [
        'mediumFields',
        'largeFields',
      ]
     ),

    },

    methods: {

      ...mapMutations('createAccount',
        [
          'CHECKBOX_ERROR',
        ]
      ),

      ...mapActions('createAccount',
        [
          'SUBMIT_FORM',
        ]
      ),

      validateForm () {

        this.form.forEach(
          (field) => {

            if (!field.value.trim().length) {

              const error = `Please do not leave ${field.label} field empty`;

              this.$store.commit('createAccount/UPDATE_FIELD', {field: field.field, newValue: '', error})
            }
          }
        );
      },



      resetErrors() {

        this.$store.commit('createAccount/RESET_ERRORS');
      },

      async submitForm() {

        this.resetErrors();

        this.validateForm();

        if (!this.isChecked) {

          this.CHECKBOX_ERROR('Please accept the Privacy Policy to continue.');
        }

        if (!this.hasErrors && this.isChecked) {

          await this.SUBMIT_FORM();

          if (this.isSubmitted) {

            this.$router.push({ name: 'Login' , query: {signup: 'success'} });
          }
        }
      },
    },
  };

</script>

<style lang="scss">

/*
IMPORTS
*/

  @import '../../sass/general/_variables.scss';
  @import '../../sass/general/_base.scss';
  @import '../../sass/forms/_inputs.scss';
  @import '../../sass/general/_buttons.scss';



  .create__account {
    box-sizing: border-box;
    background-image: url('../../assets/create_account.svg');
    width: 100%;
    height: 100%;
  }

  .create__account__container {
    width: 448px;
    margin: 0rem auto auto 15rem;

    box-sizing: border-box;
  }

  .create__account__form {
    box-sizing: border-box;
    height: 800px;
    margin: 0 auto;
    margin-bottom: 3rem;
    display: flex;
    flex-direction: column;
    align-items: left;
    padding: 1rem;

    button {
      background-color: $themeBlue;
      color: #fff;
    }


  }

  .create__account__input__container_md {
    display: flex;
    justify-content: space-between;

  }

  .create__account__button__container {
    margin: 2rem auto;
    display:flex;
    justify-content: center;
  }

  .create__account__input__container_lg {
    width: 100%;
  }

  @media(max-width: 600px) {

    .create__account__container {
      box-sizing: border-box;
      width: 95%;
      margin: 0 auto;
    }

    .create__account__input__container_md {
        flex-direction: column;

        width: 100%;

        input {
          width: 100%;
        }

    }

    .create__account__form {
      justify-content: center;
      align-items: center;
    }
  }

</style>