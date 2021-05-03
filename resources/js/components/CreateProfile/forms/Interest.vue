<template>
  <div class="about_details_interest__container">
    <span @click="removeInterest(id)">X</span>
    <p>{{ name }}</p>
  </div>
</template>


<script>

  import { mapState, mapMutations } from 'vuex';


  export default {

    name: 'Interest',

    props: {
      id: Number,
      name: String,
    },


    methods: {

      ...mapMutations('aboutDetails',
        [
          'REMOVE_INTEREST'
        ]
      ),

      removeInterest(id) {

        this.REMOVE_INTEREST(id);
      }
    }
  }




</script>

<style>

</style>


<style lang="scss">

  @import '../../../../sass/general/_variables.scss';

  .about_details_interest__container {
    background-color: $primaryGray;
    margin: 1rem;
    display: flex;
    justify-content: center;
    padding: 0.4rem 0.5rem;
    border-radius: 8px;
    box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.1);
    position: relative;
    min-height: 30px;
    height: auto;
    width: 115px;
    overflow-wrap: break-word;
    text-align: center;

    p {
      margin: 0;
      font-size: 0.8rem;
      color: $mainInputLabel;
      width: 100%;
      word-break: break-all;
      display: flex;
      align-items: center;
      justify-content: center;


    }

    span {
      cursor: pointer;
      font-size: 0.75rem;
      font-weight: bold;
      color: lighten($primaryBlack, 5);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0.1rem;
      width: 15px;
      height: 15px;
      border-radius: 50%;
      border: 2px solid lighten($primaryBlack, 5);
      position: absolute;
      top: -2px;
      left: -1px;
      transition: all 0.3s ease-out;

      &:hover {
        background-color: darken($primaryGray, 5);
      }
    }
  }


</style>