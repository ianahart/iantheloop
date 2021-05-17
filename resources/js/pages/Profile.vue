<template>

    <div  v-if="dataLoaded" class="profile__container">
      <div class="profile_component__wrapper">
        <Header />
        <ProfileStats/>
        <Dashboard />
      </div>
    </div>
</template>

<script>

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

    async mounted () {

      await this.fetchBaseProfileData(this.$route.params.id);

    },

    beforeDestroy () {
      this.clearBaseProfileState();
    },

    data () {

      return {
        paramChange: false,
      }
    },

    watch: {

      '$route.params.id': function () {

        this.RESET_MODULE();

        this.fetchBaseProfileData(this.$route.params.id);
      },

      fetchError () {

        if (this.fetchError) {

          this.RESET_MODULE();
          this.$router.push({name: 'NotFound'});
        }
      },

    },



    computed: {

        linkParam () {

          return this.$route.params.id;
        },

      ...mapState('profile',
          [
            'dataLoaded',
            'fetchError',
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