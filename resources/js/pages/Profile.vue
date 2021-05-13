<template>
  <!-- <div v-if="dataLoaded" class="profile__container">
    <Header />
  </div> -->
    <div class="profile__container">
      <div class="profile_component__wrapper">
        <Header />
        <ProfileStats/>
        <Dashboard />
      </div>
    </div>
</template>

<script>


        // backgroundpicture done
        // profilepic done
          // display name
         // follower stats
        // follow btn,
        // full name
        // company
        // position

        //

  import { mapGetters, mapMutations, mapState,  mapActions } from 'vuex';

  import Header from '../components/Profile/Header.vue';
  import ProfileStats from '../components/Profile/ProfileStats.vue';
  import Dashboard from '../components/Profile/Dashboard.vue';

  export default {

    name: 'Profile',

    components: {
      Header,
      ProfileStats,
      Dashboard,
    },

    created () {

    },

    async mounted () {

      await this.fetchBaseProfileData(this.$route.params.id);

    },

    beforeDestroy () {
      this.clearBaseProfileState();
    },

    computed: {

      ...mapState('profile',
          [
            'dataLoaded'
          ]
        ),
    },

    methods: {

      ...mapMutations('profile',
          [
          'RESET_MODULE'
          ]
        ),

      ...mapActions('profile',
          [
            'FETCH_BASE_PROFILE_DATA'
          ]
        ),

      clearBaseProfileState() {

        this.RESET_MODULE();
      },

      async fetchBaseProfileData (profileId) {

        await this.FETCH_BASE_PROFILE_DATA(profileId);
      }
    },
  }
</script>

<style>

.profile_component__wrapper {
  max-width: 960px;
  margin: 0 auto;
  height: 100%;
}

.profile__container {
  box-sizing: border-box;
  width: 100%;
  height: 100%;
  min-height: 100vh;
}

@media(max-width: 600px) {
  .profile__container {
    padding: 0.5rem;
  }
}

</style>