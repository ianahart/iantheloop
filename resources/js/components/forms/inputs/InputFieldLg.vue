<template>
  <div class="forms__input__field_lg">
    <label>
      {{ label }}:
    </label>
    <div class="password__icon__outer__container">
      <PasswordIcon v-if="nameAttr === 'visiblepassword'">
        <input
          autocomplete="new-password"
          :name="nameAttr"
          @change="updateFieldValue"
          :type="isPasswordShowing ? 'text' : 'password'"
          :value="value"
        />
      <div v-if="errors.length">
        <p
          class="forms__input__error"
          v-for="(error, index) in errors"
          :key="index"
        >
          {{ error }}
        </p>
    </div>
      </PasswordIcon>
    </div>
    <input
      @change="updateFieldValue"
      :type="type"
      :value="value"
      :name="nameAttr"
    />
    <p
      v-if="nameAttr === 'visiblepassword'"
      class="password__instructions"
      >
        Password must include one uppercase letter, one lowercase letter, one number, and one special character.
    </p>
    <div v-if="errors.length">
      <p
        class="forms__input__error"
        v-for="(error, index) in errors"
        :key="index"
      >
        {{ error }}
      </p>
    </div>
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
      errors: Array,
      label: String,
      value: String,
      nameAttr: String,
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
            value: e.target.value,
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
