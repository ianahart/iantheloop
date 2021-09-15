<template>
    <div @click="handleClick" :class="`story_container ${storyContainer}`">
      <StoryProPic
        :src="profilePicture"
        :alt="name"
      />
      <h3>{{ capitalizedName }}</h3>
    </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import StoryProPic from './StoryProPic.vue';

  export default {
    name: 'Story',
    props: {
     name: String,
     profilePicture: String,
     profileId: Number,
     storyId: Number,
     userId: Number,
    },
    components: {
       StoryProPic,
    },

    computed: {
      ...mapGetters('user',
        [
          'getUserId'
        ]
      ),


      storyContainer() {
        return this.$route.name === 'NewsFeed' ? 'newsfeed_page_story_container' : 'dashboard_page_story_container';
      },

      capitalizedName() {
        return this.name.split(' ')
        .map(word => word.toUpperCase().slice(0, 1) + word.toLowerCase().slice(1))
        .join(' ');
      },
    },

    methods: {
      ...mapMutations('stories',
        [
          'SET_USER_ID_CLICKED',
          'SET_IS_LIGHTBOX_ACTIVE',
        ]
      ),
      ...mapActions('stories',
        [
          'RETRIEVE_STORY'
        ]
      ),

      handleClick() {
        this.$route.name === 'NewsFeed' ? this.goToDashboard(this.userId) : this.retrieveUserStory(this.userId);
      },

      async retrieveUserStory(userId) {
        this.SET_USER_ID_CLICKED(userId);
        await this.RETRIEVE_STORY(userId);
      },

      goToDashboard(userId) {
        this.$router.push({name: 'StoriesDashboard', params: { userId: this.getUserId }});
        this.retrieveUserStory(this.userId)
        this.SET_USER_ID_CLICKED(userId);
        this.SET_IS_LIGHTBOX_ACTIVE(true);

      }
    },
  }

</script>


<style lang="scss">
  .story_container {
    box-sizing: border-box;
    margin: 1.5rem 0;
    display: flex;
    cursor: pointer;
    transition: all 0.2s ease-in;
    width: 100%;

    h3 {
      font-weight: 100;
      font-size: 0.85rem;
    }
  }

  .dashboard_page_story_container {
    flex-direction: row;

    &:hover {
      background-color: rgba(0,0,0,0.4);
    }
    h3 {
      color: #fcfcfc;
      padding-left: 1rem;
      margin-left: 0.5rem;
      margin-top: auto;
    }
}

  .newsfeed_page_story_container {
    margin:0 0.15rem;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    h3 {
      color: $primaryBlack;
      padding-left: 0;
      text-align: center;
    }
  }
</style>