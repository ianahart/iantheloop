<template>
  <div class="profile_header__container">
    <header class="profile__header">
      <div class="profile__header_image__section">
        <BackgroundImage />
        <ProfileImage />
      </div>
    </header>
  </div>
</template>


<script>

  import { mapGetters } from 'vuex';

  import BackgroundImage from './BackgroundImage.vue';
  import ProfileImage from './ProfileImage.vue';

  export default {
    name: 'Header',

    props: {

    },

    components: {

      BackgroundImage,
      ProfileImage,
    },

    mounted () {

    },

    computed: {

      ...mapGetters('profile',
        [
          'getBaseProfile'
        ]
      ),
    },
  }
</script>

<style lang="scss">

  // .profile_header__container {
  //   margin-bottom: 3rem;
  // }

  .profile__header {
    box-sizing: border-box;
    border-radius: 8px;
    max-width: 940px;
    margin: 0 auto;
    height: 440px;
  }

  .profile__header_image__section {
    position: relative;
    height: 100%;
    width: 100%;
  }

</style>