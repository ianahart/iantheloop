<template>
  <transition name="fade-form" mode="in-out" appear>
    <form class="story_form_container" @submit.self.prevent="handleSubmit">
      <div v-if="validationErrors.length" class="story_form_validation_container">
        <p v-for="(validationError , index) in validationErrors" :key="index">{{ validationError }}</p>
      </div>
      <div v-if="storyType === 'photo'" class="story_form_photo_fields">
        <p class="edit_photo_story_error" v-if="storyError.length">{{ storyError }}</p>
        <div class="story_form_photo_duration_container">
          <p class="story_form_label">Duration:</p>
          <Duration />
        </div>
        <p v-if="photoError.length" class="edit_photo_story_error">{{ photoError }}</p>
        <div class="edit_photo_story_upload">
          <p class="story_form_label">Change Photo:</p>
          <PictureIcon />
          <PhotoUpload
              @error="setError"
              @upload="handlePhotoStory"
              :photoError="photoError"
          />
        </div>
      </div>
      <div v-if="storyType === 'text'" class="story_form_customizations_container">
         <div class="story_form_alignment_container">
            <p class="story_form_label">Alignment:</p>
            <div class="story_form_alignments">
              <div
                @click="selectAlignment('top')"
                :class="`story_form_alignment_top ${newStory.alignment === 'top' ? 'story_form_alignment_selected' : 'story_form_default'}`">
                <AlignmentIcon />
              </div>
              <div
                @click="selectAlignment('center')"
                :class="`story_form_alignment_center ${newStory.alignment === 'center' ? 'story_form_alignment_selected' : 'story_form_default'}`"
              >
                <AlignmentIcon />
              </div>
              <div
                @click="selectAlignment('bottom')"
                :class="`story_form_alignment_bottom ${newStory.alignment === 'bottom' ? 'story_form_alignment_selected' : 'story_form_default'}`">
                <AlignmentIcon />
              </div>
            </div>
          </div>
          <div class="story_selects_container">
            <div class="story_select_container">
              <p class="story_form_label">Duration:</p>
              <Duration />
            </div>
            <div class="story_select_container">
              <p class="story_form_label">Text Size:</p>
              <CustomSelect
                  marker="font_size"
                  @selected="handleSelection"
                  className="custom_select__container story_select custom_select_size__sm"
                  commitPath="stories/UPDATE_STORY_FIELD"
                  :errors="[]"
                  label="Font Size"
                  :value="newStory.font_size"
                  nameAttr="font_size"
                  field="font_size"
                  :options="[{name: '12px', abbrv: '12px', id: 1}, {name: '24px', abbrv: '24px', id: 2}, {name: '36px', abbrv: '36px', id: 3}]"
                  :selected="newStory.font_size"
                />
            </div>
          </div>
      <p class="story_text_error">
        {{ storyError }}
      </p>
      <div class="story_form_text_area_container">
        <textarea
          :value="newStory.text"
          @change="handleTextChange"
          @keyup="handleKeyUp"
          placeholder="Add Text"
          >
        </textarea>
      </div>
      <p class="story_form_label_auto">Story Background:</p>
      <div class="story_form_text_background_container">
        <BackgroundOption
          v-for="(background, index) in backgrounds" :key="index"
          @selected="setBackgroundSelection"
        />
      </div>
      <div class="story_form_text_color_container">
        <p class="story_form_label">Text Color:<p>
        <div class="story_form_text_colors">
          <div :style="newStory.color === 'black' ? 'transform: scale(1.2)' : ''" @click.stop.prevent="selectColor('black')"><span :class="selectedTextColor.selectedBlack"></span></div>
          <div :style="newStory.color === 'white' ? 'transform: scale(1.2)' : ''" @click.stop.prevent="selectColor('white')"><span :class="selectedTextColor.selectedWhite"></span></div>
        </div>
      </div>
    </div>
      <div class="story_action_btns_container">
        <button @click.stop.prevent="clearStoryForm">Clear</button>
        <button :disabled="storyError.length && storyType === 'text' ? true : false" type="submit">Share</button>
      </div>
    </form>
  </transition>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import BackgroundOption from './BackgroundOption.vue';
  import CustomSelect from '../forms/selects/CustomSelect.vue';
  import AlignmentIcon from '../Icons/AlignmentIcon.vue';
  import Duration from './Duration.vue';
  import PhotoUpload from './PhotoUpload.vue';
  import PictureIcon from '../Icons/PictureSolidIcon.vue';


  export default {
    name: 'Form',
    props: {

    },
    components: {
      BackgroundOption,
      CustomSelect,
      AlignmentIcon,
      Duration,
      PhotoUpload,
      PictureIcon,

    },
    data () {
      return {
        backgrounds: [1,2,3,4,5,6,7,8,9,10],
        debounceID: '',
        photoError: '',
      }
    },

    mounted() {

    },

    beforeDestroy() {
      clearTimeout(this.debounceID);
    },

    computed: {
       ...mapState('stories',
        [
          'newStory',
          'storyType',
          'storyError',
          'validationErrors',
        ]
      ),

      selectedTextColor() {
          return {
            selectedBlack : this.newStory.color === 'black' ? 'story_form_selected_color' : 'story_form_default_color',
            selectedWhite : this.newStory.color === 'white' ? 'story_form_selected_color' : 'story_form_default_color',
          }
      },
    },

    methods: {
      ...mapMutations('stories',
        [
          'UPDATE_STORY_FIELD',
          'SET_STORY_ERROR',
          'CLEAR_STORY_FORM',
          'SAVE_PHOTO_FILE',
          'CLEAR_VALIDATION_ERRORS',
        ]
      ),
      ...mapActions('stories',
        [
          'CREATE_STORY'
        ]
      ),

       setError(error) {
        this.photoError = error;
      },

      handlePhotoStory(photo) {
        if (!this.photoError.length) {
          this.SAVE_PHOTO_FILE(photo);
        }
      },

      handleKeyUp(e) {
        this.debounce(() => {
        this.UPDATE_STORY_FIELD(
            {
              field: 'text',
              value: e.target.value,
              error: '',
              form: null
            });
        }, 350);
      },

      selectAlignment(alignment) {
        this.UPDATE_STORY_FIELD(
          {
            field: 'alignment',
            value: alignment,
            error: '',
            form: null,
          }
        );
      },

      selectColor(color) {
          this.UPDATE_STORY_FIELD(
          {
            field: 'color',
            value: color,
            error: '',
            form: null,
          }
        );
      },

      handleTextChange(e) {
          this.checkStoryTextError();
      },

      debounce(fn, delay = 400) {
        return ((...args) => {
            clearTimeout(this.debounceID);

            this.debounceID = setTimeout(() => {
                this.debounceID = null;

                fn(...args);
            }, delay);
        })();
      },

      clearStoryForm() {
        this.CLEAR_STORY_FORM();
      },

      checkStoryTextError() {

        if (this.newStory.text.trim().length > 150) {
           this.SET_STORY_ERROR('Please keep the text in your story under 150 characters.');
        } else {
          this.SET_STORY_ERROR('');
        }
      },

      async handleSubmit(e) {
        this.SET_STORY_ERROR('');
        this.CLEAR_VALIDATION_ERRORS();
        let canSubmit = false;

        if (this.storyType === 'text') {
            this.checkStoryTextError();
          if (!this.storyError.length && this.newStory.text.length) {
            canSubmit = true;
          }
        }
        if (this.storyType === 'photo') {
          const file = this.newStory.file.file !== null && !this.photoError.length;
          if (file && !this.storyError.length) {
            canSubmit = true;
          }
        }
        if (canSubmit) {
          await this.CREATE_STORY();
        }
      },

      handleSelection ({ selection }) {
        this.UPDATE_STORY_FIELD(selection);
      },

      setBackgroundSelection(selection) {
        this.UPDATE_STORY_FIELD(
          {
            field: 'background',
            value: selection,
            error: '',
            form: null
          });
      },
    }
  }


