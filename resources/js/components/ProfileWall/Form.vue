<template>
  <transition name="modal-form" appear>
    <div v-if="modalFormIsOpen && dataLoaded" class="profile_form__container">
      <header>
        <h3>Create a Post</h3>
        <div @click="closeForm">
          <CloseSolidIcon
            className="icon__md__light"
          />
        </div>
      </header>
      <form  @submit.prevent="createPost">
        <div class="profile_post__current_user">
          <img v-if="currentUserProfilePic" :src="currentUserProfilePic" />
          <DefaultProfileIcon
            v-else
            class="default_profile_image_rel_md"
          />
          <span>{{ currentUserFullName }}</span>
        </div>
        <div v-if="postErrors.length" class="profile_post_errors">
          <p
            class="profile_post_error"
            v-for="(error, index) in postErrors"
            :key="index"
          >
            {{ error }}
          </p>
        </div>
        <textarea  @input="handlePostTextChange" :value="postInputText" :placeholder="postInputPlaceholder"  class="profile_post__input_group">
        </textarea>
        <div v-if="postInputPhoto.src" class="post_form_photo__container">
          <div class="post_form_photo__overlay">
            <div @click="removeMedia(postInputPhoto.input)">
              <CloseSolidIcon
                class="icon__sm__light"
              />
            </div>
          </div>
          <img :src="postInputPhoto.src" alt="user supplied photo for a post" />
        </div>



        <div v-if="postInputVideo.src" class="post_form_video__container">
          <div class="post_form_video__overlay">
            <div @click="removeMedia(postInputVideo.input)">
              <CloseSolidIcon
                class="icon__sm__light"
              />
            </div>
          </div>
          <VideoPlayer
            :src="postInputVideo.src"
          />
        </div>
          <div class="post_form_separator"></div>
          <div class="post_form_options">
            <div class="post_form_option">
             <div>
               <PictureSolidIcon
                 className="icon__sm__theme"
               />
            </div>
             <p>Photo</p>
             <input
                name="photo"
                @change="photoFileChange"
                accept="image/*"
                type="file"
             />
           </div>
            <div class="post_form_option">
             <div>
               <VideoSolidIcon
                 className="icon__sm__theme"
               />
            </div>
             <p>Video</p>
             <input
               name="video"
               @change="videoFileChange"
               accept="video/*"
               type="file"
             />
            </div>
         </div>
         <div class="profile_create_post_btn__container">
          <button>Post</button>
         </div>
      </form>
    </div>
  </transition>
</template>


