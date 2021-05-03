<template>
  <div class="radio_input__container">
    <div>
      <label>
        <input
          @change="updateFieldValue"
          :type="type"
          :name="nameAttr"
          :value="label.toLowerCase()"
        />
          <span class="checkmark"></span>
        {{ label }}
      </label>
    </div>
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

  export default {

    name: 'RadioBtns',

    props: {
      field: String,
      type: String,
      errors: Array,
      label: String,
      value: String,
      nameAttr: String,
      commitPath: String,
      form: String,
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
      },
    }
  }

</script>


<style lang="scss">


  @import '../../../../sass/forms/_inputs.scss';


</style>