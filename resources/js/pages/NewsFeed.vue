<template>
  <div class="newsfeed_parent_container">
    <Modal />
    <div class="user_stories_component_container">
      <CreateStoryTrigger />
      <Stories />
    </div>
    <div v-if="posts.length" class="newsfeed_flex_container">
      <Feed
        v-if="postsLoaded && posts.length"
        @refillfeed="refillNewsfeedPosts"
      />
      <Sidebar />
    </div>
    <div v-else class="empty_newsfeed_container">
      <Loader v-if="!postsLoaded && !posts.length" />
      <div class="empty_newsfeed_message" v-else-if="postsLoaded && !posts.length">
        <FriendsIcon />
        <p>Start following people to get your newsfeed up and running!</p>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';

  import Feed from '../components/NewsFeed/Feed.vue';
  import FriendsIcon from '../components/Icons/FriendsIcon.vue';
  import Sidebar from '../components/NewsFeed/Sidebar.vue';
  import Modal from '../components/ProfileWall/Modal.vue';
  import Stories from '../components/Stories/Stories.vue';
  import CreateStoryTrigger from '../components/Stories/CreateStoryTrigger.vue';
  import Loader from '../components/Misc/Loader.vue';
  export default {

    name: 'NewsFeed',

    components: {
      Feed,
      Sidebar,
      FriendsIcon,
      Modal,
      Stories,
      CreateStoryTrigger,
      Loader,
    },

    created() {
      this.SET_STORIES_LOCATION(this.$route.name);
      this.SET_IS_LOADER_SHOWING(true);
    },

    async mounted() {
      await this.loadNewsfeedPosts();
      if (this.checkExistingStoryConnection() === -1) {
            this.initStoryChannel();
      }
    },

    beforeDestroy() {
      this.RESET_MODULE();
    },

    computed: {
      ...mapState('posts',
        [
          'posts',
          'morePosts',
          'postsLoaded',

        ]
      ),
      ...mapState('newsFeed', ['isLoaderShowing']),
      ...mapState('stories',
        [
          'stories',
        ]
      ),
      ...mapGetters('user',
        [
          'getUserId',
          'getProfilePic',
          'getToken',
        ]
      ),

    },

    methods: {

      ...mapMutations('posts',
        [
          'RESET_MODULE'
        ]
      ),
      ...mapMutations('stories',
        [
          'SET_STORIES',
          'SET_CURRENT_USER_STORIES',
          'RESET_STORIES_MODULE',
          'SET_STORIES_LOCATION',
        ]
      ),
      ...mapMutations('newsFeed', ['SET_IS_LOADER_SHOWING']),
      ...mapActions('posts',
        [
          'NEWSFEED_POSTS'
        ]
      ),

      checkExistingStoryConnection() {
        const channelNames = Object.keys(window.Echo.connector.channels)
        const storyConnection = channelNames.findIndex(channelName => channelName === `private-stories.${this.getUserId}`);
        return storyConnection;
      },

      async loadNewsfeedPosts () {
        await this.NEWSFEED_POSTS();
        this.SET_IS_LOADER_SHOWING(false);
      },

      async refillNewsfeedPosts() {
        await this.NEWSFEED_POSTS();
      },

      initStoryChannel() {
        Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
         Echo.private(`stories.${this.getUserId}`)
            .listen('StoryPhotoProcessed', (event) => {
                if (parseInt(event[0].user_id) === parseInt(this.getUserId)) {
                      this.SET_CURRENT_USER_STORIES([...event]);
                } else {
                    this.SET_STORIES([...event]);
                }
            });
      }
    },
  }

</script>

<style lang="scss">
  .newsfeed_parent_container {
    box-sizing: border-box;
  }

  .user_stories_component_container {
    background-color: #3b3b441f;
    box-sizing: border-box;
    padding: 1rem 3rem;
    display: flex;
    justify-content: flex-start;
    border-radius: 0.25rem;
    margin: 1.5rem 0.85rem;
  }

  .newsfeed_flex_container {
    box-sizing: border-box;
    padding:0.5rem;
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

  .empty_newsfeed_message {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  @media (max-width:600px) {
    .newsfeed_flex_container {
      flex-direction: column-reverse;
    }

    .user_stories_component_container {
      margin: 1.5rem 0.5rem;
      padding: 0.5rem;
      flex-direction: column;
      align-items: center;
    }
  }
</style>


