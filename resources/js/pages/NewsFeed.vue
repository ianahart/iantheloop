<template>
  <div class="newsfeed_parent_container">
    <div class="newsfeed_flex_container">
      <Feed
        v-if="postsLoaded && posts.length"
        @refillfeed="refillNewsfeedPosts"
      />
      <Sidebar />
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';

  import Feed from '../components/NewsFeed/Feed.vue';
  import Sidebar from '../components/NewsFeed/Sidebar.vue';

  export default {

    name: 'NewsFeed',

    components: {
      Feed,
      Sidebar,
    },

    async mounted() {
      await this.loadNewsfeedPosts();
    },

    beforeDestroy() {
      this.RESET_MODULE();
    },

    computed: {
      ...mapState('posts',
        [
          'posts',
          'morePosts',
          'postsLoaded'

        ]
      ),
      ...mapGetters('user',
        [
          'getUserId',
          'getProfilePic'
        ]
      ),
    },

    methods: {

      ...mapMutations('posts',
        [
          'RESET_MODULE'
        ]
      ),
      ...mapActions('posts',
        [
          'NEWSFEED_POSTS'
        ]
      ),
      async loadNewsfeedPosts () {

        await this.NEWSFEED_POSTS();
      },
      async refillNewsfeedPosts() {
        await this.NEWSFEED_POSTS();
    },
    },
  }

</script>

<style lang="scss">
  .newsfeed_parent_container {
    box-sizing: border-box;
  }

  .newsfeed_flex_container {
    box-sizing: border-box;
    padding:0.5rem;
    border: 1px solid green;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  @media (max-width:600px) {
    .newsfeed_flex_container {
      flex-direction: column;
    }
  }
</style>


