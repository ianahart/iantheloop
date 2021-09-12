<template>
  <div :class="`story_pro_pic ${activeStories}`">
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
      },
      computed: {
        ...mapState('stories',
        [
          'currentUserHasStories',
        ]
      ),

        page() {
          return this.$route.name;
        },

        activeStories() {
          if (this.page === 'CreateStory') {
            return this.currentUserHasStories ? 'story_pro_pic_active' : 'story_pro_pic_not_active';
          } else if (this.page === 'StoriesDashboard') {
            return 'story_pro_pic_active';
          }
        }
      }
    }

</script>

<style lang="scss">
  .story_pro_pic {
    box-sizing: border-box;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
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

  .story_pro_pic_active {
    border: 4px solid $themeRoyalBlue;
    img {
      border: 3px solid #3a3a3a;
    }
    svg {
      border: 3px solid #3a3a3a;
    }
  }

  .story_pro_pic_not_active {
    border: 4px solid transparent;
    img {
      border: 3px solid transparent;
    }
    svg {
      border: 3px solid transparent;
    }

  }
</style>