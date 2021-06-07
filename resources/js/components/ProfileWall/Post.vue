<template>
  <div ref="post" class="post__container">
    <div class="post_top__container">
      <div class="post_top__column">
        <div class="post_author_profile_pic__container">
          <img v-if="post.profile_picture" :src="post.profile_picture" />
          <DefaultProfileIcon
            v-else
            className="default_profile_image_rel_sm"
          />
        </div>
        <div class="post_header__container">
          <div class="post__names">
            <router-link
              :to="{name: 'Profile', params: {id: `${post.author_user_id.toString()}`}}"
            >
              <p>{{ post.full_name }}</p>
            </router-link>
            <PlaySolidIcon
              className="icon__xsm__dark"
            />
            <p>{{ post.subject_name }}</p>
        </div>
        <div class="posted__date">
          <p>{{ post.post_posted }}</p>
        </div>
        </div>
      </div>
      <div
        @click="togglePostOptions"
        class="post_options_dots__container"
      >
        <PostActions
          v-if="isPostOptionsOpen"
          :authorUserId="post.author_user_id"
          :subjectUserId="post.subject_user_id"
          :postId="post.id"
        />
        <div ref="postActionsTrigger">
          <HorizontalDotsIcon
            className="horizontal_dots__icon"
            marker="postActions"
          />
        </div>
      </div>
    </div>
    <div class="post_body__container_row">
      <p class="post_body__text">{{ post.post_text }}</p>
      <div
        v-if="post.photo_link !== null || post.video_link !== null"
        class="post_body__media_container"
      >
        <a
          v-if="post.photo_link !== null"
          :href="post.photo_link"
        >
          <img
            v-if="post.photo_link !== null"
            :src="post.photo_link" :alt="post.photo_filename"/>
        </a>
        <div
          v-if="post.video_link !== null"
          class="post_body__media_player"
        >
          <a
            v-if="post.video_link !== null"
            :href="post.video_link"
          >
            <VideoPlayer
              :src="post.video_link"
            />
          </a>
        </div>
      </div>
    </div>
    <div class="post__divider"></div>
  </div>
</template>


<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import HorizontalDotsIcon from '../Icons/HorizontalDotsIcon.vue';
  import PlaySolidIcon from '../Icons/PlaySolidIcon.vue';
  import VideoPlayer from './VideoPlayer.vue';
  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';
  import PostActions from './PostActions.vue';


  export default {

    name: 'Post',

    props: {
      post: Object,
    },

    components: {
      HorizontalDotsIcon,
      PlaySolidIcon,
      VideoPlayer,
      DefaultProfileIcon,
      PostActions,
    },

    data () {
      return {
        isPostOptionsOpen: false,
      }
    },

    mounted() {
      this.$refs.post.addEventListener('click', this.closeOptionsFallback);
    },

    beforeDestroy() {
      this.$refs.post.removeEventListener('click', this.closeOptionsFallback);
    },

    computed : {

    },

    methods: {

      togglePostOptions () {
        this.isPostOptionsOpen = !this.isPostOptionsOpen;
      },

      closeOptionsFallback (e) {

        if (e.target.id !== 'postActions') {

          this.isPostOptionsOpen = false;
        }


      }
    },
  }
</script>

<style lang="scss">

.post__container {
  background-color: $primaryGray;
  border: 1px solid $primaryGray;
  padding: 0.5rem;
  width: 80%;
  margin: 0 auto;
  border-radius: 8px;
  box-shadow: rgb(0 0 0 / 10%) 0px 20px 25px -5px, rgb(0 0 0 / 4%) 0px 10px 10px -5px;
  margin-bottom: 1.5rem;
  box-sizing: border-box;
}


.post_top__container {
  box-sizing: border-box;
  display: flex;
  justify-content: space-between;
}

.post_top__column {
  display: flex;
}

.post__names {
  display: flex;
  align-items: center;

  a {
    cursor: pointer;
    text-decoration: none;
  }

  p {
    margin: 1rem 0.3rem 0 0.3rem;
    font-family: 'Secular One', sans-serif;
    font-weight: 200;
    color: lighten($primaryBlack, 5)
  }

  svg {
     margin: 1rem 0.3rem 0 0.3rem;
     color: darken($primaryGray, 15);

  }
}

.post_author_profile_pic__container {

    img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
  }

  svg {
    color: $themePink;
    background: $themeLightBlue;
  }
}

.posted__date {

  p {
    font-size: 0.8rem;
    color: gray;
    margin-top: 0.4rem;
  }
}

.post_body__container_row {
  margin-top: 1.2rem;
  box-sizing: border-box;
}

.post_body__text {
  font-size: 0.9rem;
  color: lighten($primaryBlack, 4);
  font-family: 'Open Sans', sans-serif;
  line-height: 1.6;
}

.post__divider {
  border-top: 1px solid $mainInputBorder;
  width: 100%;
  margin: 0.5rem 0;
}

.post_body__media_container {
  display: flex;
  justify-content: center;

  img {
    width: 200px;
    height: 200px;
    border-radius: 8px;
  }
}

.post_body__media_player {
    width: 200px;
    height: 200px;
    border-radius: 8px;
}

.post_options_dots__container {
  position: relative;
}



@media (max-width: 600px) {

  .post__container {
    width: 100%;
  }
}

</style>
