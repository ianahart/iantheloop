<template>
  <div class="logo">
      <img :src="require('../../../assets/looped_logo.png').default" alt="lines connected by dots" />
  </div>
</template>

<script>

  export default {

    name: 'Logo',

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

    methods: {

    }
  };

</script>

<style lang="scss">

  .logo {
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;

    img {
      height: 100px;
      width: 100px;
      display: block;
    }
  }

</style>