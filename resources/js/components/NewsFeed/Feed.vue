<template>
  <div v-if="postsLoaded" class="feed_main_container">
    <h3>Feed Container</h3>
    <Posts
      :subjectUserId="getUserId.toString()"
      :currentUserProfilePic="getProfilePic"
      @loadsubsequent="emitRefill"
    />
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import Posts from '../Post/Posts.vue';

  export default {
    name: 'Feed',
    props: {

    },
    components: {
      Posts,
    },
    data () {
      return {

      }
    },

    computed: {
      ...mapGetters('user',
        [
          'getUserId',
          'getProfilePic'
        ]
      ),
      ...mapState('posts', ['postsLoaded']),
    },
    methods: {
    emitRefill() {
      this.$emit('refillfeed');
    },
    },
  }


</script>

<style lang="scss">
  .feed_main_container {
    box-sizing: border-box;
    border: 1px solid blue;
    flex-grow: 2;
    max-width: 900px;
  }

  @media (max-width:600px) {
    .feed_main_container {
      flex-grow: 1;
      width: 100%;
      max-width: 100%;
    }
  }
</style>