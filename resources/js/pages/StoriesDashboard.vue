<template>
  <div class="stories_dashboard_container">
  <div class="stories_dashboard_grid">
    <Sidebar>
      <template v-slot:Sidebar>
        <SidebarHeader
         :page="$route.name"
        />
        <h3>TEST TEST TEST TEST TESSET</h3>
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

   async created () {
     await this.getActiveStoryCount(this.getUserId);
     this.SET_USER_ID_CLICKED(this.getUserId);
   },

   beforeDestroy() {
     this.RESET_STORIES_MODULE();
   },

   computed: {
     ...mapState('stories',
      [
        'stories',
        'currentUserStories',
        'isLightBoxActive',
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
        'SET_USER_ID_CLICKED'
      ]
    ),
    ...mapActions('stories',
      [
        'ACTIVE_STORY_COUNT'
      ]
    ),

    async getActiveStoryCount(userId) {
      await this.ACTIVE_STORY_COUNT(userId);
    }
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

  @media(max-width: 800px) {
    .stories_dashboard_grid {
      flex-direction: column;
    }
  }

</style>