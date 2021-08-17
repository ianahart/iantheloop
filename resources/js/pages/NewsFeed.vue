<template>
  <div class="newsfeed_parent_container">
    <Modal />
    <div v-if="posts.length" class="newsfeed_flex_container">
      <Feed
        v-if="postsLoaded && posts.length"
        @refillfeed="refillNewsfeedPosts"
      />
      <Sidebar />
    </div>
    <div v-else class="empty_newsfeed_container">
      <p>Start following people to get your newsfeed up and running!</p>
      <FriendsIcon />
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';

  import Feed from '../components/NewsFeed/Feed.vue';
  import FriendsIcon from '../components/Icons/FriendsIcon.vue';
  import Sidebar from '../components/NewsFeed/Sidebar.vue';
  import Modal from '../components/ProfileWall/Modal.vue';

  export default {

    name: 'NewsFeed',

    components: {
      Feed,
      Sidebar,
      FriendsIcon,
      Modal,
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
  }

  .empty_newsfeed_container {
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-top: 3rem;
    p {
      color: gray;
      text-align: center;
    }

    svg {
      height: 100px;
      width: 100px;
      color: gray;

    }
  }

  @media (max-width:600px) {
    .newsfeed_flex_container {
      flex-direction: column-reverse;
    }
  }
</style>


