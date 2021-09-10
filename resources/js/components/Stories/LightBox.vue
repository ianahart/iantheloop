<template>
  <div v-if="lightBoxStory !== ''" class="story_light_box_container">
    <svg @click="prevStory(lightBoxStory)" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 v story_light_box_prev" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
    </svg>
    <svg @click="nextStory(lightBoxStory)" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 story_light_box_next" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div class="lightbox_story">
      <div
        v-if="lightBoxStory.story_type === 'photo'"
        class="story_blur_container"
      >
      <img
        :src="lightBoxStory.photo_link"
        :alt="fullName"
       />
      </div>
      <div
       v-if="lightBoxStory.story_type === 'photo'"
       class="story_photo_container">
        <img
          :src="lightBoxStory.photo_link"
          :alt="lightBoxStory.photo_filename"
        />
      </div>
      <div
        v-if="lightBoxStory.story_type === 'text'"
        :style="storyBGColor"
        :class="`story_text_container ${this.storyAlignment}`"
      >
       <div :style="storyAlignment === 'text_story_align_top' ? {marginTop: '3.8rem', paddingTop: '1.5rem'} : ''" class="story_text_content">
        <p :style="{fontSize: textSize, color: textColor}">{{ lightBoxStory.text }}</p>
       </div>
      </div>
      <div class="lightbox_story_settings">
        <div class="story_settings_username">
          <img v-if="lightBoxStory.profile_picture" :src="lightBoxStory.profile_picture" :alt="fullName" />
          <DefaultIcon v-else />
          <p>{{ fullName }}</p>
        </div>
        <p class="story_posted_time">{{ lightBoxStory.displayed_time }}</p>
        <div class="story_duration_bar_container">
          <DurationBar v-for="durationBar in durationBars" :key="durationBar.id"
           :durationBar="durationBar"
           :activeDurationBar="lightBoxStory.id"
           @animate="handleDurationBar"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import DefaultIcon from '../Icons/DefaultProfileIcon.vue';
  import DurationBar from './DurationBar.vue';

  export default {
    name: 'LightBox',
    props: {

    },
    components: {
     DefaultIcon,
     DurationBar,
    },

    computed: {
      ...mapState('stories',
        [
          'stories',
          'currentUserStories',
          'lightBoxStory',
          'userIdClicked',
        ]
      ),

      ...mapGetters('user',
        [
          'getUserId',
        ]
      ),

      durationBars() {
        return this.getUserId === this.userIdClicked ? this.currentUserStories : this.stories;
      },

      fullName() {
        if(this.lightBoxStory !== '') {
          return this.lightBoxStory.full_name.split(' ').map(word => word.toUpperCase().slice(0,1) + word.toLowerCase().slice(1)).join(' ');
        }
      },

      storyBGColor() {
        return `background: ${this.lightBoxStory.background}`;
      },
      storyAlignment() {
        let className = '';
        switch(this.lightBoxStory.alignment) {
             case 'center':
               className = 'text_story_align_center';
             break;
             case 'bottom':
               className = 'text_story_align_bottom';
             break;
             case 'top':
               className = 'text_story_align_top';
             break;
             default: 'text_story_align_center';
        }
        return className;
      },

      textSize() {
        return `${this.lightBoxStory.font_size}`;
      },

      textColor() {
        return `${this.lightBoxStory.color}`;
      }
    },

    methods: {
      ...mapMutations('stories',
         [
           'SET_LIGHTBOX_STORY',
         ]
      ),

      handleDurationBar({ story }) {
         this.SET_LIGHTBOX_STORY(
           {
              story: story.id,
              btn: 'next',
              user: this.storyAuthor(story),
           }
         );
      },

      prevStory(story) {
        this.SET_LIGHTBOX_STORY(
          {
            story: story.id,
            btn: 'prev',
             user: this.storyAuthor(story)
          }
        );
      },

      nextStory(story) {
        this.SET_LIGHTBOX_STORY(
          {
            story: story.id,
            btn: 'next',
            user: this.storyAuthor(story)
          });
      },
      storyAuthor(story) {
         return story.user_id === this.getUserId ? 'currentUserStories' : 'stories';
      },
    }
  }

