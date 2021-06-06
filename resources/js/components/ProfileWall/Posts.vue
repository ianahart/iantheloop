<template>
  <div v-if="postsLoaded" class="profile_posts__container">
    <h2>Posts</h2>
    <button v-if="morePosts" @click="loadMore">LOAD MORE</button>
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'Posts',

    props: {
      subjectUserId: String,
    },

    async created () {

       await this.loadPosts(this.subjectUserId);
    },

    components: {

    },

    computed: {

      ...mapState('profileWall',
        [
          'postsLoaded',
          'morePosts',
        ]
      ),
    },

    methods: {

      ...mapActions('profileWall',
        [
          'LOAD_POSTS',
        ]
      ),

      async loadMore () {
        await this.LOAD_POSTS(this.subjectUserId);
      },

      async loadPosts (subjectUserId) {

        await this.LOAD_POSTS(subjectUserId);
      }
    },
  }
</script>

<style lang="scss">

</style>