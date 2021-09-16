<template>
  <div v-if="baseStories.length" :class="`stories_container ${storiesContainer}`">
      <svg @click.stop="prevStories('prev')" v-if="$route.name === 'NewsFeed' && pagination.current_page !== 1" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 newsfeed_stories_chevron_left" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      <svg @click.stop="nextStories('next')" v-if="$route.name === 'NewsFeed' && pagination.current_page !== pagination.last_page" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 newsfeed_stories_chevron_right" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    <transition-group class="story_transition_container" name="slide-stories" tag="div" mode="out-in" appear>

    <Story
      v-for="baseStory in baseStoryData" :key="baseStory.id"
      :name="baseStory.full_name"
      :profilePicture="baseStory.subject_story.profile.profile_picture"
      :profileId="baseStory.subject_story.profile.id"
      :storyId="baseStory.subject_story.id"
      :userId="baseStory.id"
    />

        </transition-group>

    <button class="load_more_stories_btn" v-if="!isLastPage && $route.name === 'StoriesDashboard'" @click="loadMoreStories">More stories...</button>
  </div>

</template>

<script>
 import { mapState, mapGetters, mapMutations, mapActions } from 'vuex'

 import Story from './Story.vue';

 export default {
   name: 'Stories',
   components: {
     Story,
   },

   data () {
     return {
       debounceID: '',
       fetchedPages: [],
     }
   },

   async created() {
     this.CLEAR_BASE_STORIES();
     this.RESET_STORIES_MODULE();
     if (!this.baseStories.length) {
       this.SET_STORIES_LOCATION(this.$route.name);
       await this.RETRIEVE_BASE_STORIES_DATA();
       if (this.$route.name === 'NewsFeed' && this.baseStories.length) {
         this.NAVIGATE_CAROUSEL('next');
       }
     }
   },

   beforeDestroy() {
      clearTimeout(this.debounceID);
      this.CLEAR_BASE_STORIES();
      this.fetchedPages = [];
   },

   computed: {
     ...mapState('stories',
      [
        'storiesLocation',
        'baseStories',
        'pagination',
        'carousel',
        'carouselNavigation',
      ]
    ),

    storiesContainer() {
      return this.$route.name === 'NewsFeed' ? 'newsfeed_page_stories_container' : 'dashboard_page_stories_container';
    },

    baseStoryData() {
       return this.$route.name === 'NewsFeed' ? this.carousel : this.baseStories;
    },

    isLastPage() {
      let lastPage = false;
          if (this.pagination.current_page === this.pagination.last_page) {
            lastPage = true;
          }
        return lastPage;
    },
   },

   methods: {
     ...mapMutations('stories',
      [
        'CLEAR_BASE_STORIES',
        'SET_PAGINATION_PAGE',
        'NAVIGATE_CAROUSEL',
        'SET_STORIES_LOCATION',
        'RESET_STORIES_MODULE',
      ]
    ),
     ...mapActions('stories',
      [
        'RETRIEVE_BASE_STORIES_DATA',
      ]
    ),

    async nextStories(navigation) {
     this.SET_PAGINATION_PAGE(navigation);
        this.debounce(async () => {
            await this.RETRIEVE_BASE_STORIES_DATA();
            this.NAVIGATE_CAROUSEL(navigation);
          }, 350);
    },

    prevStories(navigation) {
      this.SET_PAGINATION_PAGE(navigation);
      this.NAVIGATE_CAROUSEL(navigation);
    },

    async loadMoreStories() {
      this.debounce(async () => {
        await this.RETRIEVE_BASE_STORIES_DATA();
      }, 350);
    },

    debounce(fn, delay = 400) {
    return ((...args) => {
      clearTimeout(this.debounceID)
      this.debounceID = setTimeout(() => {
        this.debounceID = null
        fn(...args)
      }, delay)
    })()
    },
   },
 }


</script>

<style lang="scss">

  .slide-stories-enter-active {
    transition: all 0.4s ease-in;
  }
  .slide-stories-leave-active {
    transition: all 0.1s ease-out;
  }

  .slide-stories-enter, .slide-stories-leave-to  {
    opacity: 0;
    transform: translateX(50px);
  }

 .stories_container {
   box-sizing: border-box;
   display: flex;
 }

 .dashboard_page_stories_container {
   flex-direction: column;
   align-items: flex-start;
 }

 .story_transition_container {
   width: 100%;
 }

 .newsfeed_page_stories_container {

      justify-content: space-evenly;
      align-items: center;
      position: relative;
      transition: all 0.2s ease-in;
      div:first-of-type {
        display: flex;
      }
 }

.load_more_stories_btn {
  border: none;
  padding: 0.6rem 0.5rem;
  width: 120px;
  border-radius: 10px;
  background-color: $themeLightBlue;
  text-shadow: 1px 1px 0px rgb(0 0 0 / 50%);
  font-family: 'Open Sans', sans-serif;
  font-size: 0.95rem;
  color: $primaryWhite;
  transition: all 0.3s ease-in-out;
  cursor: pointer;
  box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
  margin-left: 1rem;
  margin-bottom: 2rem;
}


.newsfeed_stories_chevron_left,
.newsfeed_stories_chevron_right {
   width: 55px;
   height: 55px;
   position: absolute;
   cursor: pointer;
   color: $primaryBlack;
   transition: all 0.3s ease-in;
   &:hover {
     color: lighten($primaryBlack, 7);
   }
}

   .newsfeed_stories_chevron_left {
     top:65px;
     left: -50px;
   }

   .newsfeed_stories_chevron_right {
     top: 65px;
     right: -50px;
   }

@media(max-width:700px) {
  .newsfeed_page_stories_container {
    div:first-of-type {
    flex-wrap: wrap;
    }
  }

  .newsfeed_stories_chevron_left {
    left: -10px;
  }

  .newsfeed_stories_chevron_right {
    right: -10px;
  }
}
</style>