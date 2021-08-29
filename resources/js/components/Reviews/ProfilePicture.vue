<template>
  <div class="review_user_picture">
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
  .review_user_picture {
    box-sizing: border-box;

     img {
       width: 46px;
       height: 46px;
       border-radius: 50%;
     }

     svg {
       width: 46px;
       height: 46px;
       border-radius: 50%;
       background-color: $themeLightBlue;
       color: $themePink;
     }
  }
</style>