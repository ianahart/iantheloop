<template>
  <div class="forms__input__field_lg">
    <label>
      {{ label }}:
    </label>
    <div class="password__icon__outer__container">
      <PasswordIcon v-if="field === 'createPassword'">
        <input
          @change="updateFieldValue"
          :type="isPasswordShowing ? 'text' : 'password'"
          :value="value"
        />
        <p class="forms__input__error" v-if="error">{{ error }}</p>
      </PasswordIcon>
    </div>
    <input
      @change="updateFieldValue"
      :type="type"
      :value="value"
    />
    <p
      v-if="field === 'createPassword'"
      class="password__instructions"
      >
        Password must include one uppercase letter one number, and one special character.
    </p>
    <p
      class="forms__input__error"
      v-if="error"
      >
      {{ error }}
    </p>
  </div>
</template>

<script>

  import { mapState } from 'vuex';

  import PasswordIcon from '../../Icons/PasswordIcon.vue';

  export default {

    name: 'InputFieldLg',

    props: {
      field: String,
      type: String,
      error: String,
      label: String,
      value: String,
      commitPath: String,
    },

    components: {

      PasswordIcon,
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
          'isPasswordShowing'
        ]
      ),
    },

    methods: {
      updateFieldValue(e) {

        this
        .$store
        .commit(this.commitPath,
          {
            field: this.field,
            newValue: e.target.value,
            error: '',
          }
        );
      }
    },
  };

</script>

<style lang="scss">

/*
IMPORTS
*/
  @import '../../../../sass/forms/_inputs.scss';

  .password__icon__outer__container{
    position: relative;
  }

  .password__instructions {
    color: lighten(gray, 7);
    font-weight: 100;
    font-style: italic;
    font-size: 0.8rem;
  }

</style>
