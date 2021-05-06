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

  };

</script>

<style lang="scss">

/*
  IMPORTS
*/

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