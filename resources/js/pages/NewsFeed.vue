<template>
  <div>
    <h1>News feed</h1>
    <p>friend's posts go here</p>
    <p v-for="(name, index) in names" :key="index">{{ name }}</p>
    <button @click="logout">logout</button>
  </div>
</template>

<script>

  import { mapState, mapActions } from 'vuex';

  export default {

    name: 'NewsFeed',

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

      this.setNames();
    },

    computed: {
      ...mapState('newsFeed',
        [
        'names',
        ]
      ),
    },

    methods: {

      ...mapActions('newsFeed',
        [
          'GET_NAMES',
        ]
      ),
      ...mapActions('user',
        [
          'LOGOUT'
        ]
      ),

      async setNames () {

        await this.GET_NAMES();
      },



        // move logout later

      async logout() {

        await this.LOGOUT();

        this.$router.push({ name: 'Login' });
      },
    },
  }

</script>

<style lang="scss">


</style>


