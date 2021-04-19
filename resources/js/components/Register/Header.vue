<template>
  <div class="create__account__header">
    <h1>Sign Up</h1>
    <div class="create__account__user">
      <p>Existing user?</p>
      <router-link :to="{name: 'Login'}">Sign in</router-link>
    </div>
  </div>
</template>

<script>

  export default {

    name: 'Header',

    components: {

    },

    props: {

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

    },
  }

</script>

<style lang="scss">

/*
IMPORTS
*/

 @import '../../../sass/general/_variables.scss';
  @import '../../../sass/general/_base.scss';

.create__account__header {
  margin-top: 6rem;
  h1 {
    color: $themeBlue;
    font-weight: 500;
    margin-bottom: 0;
    font-size: 3rem;
  }
}

.create__account__user {

  display: flex;
  align-items: center;
  width: 200px;
  margin-right: auto;
  text-align: left;

  p {
    color: lighten(darkslategrey, 10);
    margin: 0;
    margin-right: 0.5rem;
  }

  a {
    font-weight: bold;
    color: $themeBlue;
    text-decoration: none;

  }
}

@media(max-width: 600px) {

  .create__account__header {
    display: flex;
    align-items: center;
    flex-direction: column;
    margin-top: 1.5rem;
  }

  .create__account__user {
    margin: 0;
    justify-content: space-between;
  }
}

</style>