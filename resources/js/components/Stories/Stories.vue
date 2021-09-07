<template>
 <div class="stories_container">
   <h3>Stories Component</h3>
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

   mounted() {

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
   border: 1px solid blue;
   display: flex;
 }


//  .stories_location_storiesdashboard {
//    flex-direction: column;
//  }

//  .stories_location_newsfeed {
//    flex-direction: row;
//  }
</style>