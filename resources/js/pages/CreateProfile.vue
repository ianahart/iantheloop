<template>
  <div class="create_profile__container">
    <div class="create_profile__grid">
      <Sidebar />
      <Viewer />
      <Triggers/>
    </div>
  </div>
</template>


<script>

    import { mapGetters, mapActions, mapMutations, mapState } from 'vuex';

    import Sidebar from '../components/CreateProfile/Sidebar.vue';
    import Viewer from '../components/CreateProfile/Viewer.vue';
    import Triggers from '../components/CreateProfile/Triggers.vue';

  export default {


    name: 'CreateProfile',

    components: {

      Sidebar,
      Viewer,
      Triggers,
    },


    beforeDestroy() {

      this.RESET_MODULE();
      this.$store.commit('workDetails/RESET_MODULE');
      this.$store.commit('identity/RESET_MODULE');
      this.$store.commit('generalDetails/RESET_MODULE');
      this.$store.commit('customize/RESET_MODULE');
      this.$store.commit('aboutDetails/RESET_MODULE');

    },

    computed: {
  ...mapGetters('user',
        [
          'getProfileStatus',
        ]
      ),
    },

    created () {

      if (this.getProfileStatus) {

        this.$router.push('/');
      }
    },

    methods: {

      ...mapMutations('createProfile',
            [
              'RESET_MODULE'
            ]
          ),
    }
  }
</script>

<style lang="scss">

  .create_profile__container {
    background-image: url('../../assets/create_profile_bg.svg');
    background-size: center;
    height: 100%;
    box-sizing: border-box;
    width: 100%;

  }

  .create_profile__grid {
    width: 90%;
    height: 100%;
    box-sizing: border-box;
    margin:  auto;
    padding: 2.3rem;
    display: grid;
    grid-template-columns: 1fr 2fr 1fr;
    grid-template-rows: 2fr 1fr 1fr;
    gap: 4rem;
  }

  @media(max-width: 1204px) {

    .create_profile__grid {
      grid-template-columns: 1fr 2fr;
      grid-template-rows: 1fr;

    }
  }

  @media(max-width: 862px) {

    .create_profile__grid {
      grid-template-columns: 1fr;
      grid-template-rows: 1fr;
      justify-items: center;
      padding:0;
      padding-top: 2.3rem;
    }
  }

</style>