<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import CloseSolidIcon from '../Icons/CloseSolidIcon.vue';
  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';
  import PictureSolidIcon from '../Icons/PictureSolidIcon.vue';
  import VideoSolidIcon from '../Icons/VideoSolidIcon.vue';
  import VideoPlayer from './VideoPlayer.vue';

  export default {

    name: 'Form',

     props : {
      baseProfile: Object,
      currUserFollowing: Boolean,
      currentUserId: Number,
      viewUserFirstName: String,
      currentUserProfilePic: String,
    },

    components: {

      CloseSolidIcon,
      DefaultProfileIcon,
      PictureSolidIcon,
      VideoSolidIcon,
      VideoPlayer,
    },


    computed: {


      ...mapState('profile',
        [
          'dataLoaded'
        ]
      ),

      ...mapState('profileWall',
        [
          'modalFormIsOpen',
          'postInputText',
          'currentUserFullName',
          'currentUserFirstName',
          'isInputActive',
          'postInputPlaceholder',
          'postInputPhoto',
          'postInputVideo',
          'postErrors',
        ]
      ),
    },

    methods: {

      ...mapMutations('profileWall',
        [
          'CLOSE_MODAL_FORM',
          'SET_POST_INPUT_TEXT',
          'SET_INITIAL_POST_INPUT_TEXT',
          'SET_FILE',
          'SET_POST_ERROR',
          'REMOVE_POST_MEDIA',
          'RESET_POST_ERRORS',
          'RESET_POST_STATE',
        ]
      ),

      ...mapActions('profileWall',
        [
          'CREATE_POST'
        ]
      ),

      setInitialPostInputText() {

        this.SET_INITIAL_POST_INPUT_TEXT(
          {
            baseProfileUserId: this.baseProfile.user_id,
            currentUserId:this.currentUserId,
            viewUserFirstName: this.viewUserFirstName,
          }
        );
      },

      closeForm () {

        this.CLOSE_MODAL_FORM();
      },

      removeMedia(type) {

        this.REMOVE_POST_MEDIA(type);
      },

      handlePostTextChange(e) {

        let text = e.target.value;
        text = text.replace(/(\\r|\\n)/g, '<br/>');

        this.SET_POST_INPUT_TEXT(text);
      },

        readFile (file, dataObj, identifier) {

        const reader = new FileReader();

        reader.onload = (e) => {

          dataObj.src = e.target.result;
          dataObj.file = file;
          dataObj.input = identifier

          this.SET_FILE(dataObj);
        }
         reader.readAsDataURL(file);
      },

      validateFile(file, fileType, MAX_SIZE) {

        let passed;

        if (file.size > MAX_SIZE) {

          this.SET_POST_ERROR(`Please keep ${fileType} under ${MAX_SIZE.toString().substr(0, 1)}MB(megabytes)`);
          passed = false;
        } else {

          passed = true;
        }

        return passed;
      },

      changeFile(e, inputFile, identifier, MAX_SIZE) {

          this.RESET_POST_ERRORS();
          const inputName = e.target.name;
          const file = e.target.files[0];

          if (!file) {

            return;
          }
            this.readFile(file, inputFile, inputName);

          const validated = this.validateFile(file, identifier, MAX_SIZE);

          if(validated) {

            this.RESET_POST_ERRORS();

            this.readFile(file, inputFile, inputName);
            e.target.value = '';
          }
           e.target.value = '';
      },


      photoFileChange (e) {

        if (this.postInputVideo.src.length && this.postInputVideo.file !== null) {

          this.removeMedia(this.postInputVideo.input);
        }

        this.changeFile(e, this.postInputPhoto, 'photos', 2000000);

      },

      videoFileChange(e) {

          if (this.postInputPhoto.src.length && this.postInputPhoto.file !== null) {

          this.removeMedia(this.postInputPhoto.input);
        }

        this.changeFile(e, this.postInputVideo, 'videos', 4000000);
      },

      async createPost () {

        this.RESET_POST_ERRORS();

        if (this.postInputPhoto.file !== null) {
          this.validateFile(this.postInputPhoto, 'photos', 2000000);
        }

        if (this.postInputVideo.file !== null) {
          this.validateFile(this.postInputVideo, 'videos', 4000000);
        }

        if (this.postInputText.length > 0 && !this.postErrors.length) {

            await this.CREATE_POST(
              {
                author_user_id: this.currentUserId,
                subject_user_id: parseInt(this.baseProfile.user_id)
              }
            );

          if (!this.postErrors.length) {

            this.RESET_POST_STATE();
            this.RESET_POST_ERRORS();
            this.CLOSE_MODAL_FORM();
          }

        } else {

          this.SET_POST_ERROR(`${this.currentUserFirstName}, Please make sure the uploads are correct size and a message is present.`);
        }
      },
    },
 }
</script>

<style lang="scss">

.modal-form-enter-active, .modal-form-leave-active {
  transition: opacity .5s;
}
.modal-form-enter, .modal-form-leave-to {
  opacity: 0;
}

.profile_form__container {
  background-color: lighten($primaryBlack, 1);
  box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
  position: absolute;
  box-sizing: border-box;
  z-index: 16;
  top: -350px;
  width: 600px;
  border-radius: 8px;

  header {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    border-bottom: 1px solid darken($primaryBlack, 3);
    h3 {
      margin: 0.1rem;
      flex-grow: 1;
      text-align: center;
      color: $primaryWhite;
      font-family: 'Source One', sans-serif;
    }
  }

  form {
    padding: 0.5rem;
    box-sizing: border-box;
  }
}

