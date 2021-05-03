<template>
  <div class="add_interest_container">
    <form @submit.prevent="handleAddInterest(value)" class="add_interest__form">
      <input @change="handleChange" :type="type" :value="value" :name="nameAttr" />
      <button>Add an interest</button>
    </form>
  </div>
</template>


<script>

  import { mapState, mapMutations } from 'vuex';

  export default {

    name: 'AddInterests',

    props: {
      field: String,
      type: String,
      errors: Array,
      label: String,
      value: String,
      nameAttr: String,
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


      ...mapMutations('aboutDetails',
        [
          'ADD_INTEREST',
          'INTEREST_INPUT_CHANGE',
        ]
      ),

      handleAddInterest(interest) {

        this.ADD_INTEREST(interest);
      },

      handleChange(e) {

        this.INTEREST_INPUT_CHANGE(e.target.value);
      }
    }
  }

</script>


<style lang="scss">

    @import '../../../../sass/general/_variables.scss';

    .add_interest_container {



      button {
        color: $primaryBlack;
        border: none;
        margin-left: 0.3rem;
        background-color: darken($primaryGray, 5);
        cursor: pointer;
        transition: all 0.5s ease-in-out;
        height: 30px;
        border-radius: 8px;
        box-shadow: 0px 3px 4px rgba(0,0,0,0.2);


        &:hover {
          background-color: darken($primaryGray, 20);
        }
      }

      input {
        border: none;
        border: 1px solid $mainInputBorder;
        height: 30px;
        border-radius: 8px;


      }
    }


</style>