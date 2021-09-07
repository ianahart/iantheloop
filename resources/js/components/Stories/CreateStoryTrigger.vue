<template>
  <div @click="createRoute" class="create_story_trigger">
    <div class="create_story_trigger_profile_pic">
      <div ref="storyBtn" :class="`create_story_btn_container ${this.storyBtnOffset}`">
        <PlusIcon />
        <p>Create Story</p>
      </div>
      <img v-if="getProfilePic" :src="getProfilePic" :alt="userName" />
      <DefaultProfileIcon v-else />
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';
  import PlusIcon from '../Icons/PlusIcon.vue';

  export default {
    name: 'CreateStoryTrigger',
    props: {

    },
    components: {
       DefaultProfileIcon,
       PlusIcon,
    },

    data() {
      return {
        storyBtnOffset: '',
      }
    },

    mounted() {
      this.setStoryBtnOffset();
    },

    beforeDestroy() {

    },

    computed: {
      ...mapGetters('user',
        [
          'getProfilePic',
          'getUserId',
          'userName',
        ]
      ),
    },

    methods: {

       createRoute() {
         this.$router.push({name: 'CreateStory'});
       },

       setStoryBtnOffset() {
          this.storyBtnOffset = this.$refs.storyBtn.nextElementSibling.tagName === 'svg' ? 'svg-story-offset' : 'img-story-offset';
       }
    },
  }

</script>

<style lang="scss">

  .svg-story-offset {
    bottom: -1px;
  }

  .img-story-offset {
    bottom: 8px;
  }


  .create_story_trigger {
    box-sizing: border-box;
    margin-right: 3rem;
    border-radius: 8px;
    cursor: pointer;
  }

  .create_story_trigger_profile_pic {
    box-sizing: border-box;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    img {
      width: 130px;
      height: 200px;
      border-radius: 8px;
    }
    svg {
      border-radius: px;
      width: 130px;
      height: 200px;
      overflow: hidden;
      transform: scale(2.3);
      background-color: $themeLightBlue;
      color: $themePink;
    }
  }

  .create_story_btn_container {
    box-sizing: border-box;
    background-color: lighten($primaryBlack, 7);
    position: absolute;
    z-index: 1;
    width: 100%;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    background-color: rgba(40, 40, 46, 0.7);

    p {
      margin: 0.1 0;
      text-align: center;
      color: #fff;
      font-weight: bold;
      font-size: 0.8rem;
    }

    svg {
      height: 18px;
      width: 18px;
      color: #fff;
      background-color: transparent;
      position: absolute;
      left: 55px;
      bottom: 45px;
      border-radius: 50%;
      background-color: rgba(40, 40, 46, 0.7);
    }
  }

</style>