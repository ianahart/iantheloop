<template>
  <div class="story_pro_pic">
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
      name: 'StoryProPic',
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
  .story_pro_pic {
    box-sizing: border-box;

     img {
       width: 64px;
       height: 64px;
       border-radius: 50%;
     }

     svg {
       width: 64px;
       height: 64px;
       border-radius: 50%;
       background-color: $themeLightBlue;
       color: $themePink;
     }
  }
</style>