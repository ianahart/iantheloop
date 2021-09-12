<template>
   <div class="create_story_container">
     <div class="create_story_grid">
        <Sidebar>
          <template v-slot:Sidebar>
            <SidebarHeader
              :page="$route.name"
            />
            <div v-if="isFormOpen" class="story_form_component_container">
              <Form />
            </div>
          </template>
      </Sidebar>
       <div class="create_story_showcase_options">
        <div class="create_text_story_preview_container">
         <Preview
           v-if="isFormOpen"
           :newStory="newStory"
           :storyType="storyType"
         />
       </div>
        <div v-if="!isFormOpen" class="showcase_options_container">
          <div @click="openForm('text')" class="showcase_option_text">
            <div class="showcase_option_text_content">
              <h2>Text Story</h2>
              <h3>Aa</h3>
            </div>
          </div>
          <div class="showcase_option_photo">
            <div class="showcase_option_photo_content">
              <h2>Photo Story</h2>
              <CameraSolidIcon />
              <p v-if="photoError.length">{{ photoError }}</p>
              <PhotoUpload
                @error="setError"
                @upload="handlePhotoStory"
                :photoError="photoError"
              />
            </div>
          </div>
        </div>
       </div>
     </div>
   </div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';


  import CameraSolidIcon from '../components/Icons/CameraSolidIcon.vue';
  import Form from '../components/Stories/Form.vue';
  import Preview from '../components/Stories/Preview.vue';
  import PhotoUpload from '../components/Stories/PhotoUpload.vue';
  import Sidebar from '../components/Stories/Sidebar.vue';
  import SidebarHeader from '../components/Stories/SidebarHeader.vue';

  export default {

    name: 'CreateStory',
    components: {
      CameraSolidIcon,
      Form,
      Preview,
      PhotoUpload,
      Sidebar,
      SidebarHeader,
    },

    data () {
      return {
        photoError: '',
      }
    },

    async created() {
      await this.getActiveStoryCount();
    },

    beforeDestroy() {
      this.RESET_STORIES_MODULE();
    },

    computed: {
      ...mapState('stories',
        [
          'isFormOpen',
          'newStory',
          'storyType',
        ]
      ),
      ...mapGetters('user',
        [
          'getUserSlug',
          'getUserId',
        ]
      ),
    },

    methods: {
      ...mapMutations('stories',
        [
          'RESET_STORIES_MODULE',
          'SET_FORM_OPEN',
          'SAVE_PHOTO_FILE',
        ]
      ),
      ...mapActions('stories',
        [
          'ACTIVE_STORY_COUNT'
        ]
      ),

      setError(error) {
        this.photoError = error;
      },

      handlePhotoStory(photo) {
        if (!this.photoError.length) {
          this.SAVE_PHOTO_FILE(photo);
          this.openForm('photo');
        }
      },

      async getActiveStoryCount() {
        await this.ACTIVE_STORY_COUNT(this.getUserId);
      },

      openForm(storyType) {
        this.SET_FORM_OPEN({storyType, isFormOpen: true});
      },
    },
  }
</script>

<style lang="scss">
  .create_story_container {
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    background-color: lighten($primaryBlack, 1);
  }

  .create_story_grid {
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    height: 100%;
  }

  .create_story_showcase_options{
    box-sizing: border-box;
    background-color: #232222;
    flex-grow: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 500px;
  }


  .showcase_options_container {
    width: 80%;
    max-width: 1140px;
    box-sizing: border-box;
    display: flex;
    justify-content: space-evenly;
    height: 100%;
    align-items: center;

  }

  .showcase_option_text,
  .showcase_option_photo {
    box-sizing: border-box;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    border-radius: 10px;
    width: 35%;
    max-width: 220px;
    min-width: 200px;
    height: 490px;
    cursor: pointer;
    position: relative;
  }

    .showcase_option_text {
    background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(145,253,29,1) 50%, rgba(252,176,69,1) 100%);
  }
    .showcase_option_photo {
    background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);
  }


  .showcase_option_text_content,
  .showcase_option_photo_content {
    box-sizing: border-box;
    height: 100%;
    padding: 0.3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    h2 {
      font-family: 'Secular One', sans-serif;
      color: darken($primaryBlack, 2);
      margin: 0.1rem 0;
      font-size: 1.5rem;
      font-weight: bold;
      text-shadow: 0px 0px 6px rgba(255,255,255,0.7);
    }
  }

  .showcase_option_text_content {
     h3 {
      font-family: 'Open Sans', sans-serif;
      color: darken($primaryBlack, 2);
      margin: 0.1rem 0;
      font-size: 2.8rem;
      text-shadow: 2px 2px 3px rgb(255 255 255 / 40%);
      width: 75px;
      height: 75px;
      box-sizing: border-box;
      padding: 0.2rem;
      margin-top: 0.1rem;
      text-align: center;
      border-radius: 50%;
      background-color: rgba(0,0,0,0.4);
      display: flex;
      justify-content: center;
      align-items: center;
     }
  }

  .showcase_option_photo_content {
    p {
      margin: 0.1rem 0;
      color: #b6b6b6;
      text-align: center;
      font-weight: bold;
      font-size: 0.75rem;
    }
    svg {
        color: darken($primaryBlack, 2);
        height: 75px;
        width: 75px;
        filter: drop-shadow(3px 5px 10px rgb(255 255 255 / 0.5));
        box-sizing: border-box;
        padding: 0.2rem;
        margin-top: 0.1rem;
        text-align: center;
        border-radius: 50%;
        background-color: rgba(0,0,0,0.4);
        display: flex;
        justify-content: center;
        align-items: center;
    }
  }

  .story_form_component_container {
    box-sizing: border-box;
    margin: 3.5rem auto 1.5rem auto;
    max-width: 85%;
  }

  .create_text_story_preview_container {
    box-sizing: border-box;
    width: 100%;
    display: flex;
    justify-content: center;
  }

  @media(max-width:800px) {
    .create_story_grid {
      flex-direction: column;
    }

    .create_text_story_preview_container {
      height: 100%;
    }


    .create_story_showcase_options {
      flex-grow: 1;
      width: 100%;
      height: 100%;
    }

    .showcase_options_container {
      height: 100%;
      flex-direction: column;
      padding: 0.5rem;
      max-width: 100%;
    }

    .showcase_option_text,
    .showcase_option_photo {
       height: 300px;
       width: 95%;
       max-width: 95%;
       margin: 2rem auto;
    }
  }
</style>