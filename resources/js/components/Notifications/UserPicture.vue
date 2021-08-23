<template>
  <div class="nav_notification_user_picture">
    <img v-if="src" :src="src" :alt="alt" />
    <DefaultProfileIcon
      v-else
      className="default_profile_image_rel_sm"
    />
  </div>
</template>



<script>

    import { mapState } from 'vuex';
    import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';

    export default {
      name: 'UserPicture',
      components: {
         DefaultProfileIcon,
      },
      props: {
        src: String,
        alt: String,
      }
    }

</script>

<style lang="scss">
  .nav_notification_user_picture {
    box-sizing: border-box;

     img {
       width: 32px;
       height: 32px;
       border-radius: 50%;
     }

     svg {
       width: 32px;
       height: 32px;
       border-radius: 50%;
       background-color: $themeLightBlue;
       color: $themePink;
     }
  }
</style>