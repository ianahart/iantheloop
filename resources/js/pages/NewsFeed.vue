<template>
  <div>
    <h1>News feed</h1>
    <p>friend's posts go here</p>
    <p v-for="(name, index) in names" :key="index">{{ name }}</p>

  </div>
</template>

<script>

  import { mapState, mapActions, mapMutations } from 'vuex';

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

      async setNames () {

        await this.GET_NAMES();
      },




    },
  }

</script>

<style lang="scss">


</style>


