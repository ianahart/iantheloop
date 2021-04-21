
<template>
  <div class="login">
    <h1>Login Page</h1>
  </div>
</template>

<script>

  import { mapMutations } from 'vuex';

  export default {

    name: 'Login',

    props: {

    },

    components: {

    },

    data () {

      return {

      }
    },

    created () {

      if(this.$route.query.signup) {

          this.clearRegistration();
      }
    },

    mounted () {
      console.log(this.$route);
    },

    methods: {

      ...mapMutations('createAccount',
        [
          'RESET_MODULE',
        ]
      ),

      clearRegistration() {

        if (this.$route.query.signup) {

            this.RESET_MODULE();
        }
      },
    },
  }

</script>