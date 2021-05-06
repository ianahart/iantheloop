<template>
  <div class="not_found__container">
    <div class="not_found__content">
      <div>
        <h1>Oops!</h1>
        <h3>We can't seem to find the page you're looking for.</h3>
        <p>Error code: 404</p>
        <div class="not_found__links">
          <router-link :to="{name: 'Home'}">Home</router-link>
          <p @click="goBack">Back</p>
        </div>

      </div>
      <img :src="require('../../assets/not_found.jpg').default" alt="two people that are sad" />
    </div>
  </div>
</template>


<script>

  export default {

    name: 'NotFound',

    props: {

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

      goBack () {

        this.$router.back();
      },
    },
  };

</script>
<style lang="scss">

  .not_found__container {
    background-color: $primaryGray;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
  }

  .not_found__content {
    background-color: #fff;
    box-sizing: border-box;
    height: 90%;
    width: 90%;
    padding: 1rem;
    display: flex;
    justify-content: center;
    align-items: center;

  h1, h3, p {
      color: lighten($primaryBlack, 5);

  }

    h1 {
      font-size: 5rem;

    }

    h3 {
      width: 75%;
    }

  }

  .not_found__links {
    display: flex;
    align-items: center;

    a, p {
      display: block;
      margin: 0.5rem;
      color: $error;
    }

    p {
      text-decoration: underline;
      cursor: pointer;
    }
  }

  @media (max-width: 600px) {
    .not_found__content {

      flex-direction: column;
      img {
        width: 100%;
        height: 100%;
        overflow: hidden;
      }

     h3, h1, p {
       text-align: center;
     }

     h3 {
       margin: 0 auto;
     }
    }

    .not_found__links {
      justify-content: center;
    }
  }

</style>