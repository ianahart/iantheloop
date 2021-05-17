<template>
  <div class="profile_header__container">
    <header class="profile__header">
      <div class="profile__header_image__section">
        <BackgroundImage
          v-if="getBaseProfile.background_picture"
        />
        <div class="no_background_image" v-else></div>
        <ProfileImage
          v-if="getBaseProfile.profile_picture"
          className="profile_image_lg "
          :src="getBaseProfile.profile_picture"
        />
        <div class="no_profile_image" v-else>
          <DefaultProfileIcon
            className="icon__lg__neutral"
          />
        </div>
      </div>
    </header>
  </div>
</template>


<script>

  import { mapGetters } from 'vuex';

  import BackgroundImage from './BackgroundImage.vue';
  import ProfileImage from './ProfileImage.vue';
  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';

  export default {
    name: 'Header',

    props: {

    },

    components: {

      BackgroundImage,
      ProfileImage,
      DefaultProfileIcon,
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

  .no_background_image {
    background-color: $primaryGray;
    border-radius: 8px;
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
  }

  .no_profile_image {
      border-radius: 50%;
      position: absolute;
      height: 200px;
      width: 200px;
      bottom: -120px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background-color: $primaryGray;
      border: 1px solid darken($primaryGray, 5);
  }

</style>