</script>
<style lang="scss">

  .fade-form-enter-active,
  .fade-form-leave-active {
      transition: all 0.45s;
  }
  .fade-form-enter,
  .fade-form-leave-to {
      opacity: 0;
  }

  .story_text_error {
    color: silver;
    font-weight: bold;
    font-size: 0.85rem;
    text-align: left;
  }

 .story_form_container {
   box-sizing: border-box;
   padding: 0.5rem;
   width: 100%;
   margin: 0 auto;
 }

 .story_form_text_area_container {
   box-sizing: border-box;
   width: 100%;
   min-height: 175px;

   textarea {
     padding: 0.4rem;
     box-sizing: border-box;
     resize: none;
     border-radius: 10px;
     border: 1px solid #615f5f;
     background-color: transparent;
     color: $primaryWhite;
     font-family: 'Open Sans',sans-serif;
     height: 100%;
     min-height: 175px;
     width: 100%;
     outline: none;
     &::placeholder {
        color: darken(silver,4);
     }
   }
 }

 .story_form_text_background_container {
   box-sizing: border-box;
   width: 100%;
   margin: 0 auto;
   padding: 0.4rem;
   border-radius: 10px;
   border: 1px solid #615f5f;
   background-color: transparent;
   color: $primaryWhite;
   font-family: 'Open Sans',sans-serif;
   height: 100%;
   min-height: 75px;
   display: grid;
   grid-template-columns: repeat(4, 1fr);
   gap: 0.6rem;
 }

 .story_form_customizations_container {
   box-sizing: border-box;
   width: 100%;
   margin: 0 auto;
   padding: 0.4rem;
   border-radius: 10px;
   display: flex;
   justify-content: space-evenly;
   flex-direction: column;
   align-items: center;
   background-color: transparent;
   color: $primaryWhite;
   font-family: 'Open Sans',sans-serif;
   height: 100%;
   min-height: 150px;
 }

 .story_selects_container {
   display: flex;
   justify-content: space-evenly;
 }

 .story_select_container {
   display: flex;
   flex-direction: column;
   margin: 0.1rem 1rem;
 }

 .story_select_container .custom_select__container {
   div {
    &:first-of-type {
      border: 1px solid #615f5f;
    }
   }
 }

 .story_select {
  p {
    color: $primaryWhite;
    font-weight: bold;
    font-size: 0.7rem;
  }
  svg {
    color: $primaryWhite;
  }
 }

 .story_action_btns_container {
   margin: 3.5rem auto 2rem auto;
   display: flex;
   justify-content: space-evenly;
   align-items: center;
   width: 100%;
   padding:0.4rem;
   box-sizing: border-box;

   button {
     height: 35px;
     margin: 0 0.7rem;
     transition: all 0.3s ease-in-out;
     cursor: pointer;
     border: none;
     box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
     border-radius: 40px;
     width: 150px;
     padding: 0.4rem 0.2rem;
     font-weight: bold;
     &:hover {
       opacity: 0.7;
     }
     &:first-of-type {
       background-color: $primaryGray;
       color: $primaryBlack;
     }
     &:last-of-type {
       background-color: lighten($themeLightBlue, 5);
       color: $primaryGray;
     }
   }
 }

 .story_form_alignment_container {
   width: 100%;
   margin: 1.3rem auto;
   box-sizing: border-box;
   p:first-child {
    margin: 0.5rem 0;
   }
 }

 .story_form_alignments {
   box-sizing: border-box;
   width: 100%;
   display: flex;
   justify-content: space-evenly;
 }

 .story_form_alignment_center {
    justify-content: center;
    flex-direction: column;
    align-items: center;
 }

 .story_form_alignment_bottom {
    flex-direction: column;
    svg {
      margin-top: auto;
    }
 }

 .story_form_alignment_top {
   justify-content: center;
 }

 .story_form_alignment_selected {
      background-color: #232222;
 }
 .story_form_alignment_default {
   background-color: transparent;
 }

 .story_form_alignment_center,
 .story_form_alignment_bottom,
 .story_form_alignment_top {
   display: flex;
   cursor: pointer;
   margin: 0 1.2rem;
   width: 40px;
   height: 40px;
   border: 1px solid #615f5f;
   box-sizing: border-box;
   padding: 0.2rem;
   border-radius: 8px;
   text-align: center;
   svg {
     height: 20px;
     width: 20spx;
     color: $primaryWhite;
   }
 }

 .story_form_text_color_container {
   margin-top: 1.5rem;
 }

 .story_form_photo_duration_container {
   box-sizing: border-box;
   display: flex;
   justify-content: center;
 }

 .story_form_text_colors {
   margin-top: 1.5rem;
   box-sizing: border-box;
   display: flex;

   div {
     border-radius: 50%;
     height: 30px;
     width: 30px;
     margin: 0 1rem;
     border-radius: 8px;
     box-sizing: border-box;
     border: 1px solid #615f5f;
     cursor: pointer;
     position: relative;
     span {
       box-sizing: border-box;
       border-radius: 8px;
       display: block;
       height: 100%;
       top:0;
       left:0;
       width:100%;
       position: absolute;
     }
   }

   .story_form_selected_color {
      background-color: transparent;
      transition: all 0.3s ease-in-out;
   }

   .story_form_default_color {
      background-color: rgba(0,0,0,0.4);
   }

   div:first-child {
     background-color: black;

   }
   div:last-child {
     background-color: white;
   }
 }

 .edit_photo_story_upload {
    position: relative;
    box-sizing: border-box;
    border-radius: 50%;
    padding: 0.5rem;
    display: flex;
    justify-content: center;
    width: 90px;
    margin: 2rem 0;
    height: 90px;
    p {
      margin-top: -1rem;
    }
    svg {
     height: 75px;
    width: 75px;
    background: transparent;
    color: #615f5f;
    border-radius: 10px;
    position: absolute;
    z-index: 1;
    top: 20px;
    box-shadow: 0px 2px 3px rgb(0 0 0 / 20%);
   }
}

.edit_photo_story_error {
    margin-top: 2rem;
    font-size: 0.7rem;
    font-weight: bold;
    color: silver;
}


.story_form_validation_container {
  box-sizing: border-box;
  padding: 0.2rem;
  display: flex;
  justify-content: center;

  p {
    margin-top: 0.2rem 0;
    font-size: 0.7rem;
    font-weight: bold;
    color: silver;
  }
}



 .story_form_label {
      margin: 0.1rem 0;
      color: #b6b6b6;
      text-align: left;
      font-size: 0.6rem;
 }

 .story_form_label_auto {
      margin: 0.9rem 0;
      margin-right: auto;
      color: #b6b6b6;
      text-align: left;
      font-size: 0.6rem;
 }

 .story_form_photo_fields {
   display: flex;
   flex-direction: column;
   align-items: flex-start;
 }


 @media(max-width:600px) {
   .story_action_btns_container {
     margin-top: 1.5rem auto 1rem auto;
   }

   .story_select_container {
        margin: 0;
   }
 }
</style>