<template>
  <div ref="profileWallContainer" class="profile_wall__container">
   <div v-if="parseInt(this.baseProfile.user_id) === this.currentUserId || currUserFollowing" class="profile_wall__inner_container">
     <FormTrigger
      :baseProfile="baseProfile"
      :currentUserId="currentUserId"
      :viewUserFirstName="viewUserFirstName"
      :currentUserProfilePic="currentUserProfilePic"
     />
     <WallSettings
      v-if="currentUserId === parseInt(this.baseProfile.user_id)"
      :currentUserId="currentUserId"
      :subjectUserId="baseProfile.user_id"
     />
     <Posts
      :subjectUserId="baseProfile.user_id"
      :currentUserProfilePic="currentUserProfilePic"
      @loadsubsequent="loadMore"

     />
   </div>
   <div class="restrict_profile_wall" v-else>
     <p>You have to follow {{ viewUserFirstName }} to see their feed.</p>
   </div>
  </div>
</template>


<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import FormTrigger from '../Post/FormTrigger.vue';
  import Posts from '../Post/Posts.vue';
  import WallSettings from './WallSettings.vue';

  export default {

    name: 'ProfileWall',

    components: {

      FormTrigger,
      Posts,
      WallSettings,
    },

    props: {
      baseProfile: Object,
      currUserFollowing: Boolean,
      currentUserId: Number,
      currentUserFirstName: String,
      viewUserFirstName: String,
      currentUserProfilePic: String,
    },

    async mounted() {
      await this.loadPosts();
    },

    computed: {
      ...mapState('posts',
        ['postsLoaded']),
    },

    methods: {

      ...mapActions('posts',
        [
          'LOAD_POSTS'
        ]
      ),

       async loadMore () {
        try {
          await this.LOAD_POSTS(this.baseProfile.user_id);
        } catch(e) {

        }
      },

      async loadPosts () {
        try {
          await this.LOAD_POSTS(this.baseProfile.user_id);
        } catch (e) {

        }
      }
    }
  }

</script>


<style lang="scss">

.profile_wall__container {
  box-sizing: border-box;
  margin: 2rem auto 3rem auto;
  display: flex;
  justify-content: center;
  align-items: center;
}

.profile_wall__inner_container  {
  // border: 1px solid lightsalmon;
  width: 70%;
  margin: 0 auto;
  box-sizing: border-box;
  height: 100%;
  padding: 1rem;
}

.restrict_profile_wall {
  color: gray;
}

@media (max-width: 600px) {

  .profile_wall__inner_container {
      width: 100%;
  }
}


</style>