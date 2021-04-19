<template>
  <div class="forms__input__field_lg">
    <label>
      {{ label }}:
    </label>
    <input @change="updateFieldValue" :type="type" :value="value" />
    <p class="forms__input__error" v-if="error">{{ error }}</p>
  </div>
</template>

<script>

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



</style>
