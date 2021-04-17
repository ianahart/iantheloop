<template>

  <div class="create__account">
    <h1 class="test">Create Account</h1>
  </div>
</template>

<script>

  import { mapGetters, mapState } from 'vuex';

  export default {

    name: 'CreateAccount',

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

      ...mapState('createAccount', ['test']),
    },

    methods: {

    },
  };

</script>

<style lang="scss">

/*
IMPORTS
*/
  @import '../sass/general/_variables.scss';
  @import '../sass/general/_base.scss';
  @import '../sass/forms/_inputs.scss';

  .create__account {
    background-image: url('/assets/looped.png');
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
  }

</style>