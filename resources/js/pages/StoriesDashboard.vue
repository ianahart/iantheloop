<template>
  <div class="stories_dashboard_container">
    <h2>Stories Dashboard Page</h2>
    <Stories />
  </div>
</template>

<script>

 import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
 import Stories from '../components/Stories/Stories.vue';

 export default {
   name: 'StoriesDashboard',
   components: {
     Stories,
   },

   mounted() {

   },

   beforeDestroy() {
     this.RESET_STORIES_MODULE();
   },

   computed: {

   },

   methods: {
     ...mapMutations('stories',
      [
        'RESET_STORIES_MODULE'
      ]
    ),
   },

 }
</script>

<style lang="scss">
  .stories_dashboard_container {
    box-sizing: border-box;
  }

</style>