</script>

<style lang="scss">
  .story_light_box_container {
    box-sizing: border-box;
    background-color: #232222;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    border-radius: 10px;
    max-width:650px;
    width: 450px;
    margin: 3rem auto 2rem auto;
    min-height: 600px;
    position: relative;
  }

  .story_light_box_next,
  .story_light_box_prev {
     height: 65px;
     width: 65px;
     color: $primaryWhite;
     position: absolute;
     cursor: pointer;
     background-color: #232222;
     border-radius: 50%;
  }

  .story_light_box_next {
      top: 260px;
      right: -70px;
  }

  .story_light_box_prev {
    top: 260px;
    left: -70px;
  }

  .lightbox_story {
    box-sizing: border-box;
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    padding: 0.5rem;
    position: absolute;
    border-radius: 10px;
    z-index: 1;
  }

  .story_blur_container {
      position: absolute;
      box-sizing: border-box;
      top: 0;
      width: 100%;
      height: 100%;
      left:0;
      img {
      width: 100%;
      height: 100%;
      border-radius: 10px;
      z-index: 2;
      position: absolute;
      top: 0;
      left:0;
      filter: blur(10px);
    }
  }

  .story_photo_container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 75%;
    display: flex;
    z-index: 3;
    flex-direction: column;
    align-items: center;
    justify-content: center;
      img {
      width: 100%;
      height: 75%;
      box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
      position: absolute;
      top: 35%;
      left:0;
    }
  }

  .story_text_container {
    position: absolute;
    border-radius: 10px;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  .lightbox_story_settings {
    position: absolute;
    box-sizing: border-box;
    width: 100%;
    z-index: 3;
    right: 0;
    padding: 0.3rem;
  }

  .story_settings_username {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    box-sizing: border-box;
    img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
    }
    svg {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      color: $themePink;
      background-color: $themeLightBlue;
    }
    p {
      color: #fff;
      text-shadow: 1px 1px 0px rgb(0 0 0 / 50%);
      font-weight: bold;
      margin: 0.1rem 0 0.1rem 1.2rem;
      font-size: 0.85rem;
      font-family: 'Open Sans', sans-serif;
    }
  }

  .story_duration_bar_container {
    box-sizing: border-box;
    margin: 0.75rem 0;
    // display: flex;
    // justify-content: space-evenly;
    display: grid;
    grid-template-columns: repeat(5,1fr);
    gap: 10px;
  }

  .text_story_align_bottom {
    margin-top: auto;
    div:first-of-type {
      margin-top: auto;
    }
    flex-direction: column;
    p {
        margin-top: auto;
    }
}

.text_story_align_center {
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.text_story_align_top {
    justify-content: center;
}

.text_story_align_bottom,
.text_story_align_top,
.text_story_align_center {
  display: flex;
}

.text_story_content_align_bottom {
  margin-top: auto;
}

.story_text_content {
  box-sizing: border-box;
  width: 100%;
  p {
    text-align: center;
  }
}

.story_posted_time {
  text-align: left;
  margin: 0.3rem 0;
  color: #fff;
  text-shadow: 1px 1px 0px rgb(0 0 0 / 50%);
  font-weight: bold;
  font-size: 0.85rem;
  font-family: 'Open Sans', sans-serif;
}


  @media(max-width:800px) {
    .story_light_box_container {
      width: 95%;
      margin: 1rem auto 1rem auto;
      min-height: 400px;
    }

    .lightbox_story_settings {
      left:0;
    }

    .story_light_box_next {
       top: 140px;
       right: 2px;
       height: 45px;
       width: 45px;
       z-index: 2;
    }

    .story_light_box_prev {
       top: 140px;
       z-index: 2;
       left: 2px;
       height: 45px;
       width: 45px;
    }
  }
</style>