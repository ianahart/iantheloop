<template>
  <div class="stories_dashboard_container">
  <div class="stories_dashboard_grid">
    <Sidebar>
      <template v-slot:Sidebar>
        <SidebarHeader
         :page="$route.name"
        />
        <h3 class="stories_list_title">Stories</h3>
        <Stories />
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
     this.SET_STORIES_LOCATION(this.$route.name);
     const idClicked = this.userIdClicked !== null ? this.userIdClicked : this.getUserId;

     this.SET_USER_ID_CLICKED(idClicked);
     await this.getActiveStoryCount(this.getUserId);
   },

   beforeDestroy() {
     clearTimeout(this.debounceID);
   },

   computed: {
     ...mapState('stories',
      [
        'stories',
        'currentUserStories',
        'isLightBoxActive',
        'userIdClicked'
      ]
    ),
    ...mapGetters('user',
      [
        'getUserId'
      ]
    ),
   },

   methods: {
     ...mapMutations('stories',
      [
        'RESET_STORIES_MODULE',
        'SET_USER_ID_CLICKED',
        'SET_STORIES_LOCATION',
      ]
    ),
    ...mapActions('stories',
      [
        'ACTIVE_STORY_COUNT',
      ]
    ),

    async getActiveStoryCount(userId) {
      await this.ACTIVE_STORY_COUNT(userId);
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