<template>
  <div  class="interest_form__container">
    <form  v-if="numOfInterests !== 5" @submit.prevent="addInterest(value)">
      <div class="forms__input__field_md interest_form_input_group">
        <label :for="label">{{ label }}:</label>
        <input
          @change="handleInputChange"
          :id="label"
          :type="type"
          :value="value"
          :name="nameAttr"
          placeholder="Add an interest..."
        />
      </div>
      <button>Add</button>
    </form>
    <div v-else>
        <p class="max__interests">You've reached the maximum number of interests(5)</p>
      </div>
    <div class="interest_form__errors">
      <p
        v-for="(error, index) in errors"
        :key="index"
        class="forms__input__error"
      >
      {{ error }}
     </p>
    </div>
  </div>
</template>

<script>

  export default {

    name: 'InterestForm',

    props: {
      field: String,
      type: String,
      errors: Array,
      label: String,
      value: String,
      nameAttr: String,
      numOfInterests: Number,
    },

    components: {

    },

    data () {

      return {

      }
    },

    computed: {

    },

    methods: {

      sendEvent(event, data) {

        this.$emit(event, data);
      },

      addInterest(value) {

        if (value.trim() !== '') {

          this.sendEvent('add', value)
        }

      },

      handleInputChange (e) {

        this.sendEvent('inputchange', e.target.value);
      },
    },
  }
</script>

<style lang="scss">
.interest_form__container {

  form {
    max-width: 310px;

    button {
      width: 140px;
      border: none;
      border-radius: 11px;
      box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.1);
      transition: all 0.25s ease-out;
      cursor: pointer;
      background-color: $themeBlue;
      height: 30px;
      padding: 0.2rem 0.3rem;
      color: $primaryWhite;

      &:hover {
        background-color: lighten($themeBlue, 5);
      }
    }
  }
}


.interest_form__errors {
  margin: 1.2rem  3rem 1.2rem 0;
  max-width: 300px;
  box-sizing: border-box;
  word-break: break-all;

  p {
    margin: 0.2rem 0;
    text-align: right;
  }
}

.interest_form_input_group {
  min-width: 200px;

    input {
    width: 100%;
  }
}

.max__interests {
  color: $warning;
  font-weight: bold;
  font-size: 0.85rem;
}

@media(max-width:600px) {
  .interest_form__errors {

    margin-right: auto;

  p {

    text-align: left;
  }
}


}

</style>