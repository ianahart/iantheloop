<template>
  <div v-if="dataLoaded" class="profile_edit__container">
    <PreviousRoute
      routeName="Profile"
    />
    <h3 class="profile_edit__title">Edit Profile</h3>
    <div class="profile_edit__section__container">
     <SectionNav />
     <div class="profile_edit__forms">
      <div v-if="currentWindow === 'General'" class="profile_edit__section">
        <General />
      </div>
      <div v-if="currentWindow === 'Identity'" class="profile_edit__section">
        <Identity />
      </div>
        <div v-if="currentWindow === 'About'" class="profile_edit__section">
          <About />
      </div>
      <div v-if="currentWindow === 'Work'" class="profile_edit__section">
          <Work />
      </div>
      <div v-if="currentWindow === 'Pictures'" class="profile_edit__section">
        <Pictures />
      </div>
     </div>
    <div class="profile_edit__section__container_bg"></div>
    </div>
  </div>
</template>


<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import PreviousRoute from '../components/Navigation/PreviousRoute.vue';
  import SectionNav from '../components/EditProfile/SectionNav.vue';
  import General from '../components/EditProfile/General.vue';
  import Identity from '../components/EditProfile/Identity.vue';
  import About from '../components/EditProfile/About.vue';
  import Work from '../components/EditProfile/Work.vue';
  import Pictures from '../components/EditProfile/Pictures.vue';

  export default {

    name: 'ProfileEdit',

    components: {
      PreviousRoute,
      SectionNav,
      General,
      Identity,
      About,
      Work,
      Pictures,
    },

    async created () {

      await this.fetchEditData (this.$route.params.profileId);

      if (this.fetchError) {

        this.RESET_MODULE();

        this.$router.push({name: 'Home'});
      }
    },

    computed: {

      ...mapState('profileEdit',
        [
          'dataLoaded',
          'fetchError',
          'currentWindow',
          'allWindow'
        ]
      ),
    },

    methods: {

      ...mapMutations('profileEdit',
        [
          'RESET_MODULE'
        ]
      ),

      ...mapActions('profileEdit',
        [
          'FETCH_EDIT_DATA'
        ]
      ),

      clearBeforeRedirect() {

        this.RESET_MODULE();
      },

      async fetchEditData(profileId) {

        await this.FETCH_EDIT_DATA(profileId);
      }

    }
  }
</script>

<style lang="scss">

.profile_edit__container {
  width: 100%;
  box-sizing: border-box;
  background-color: lighten($primaryGray, 2);
  padding: 0.5rem;
  height: 100%;
}

.profile_edit__title {
  font-family: 'Open Sans', sans-serif;
  color: $primaryBlack;
  margin-left: 1rem;
  margin-top: 1rem;
}

.profile_edit__section__container {
  box-sizing: border-box;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
  min-height: 600px;
  max-width: 940px;
  margin: 3rem auto;
}

.profile_edit__section__container_bg {
  background-size: cover;
  background-repeat: no-repeat;
  background-position: bottom;
  background-image: url('../../assets/edit_profile_form.svg');
  min-height: 300px;
  height: auto;
  margin: 0 auto;
  margin-top: 1.5rem;
}

.profile_edit__section {
  // border: 1px solid blue;
  margin: 2rem auto 3rem auto;
  height: 100%;
}

.profile_edit__forms {
  box-sizing: border-box;
  padding: 1rem;
  width: 100%;
  min-height: 600px;
  height: 100%;
}
</style>
