<template>
  <div class="story_preview_container">
    <div class="story_photo_preview" v-if="storyType === 'photo'">
      <img v-if="newStory.file.src" :src="newStory.file.src" alt="story photo to be uploaded" />
    </div>
    <button @click="exitPreview">Exit Preview</button>
    <div v-if="storyType === 'text'" :style="previewStyle" class="story_preview">
      <div :class="`story_preview_content ${storyAlignment}`">
        <p>{{ newStory.text }}</p>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {
    name: 'Preview',
    props: {
      newStory: Object,
      storyType: String,
    },
    components:{

    },
    computed: {

       previewStyle() {
         return `
             background: ${this.background};
             fontSize: ${this.newStory.font_size};
             color: ${this.newStory.color};
         `;
       },

       background() {
         return this.storyType === 'text' ? this.newStory.background : 'radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);';
       },

       storyAlignment() {
         let alignment;
         switch(this.newStory.alignment) {
              case 'top':
                alignment = 'story_preview_align_top';
              break;
              case 'bottom':
                alignment = 'story_preview_align_bottom';
              break;
              case 'center':
                alignment = 'story_preview_align_center';
              break;
              default:
                alignment = 'story_preview_align_center';
         }
         return alignment;
       }
    },
    methods: {
      ...mapMutations('stories',
        [
          'SET_FORM_OPEN',
          'CLEAR_STORY_FORM'
        ]
      ),
       exitPreview() {
         this.SET_FORM_OPEN({formType: this.formType, isFormOpen: false});
         this.CLEAR_STORY_FORM();
       }
    }
  }

</script>

<style lang="scss">

.story_photo_preview {
  box-sizing: border-box;
  position: absolute;
  border-radius: 10px;
  z-index: 1;
  top:0;
  left:0;
  height: 100%;
  width: 100%;
  img {
    box-sizing: border-box;
    position: absolute;
    border-radius: 10px;
    top:0;
    left:0;
    height: 100%;
    width: 100%;
    z-index: 2;
  }
}

.story_preview_align_bottom {
    display: flex;
    flex-direction: column;
    p {
        margin-top: auto;
    }
}

.story_preview_align_center {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.story_preview_align_top {
    display: flex;
    justify-content: center;
}


  .story_preview_container {
    position: relative;
    padding-top: 1.7rem;
    box-sizing: border-box;
    border-radius: 10px;
    background-color: lighten(#232222, 6);
    width: 400px;
    height:600px;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;

    button:first-of-type {
      border: none;
      background-color: lighten($themeLightBlue,6);
      color: darken($primaryGray, 3);
      position: absolute;
      z-index: 1;
      top: 5px;
      right: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
      border-radius: 0.25rem;
      font-weight: bold;
      padding: 0.3rem 0.2rem;
      &:hover {
        opacity: 0.75;
      }
    }
  }

  .story_preview {
    box-sizing: border-box;
    background-color: #232222;
    padding: 0.5rem;
    border-radius: 10px;
    width: 350px;
    height: 550px;
  }

  .story_preview_content {
    word-break: break-all;
    box-sizing: border-box;
    padding: 0.2rem;
    width: 100%;
    height: 100%;
    p {
      text-align: center;
    }
  }

  @media(max-width:600px) {
    .story_preview_container {
      width: 95%;
      margin: 0 auto;
      height: 320px;
      margin-bottom: 1.5rem;
    }

    .story_preview {
      height: 280px;
      width: 85%;
      margin: 0 auto;
    }
  }
</style>