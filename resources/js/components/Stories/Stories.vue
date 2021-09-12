<template>
 <div class="stories_container">
   <Story
     v-for="baseStory in baseStories" :key="baseStory.id"
     :name="baseStory.full_name"
     :profilePicture="baseStory.subject_story.profile.profile_picture"
     :profileId="baseStory.subject_story.profile.id"
     :storyId="baseStory.subject_story.id"
     :userId="baseStory.id"
   />
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
   props: {
    baseStories: Array,
   },

   beforeDestroy() {

   },

   computed: {
     ...mapState('stories',
      [
        'storiesLocation'
      ]
    ),
   },

   methods: {

   },
 }


</script>

<style lang="scss">
 .stories_container {
   box-sizing: border-box;
   display: flex;
   flex-direction: column;
   align-items: flex-start;
 }


//  .stories_location_storiesdashboard {
//    flex-direction: column;
//  }

//  .stories_location_newsfeed {
//    flex-direction: row;
//  }
</style>