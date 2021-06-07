<template>
  <div v-if="postsLoaded" class="profile_posts__container">
    <Post
      v-for="post in posts"
      :key="post.id"
      :post="post"
    />
    <button v-if="morePosts" @click="loadMore">LOAD MORE</button>
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  import Post from './Post.vue';

  export default {

    name: 'Posts',

    props: {
      subjectUserId: String,
    },

    async created () {

       await this.loadPosts(this.subjectUserId);
    },

    components: {
      Post,
    },

    computed: {

      ...mapState('profileWall',
        [
          'postsLoaded',
          'morePosts',
          'posts',
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

.profile_posts__container {
  box-sizing: border-box;
  padding:0.5rem;
  width: 100%;
  margin-top: 2rem;
}

</style>