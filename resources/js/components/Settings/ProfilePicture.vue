<template>
  <div class="settings_profile_picture_container">
     <img v-if="src" :src="src" :alt="alt" />
     <DefaultProfileIcon v-else />
  </div>
</template>

<script>

  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';

  export default {
     name: 'ProfilePicture',
     props: {
       src: String,
       alt: String,
     },
     components: {
       DefaultProfileIcon,
     }
  }
</script>
<style lang="scss">
  .settings_profile_picture_container {
    box-sizing: border-box;
    img, svg {
      border-radius: 50%;
      width: 36px;
      height: 36px;
    }
    svg {
      color: $themePink;
      background-color: $themeLightBlue;
    }
  }
</style>