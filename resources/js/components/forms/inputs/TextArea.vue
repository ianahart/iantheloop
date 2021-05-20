<template>
  <div class="text-area__container">
    <label>{{ label }}:</label>
    <p class="text_area_max_length"><span>*</span>Please keep it under {{ maxLength }} characters</p>
    <textarea
      class="textarea"
      :type="type"
      :name="nameAttr"
      :value="value"
      @change="updateFieldValue"
    >
    </textarea>
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

    name: 'TextArea',

    components: {

    },

    props: {

      field: String,
      maxLength: String,
      type: String,
      errors: Array,
      label: String,
      value: String,
      nameAttr: String,
      commitPath: String,
      form: String,
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
    }
  }
</script>

<style lang="scss">

  .text-area__container {
    display: flex;
    flex-direction: column;
    max-width: 420px;
    min-height: 150px;
    box-sizing: border-box;
    width: 100%;

    label {
      color: lighten($mainInputLabel, 10);
      font-weight: bold;
      font-size: 0.85rem;
    }
  }

  .text_area_max_length {
    font-size: 0.8rem;
    color:  gray;
    font-style: italic;
    margin-bottom: 3px;

    span {
      color: $themeLightBlue;
    }
  }

</style>