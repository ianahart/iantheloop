<template>
  <div class="edit_profile_pictures__container">
    <h3 class="profile_edit__title">Change Pictures</h3>
    <div class="edit_profile_pictures_icon__container">
      <CameraSolidIcon
        className="icon__md__light"
      />
    </div>
    <p class="customize__label">{{ getEditGroup.profile_picture.label }}:</p>
      <p class="customize__meta_info"><em>*Please keep all files/pictures under 2MB</em></p>
       <div v-if="getEditGroup.profile_picture.value.length && !initialProfilePic || getEditGroup.profile_picture.errors.length && !initialProfilePic" class="customize_success__container">
          <div>
            <CheckCircleIcon
              className="icon__md icon__success"
            />
            <p>Picture uploaded</p>
          </div>
          <div>
            <button @click="clearPicture('profile_picture')">Clear Upload</button>
          </div>
        </div>
        <div v-if="getEditGroup.profile_picture.errors.length">
          <p
            v-for="(error, index) in getEditGroup.profile_picture.errors"
            :key="index" class="forms__input__error"
          >
          {{ error }}
          </p>
      </div>
      <div class="edit_profile_profile_picture__container">
          <FileUpload
            @upload="handleFileUpload"
            :field="getEditGroup.profile_picture.field"
            :type="getEditGroup.profile_picture.type"
            :value="getEditGroup.profile_picture.value"
            :nameAttr="getEditGroup.profile_picture.nameAttr"
            accept="image/*"
            :maxSize="2000000"
            shape="circle"
        />
    </div>
    <div class="edit_profile_pictures__separator"></div>
    <div class="edit_profile_pictures_icon__container">
      <PictureSolidIcon
        className="icon__md__light"
      />
    </div>
     <p class="customize__label">{{ getEditGroup.background_picture.label }}:</p>
     <p class="customize__meta_info"><em>*Please keep all files/pictures under 2MB</em></p>
    <div v-if="getEditGroup.background_picture.value.length && !initialBackgroundPic || getEditGroup.background_picture.errors.length && !initialBackgroundPic" class="pictures_success__container">
        <div>
          <CheckCircleIcon
            className="icon__md icon__success"
          />
          <p>Picture uploaded</p>
        </div>
        <div>
          <button @click="clearPicture('background_picture')">Clear Upload</button>
        </div>
      </div>
      <div v-if="getEditGroup.background_picture.errors.length">
        <p
          v-for="(error, index) in getEditGroup.background_picture.errors"
          :key="index" class="forms__input__error"
        >
        {{ error }}
        </p>
      </div>
    <div class="edit_profile_background_picture__container">
      <FileUpload
        @upload="handleFileUpload"
        :field="getEditGroup.background_picture.field"
        :type="getEditGroup.background_picture.type"
        :value="getEditGroup.background_picture.value"
        :nameAttr="getEditGroup.background_picture.nameAttr"
        accept="image/*"
        :maxSize="2000000"
        shape="square"
      />
    </div>
  </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  import CameraSolidIcon from  '../Icons/CameraSolidIcon.vue';
  import CheckCircleIcon from '../Icons/CheckCircleIcon.vue';
  import PictureSolidIcon from '../Icons/PictureSolidIcon.vue';

  import FileUpload from '../forms/inputs/FileUpload.vue';



  export default {

    name: 'Pictures',

    props: {

    },

    components: {
      CameraSolidIcon,
      CheckCircleIcon,
      FileUpload,
      PictureSolidIcon,
    },

    beforeDestroy() {

    },

    computed: {


      ...mapState('profileEdit',
        [
          'initialProfilePic',
          'initialBackgroundPic'
        ]
      ),

      ...mapGetters('profileEdit',
        [
          'getEditGroup'
        ]
      ),
    },

    methods: {

      ...mapMutations('profileEdit',
        [
          'REMOVE_PICTURE',
          'SET_PICTURE',
        ]
      ),

     clearPicture(image) {

        this.REMOVE_PICTURE(image);
      },

      handleFileUpload(payload) {

        this.SET_PICTURE(payload);
      }
    }
  }
</script>

<style lang="scss">

.edit_profile_pictures_container {

  h3 {
    margin-bottom: 4rem;
  }
}

.edit_profile_pictures_icon__container {
  background-color: $themeLightBlue;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin-bottom: 1rem;
}

  .edit_profile_background_picture__container {
    box-sizing: border-box;
    width: 100%;
    height: 400px;
    padding: 1rem;
    margin: 2rem auto;

  }

  .edit_profile_pictures__separator{
    height: 2px;
    border-bottom: 1px solid $primaryGray;
    margin-bottom: 1.3rem;
  }

.edit_profile_profile_picture__container {
    display: block;
    box-sizing: border-box;
    width: 70%;
    height: 200px;
    padding: 1rem;
    margin: 2rem  auto;
    margin-left: 1rem;
}

  .customize__label {
    font-weight: bold;
    color: $mainInputLabel;
    font-size: 0.85rem;
    margin: 0.1rem 0 0.2rem 0;
  }

  .customize__meta_info {
    font-size: 0.8rem;
    color: gray;
    font-weight: 100;
    margin: 0.1rem 0;
  }

  .pictures_success__container {

    div {

      &:first-child {
        display: flex;
        margin-top: 1.3rem;
      }

      &:last-child {
        display: flex;
        justify-content: flex-end;
      }
    }

    p {
      font-size: 0.85rem;
      color: gray;
    }

    button {
      margin-top: 1.3rem;
      padding: 0.2rem 0.3rem;
      height: 35px;
      border:none;
      background-color: $primaryGray;
      border-radius: 8px;
      box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
      transition: all 0.3s ease-out;
      color: $primaryBlack;
      cursor: pointer;

      &:hover {
        background-color: darken($primaryGray, 7);
      }

    }
  }

</style>