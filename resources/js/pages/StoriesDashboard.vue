<template>
  <div class="stories_dashboard_container">
  <div class="stories_dashboard_grid">
    <Sidebar>
      <template v-slot:Sidebar>
        <SidebarHeader
         :page="$route.name"
        />
        <h3 class="stories_list_title">Stories</h3>
        <Stories
         v-if="baseStories.length"
         :baseStories="baseStories"
         />
        <button class="load_more_stories_btn" v-if="!isLastPage" @click="loadMoreStories">Fetch more</button>
      </template>
    </Sidebar>
    <div class="dashboard_lightbox_wrapper">
      <LightBox v-if="isLightBoxActive" />
    </div>
  </div>
  </div>
</template>

<script>

 import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
 import Sidebar from '../components/Stories/Sidebar.vue';
 import SidebarHeader from '../components/Stories/SidebarHeader.vue';
 import Stories from '../components/Stories/Stories.vue';
 import LightBox from '../components/Stories/LightBox.vue';

 export default {
   name: 'StoriesDashboard',
   components: {
     Sidebar,
     SidebarHeader,
     Stories,
     LightBox,
   },

   data() {
     return {
       debounceID: '',
     }
   },

   async created () {
     this.SET_USER_ID_CLICKED(this.getUserId);
     await this.getActiveStoryCount(this.getUserId);
     await this.RETRIEVE_BASE_STORIES_DATA();

   },

   beforeDestroy() {
     this.RESET_STORIES_MODULE();
     clearTimeout(this.debounceID);
   },

   computed: {
     ...mapState('stories',
      [
        'stories',
        'baseStories',
        'pagination',
        'currentUserStories',
        'isLightBoxActive',
      ]
    ),
    ...mapGetters('user',
      [
        'getUserId'
      ]
    ),
    isLastPage() {
      let lastPage = false;
        if (this.pagination !== null) {
          if (this.pagination.current_page === this.pagination.last_page) {
            lastPage = true;
          }
        }
        return lastPage;
    },
   },

   methods: {
     ...mapMutations('stories',
      [
        'RESET_STORIES_MODULE',
        'SET_USER_ID_CLICKED'
      ]
    ),
    ...mapActions('stories',
      [
        'ACTIVE_STORY_COUNT',
        'RETRIEVE_BASE_STORIES_DATA',
      ]
    ),

    async getActiveStoryCount(userId) {
      await this.ACTIVE_STORY_COUNT(userId);
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
  .stories_dashboard_container {
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    background-color: lighten($primaryBlack, 1);
  }
  .stories_dashboard_grid {
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    height: 100%;
  }

  .dashboard_lightbox_wrapper {
    box-sizing: border-box;
    flex-grow: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #000;
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

  .stories_list_title {
    color: #fcfcfc;
    font-family: "Secular One", sans-serif;
    padding-left: 0.5rem;
    font-size: 1.5rem;
  }

  @media(max-width: 800px) {
    .stories_dashboard_grid {
      flex-direction: column;
    }
  }

</style>