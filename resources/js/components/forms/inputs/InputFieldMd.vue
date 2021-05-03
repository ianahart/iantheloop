<template>
  <div class="forms__input__field_md">
    <label>
      {{ label }}:
    </label>
    <input
      @change="updateFieldValue"
      :type="type"
      :value="value"
      :name="nameAttr"

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
  </div>
</template>

<script>

  export default {

    name: 'InputFieldMd',

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

    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

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
  };

</script>

<style lang="scss">

/*
IMPORTS
*/
  @import '../../../../sass/forms/_inputs.scss';


</style>