.profile_post__current_user {
  box-sizing: border-box;
  display: flex;
  align-items: center;


  img {
    height: 75px;
    width:75px;
    border-radius: 50%;
  }
  svg {
    color: $themePink;
    background: $themeLightBlue;
  }

  span {
    align-self: flex-start;
    color: $primaryGray;
    font-size: 1.1rem;
  }
}

.profile_post__input_group {
  box-sizing: border-box;
  margin-top: 1.5rem;
  color: $primaryGray;
  outline: none;
  height: 100px;
  width: 100%;
  overflow-y:auto;
  border: none;
  resize: none;
  font-family: 'Open Sans', sans-serif;
  background-color: lighten($primaryBlack, 1);

  &::placeholder {
    color: $primaryGray;
    font-size: 0.9rem;
  }
}


.post_form_separator {
  margin-top: 0.5rem;
  height: 2px;
  border-top: 2px solid darken($primaryBlack, 3);
}

.post_form_options {
  display: flex;
  align-items: center;
  justify-content: space-evenly;
  padding: 0.5rem 0.5rem 0 0.5rem;
  box-sizing: border-box;
}

.post_form_option {
  display: flex;
  cursor: pointer;
  position: relative;

  div {
    &:first-of-type {
     transform: rotate(-15deg);
    }
  }

  p {
    margin: 0.1rem;
    margin-left: 0.3rem;
    color: darken($primaryGray, 7);
    cursor: pointer;
    font-size: 0.95rem;
    display:flex;
    flex-direction: column;
    justify-content: flex-end;
  }

  input {
    position: absolute;
    top:0;
    left: 0;
    width: 100%;
    opacity: 0;
    cursor: pointer;
    height: 100%;
    z-index: 5;
  }
}

.profile_create_post_btn__container {
    text-align: center;
    margin-top: 0.5rem;
    box-sizing: border-box;
    width: 100%;

    button {

      background-color: darken($primaryBlack, 7);
      width: 100%;
      border: none;
      color: $primaryGray;
      font-family: 'Secular One', sans-serif;
      height: 35px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease-out;

      &:hover {
        background-color: darken($primaryBlack, 4);

      }
    }
}

.post_form_photo__container {
  width: 200px;
  height: 200px;
  box-sizing: border-box;
  border-radius: 8px;
  margin: 0 auto;
  margin-bottom: 0.5rem;
  position: relative;

  img {
      border-radius: 8px;
      box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
      width: 100%;
      height: 100%;
  }
}

.post_form_video__container {
  width: 300px;
  height: 200px;
  box-sizing: border-box;
  border-radius: 8px;
  margin: 0 auto;
  margin-bottom: 0.5rem;
  position: relative;
}


.post_form_video__overlay {
  position: absolute;
  z-index: 4;
  top:0;
  left:0;
  width: 100%;
  height: 100%;
  display:flex;
  flex-direction: row;
  justify-content: flex-end;
  background-color: rgba(0, 0, 0, 0.6);

    svg {

    position: absolute;
    top:0;
    right: -2px;
  }
}

.post_form_photo__overlay
 {
  background-color: rgba(0, 0, 0, 0.6);
  border-radius: 8px;
  position: absolute;
  z-index: 4;
  top:0;
  left:0;
  width: 100%;
  height: 100%;
  display:flex;
  flex-direction: row;
  justify-content: flex-end;
}

.profile_post_errors {
  padding:0.3rem;
  margin: 0 auto;
  margin-top: 0.1rem;
  text-align: center;
  width: 70%;
  background-color: darken($primaryBlack, 7);
  border-radius: 8px;
}

.profile_post_error {
    margin: 0.1rem;
    font-size: 0.8rem;
    color: $error;
}



@media(max-width:600px) {

  .profile_form__container {
    width: 95%;
    margin:0  auto;
  }
}

</style>

