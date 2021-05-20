<template>
<transition name="urllinks" appear>
  <div class="forms__input__field_link">
    <svg @click="removeLink(id)" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
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
</transition>

</template>

<script>

  export default {

    name: 'InputFieldLink',

    props: {
      field: String,
      id: Number,
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
      },

      removeLink(id) {

        this.$emit('removelink', { id });
      }
    },
  };

</script>

<style lang="scss">


  .urllinks-enter-active,
  .urllinks-leave-active {
    transition: all 0.3s ease;

  }
  .urllinks-enter,
  .urllinks-leave-to {
    opacity: 0;
    transform: translateX(130px);
  }

  .forms__input__field_link {

    svg {
      height: 25px;
      width: 25px;
      color: lighten($mainInputLabel, 7);
      cursor: pointer;
    }

    input {
      width: 75%;
      border: none;
      border-bottom: 2px solid $mainInputBorder;
      outline: none;
      margin: 0.5rem 0;

      &:focus {
        border-bottom: 2px solid $themeLightBlue;
      }
    }
  }


  @media(max-width: 600px) {

    .forms__input__field_link {

      input {
        width: 100%;
      }
    }
  }


</style>
