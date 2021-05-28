<template>
  <div class="network_header__container">
    <div class="network_header_background__image">
      <div class="network_header_image__wrapper">
        <ProfileImage
          v-if="src"
          className="profile_image_lg"
          :src="src"
        />
      </div>
      <div v-if="!src" class="network_header_default_image__wrapper">
        <DefaultProfileIcon
        className="default_profile_image_rel_lg "
        />
      </div>
    </div>
    <div class="network_header_user_info">
      <h1>{{ ownerFullName }}</h1>
      <p>{{ network }} <span>{{ count }}</span></p>
    </div>
  </div>
</template>

<script>


  import ProfileImage from '../Profile/ProfileImage.vue';
  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';

  export default {

    name: 'Header',

    components: {
      ProfileImage,
      DefaultProfileIcon,
    },

    props: {

      src: String,
      ownerFullName: String,
      count: Number,
      network: String,

    },

    computed: {

    },
  }
</script>

<style lang="scss">

  .network_header__container {
    box-sizing: border-box;
    width: 100%;
  }

  .network_header_background__image {
   position: relative;
    background-image: url('../../../assets/network.svg');
    height: 300px;
  }

  .network_header_image__wrapper{

    position: absolute;
    bottom: 30px;
    left: 10px;
  }

  .network_header_default_image__wrapper {
    height: 200px;
    position: relative;
    width: 200px;
    bottom: -180px;
    left: 20px;
    background-color: $themeLightBlue;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .network_header_user_info {
    margin: 6rem 1rem;

    h1 {
      font-family: "Secular One", sans-serif;
      color: $primaryBlack;
    }

    p {
      color: $primaryBlack;
      display: flex;
      align-items: center;
      font-family: "Secular One", sans-serif;
    }

    span {
      margin-left: 0.5rem;
      font-size: 2rem;
      color: $themePink;
    }
  }

.network_header_default_image__wrapper >  svg {
  color: rgba(0,0,0,0.4);
}



</style>