<template>
  <div :class="className">
    <div ref="selectBox" @click="toggleSelectOptions">
       <p>{{ selected }}</p>
       <ChevronIcon />
    </div>
    <div v-if="isDropdownOpen">
      <option
        v-for="option in options"
        :key="option.id"
        @click="getSelection(
          {
          field,
          value:option.name,
          error: '',
          form: null,
          selected: option.abbrv ? option.abbrv : option.name,

          }
        )"
      >
      <span>{{ option.abbrv === selected  ? '&#10003; ' + option.abbrv : option.abbrv }}</span>
    </option>
    </div>
  </div>
</template>

<script>

  import ChevronIcon from '../../../components/Icons/ChevronIcon.vue';

  export default {

    name: 'CustomSelect',

    props: {

      className: String,
      field: String,
      type: String,
      errors: Array,
      label: String,
      value: String,
      nameAttr: String,
      commitPath: String,
      options: Array,
      selected: String,
    },

    components: {

      ChevronIcon,
    },

    data () {

      return {
        isDropdownOpen: false,

      }
    },

    created () {

    },

    mounted () {


      window.addEventListener('click', this.closeDropdown);

    },

    beforeDestroy() {

      window.removeEventListener('click', this.closeDropdown);
    },

    computed: {

    },

    methods: {

      closeDropdown (e) {

        if (e.target !== this.$refs.selectBox) {

          this.isDropdownOpen = false;
        }
      },

      toggleSelectOptions () {

        this.isDropdownOpen = !this.isDropdownOpen;
      },

      getSelection(selection) {

        this.isDropdownOpen = false;

        this.$emit('selected',
          {
            selection,
          }
        );
      }
    },
  }


</script>




<style lang="scss">

  // @import '../../../../sass/general/_variables.scss';

</style>