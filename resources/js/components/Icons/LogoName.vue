<template>
  <div class="company__logo">
      <img :src="require(`../../../assets/looped_logo_name_${theme}.png`).default" alt="a poligonal vector" />
  </div>
</template>

<script>



  export default {

    name: 'LogoName',

    props: {
      theme: String,
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

    }
  };

</script>

<style lang="scss">

/*
  IMPORTS
*/
  @import '../../../sass/general/_variables.scss';
  @import '../../../sass/general/_base.scss';

  .company__logo {
    display :flex;
    flex-direction: column;
    justify-content: center;

    img {
      width: 75px;
      height: 60px;
    }

  }

</style>