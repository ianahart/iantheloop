<template>
  <div class="form__select_sm__container">
    <select class="form__select_sm" @change="updateFieldValue" :value="value">
      <option disabled value="">{{ defaultOption }}</option>
      <option
        v-for="option in options" :key="option.id"
        :value="option.name"
        >
        {{ option.abbrv }}
      </option>
    </select>
    <p
      class="forms__input__error"
      v-for="(error, index) in errors"
      :key="index"
    >
    {{ error }}
    </p>
  </div>

</template>

<script>

  export default {

    name: 'SelectSm',

    props: {
      defaultOption: String,
      field: String,
      type: String,
      errors: Array,
      label: String,
      value: String,
      nameAttr: String,
      commitPath: String,
      options: Array,
    },

    components: {

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
            form: this.form ? this.form : null,
          }
        );
      }
    },
  }

</script>

<style lang="scss">

  @import '../../../../sass/general/_variables.scss';

  .form__select_sm__container {
    position: relative;
    p {
      margin-top: 0.1rem;
      position: absolute;
    }
  }

  .form__select_sm {
    color: $mainInputLabel;
    font-weight: bold;
  background:
    linear-gradient(45deg, transparent 50%,$themeLightBlue 50%),
    linear-gradient(135deg, $themeLightBlue 50%, transparent 50%),
    linear-gradient(to right, #fff, #fff);
  background-position:
    calc(100% - 21px) calc(1em + 2px),
    calc(100% - 16px) calc(1em + 2px),
    100% 0;
  background-size:
    5px 5px,
    5px 5px,
    2.5em 2.5em;
  background-repeat: no-repeat;


  border: 1px solid $mainInputBorder;
  height: 35px;
  border-radius: 8px;
  padding: 0.5em 3.5em 0.5em 1em;

  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  -webkit-appearance:none;
  -moz-appearance:none;


  }

</style>