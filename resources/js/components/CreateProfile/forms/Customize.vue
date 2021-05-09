<template>
  <div class="customize__container">
    <h4>Customize your profile by uploading pictures</h4>
      <p class="customize__label">{{ getBackgroundImage.label }}:</p>
      <p class="customize__meta_info"><em>*Please keep all files/pictures under 2MB</em></p>
      <div v-if="getBackgroundImage.value.length" class="customize_success__container">
        <div>
          <CheckCircleIcon
            className="icon__md icon__success"
          />
          <p>Picture uploaded</p>
        </div>
        <div>
          <button @click="clearImage('background_image')">Clear Upload</button>
        </div>
      </div>
      <div v-if="getBackgroundImage.errors.length">
        <p
          v-for="(error, index) in getBackgroundImage.errors"
          :key="index" class="forms__input__error"
        >
        {{ error }}
        </p>
      </div>
    <div class="background_image__container">
      <FileUpload
        @upload="handleFileUpload"
        :field="getBackgroundImage.field"
        :type="getBackgroundImage.type"
        :value="getBackgroundImage.value"
        :nameAttr="getBackgroundImage.nameAttr"
        accept="image/*"
        :maxSize="2000000"
        shape="square"
      />
    </div>
      <p class="customize__label">{{ getProfileImage.label }}:</p>
      <p class="customize__meta_info"><em>*Please keep all files/pictures under 2MB</em></p>
       <div v-if="getProfileImage.value.length" class="customize_success__container">
        <div>
          <CheckCircleIcon
            className="icon__md icon__success"
          />
          <p>Picture uploaded</p>
        </div>
        <div>
          <button @click="clearImage('profile_image')">Clear Upload</button>
        </div>
      </div>
      <div v-if="getProfileImage.errors.length">
        <p
          v-for="(error, index) in getProfileImage.errors"
          :key="index" class="forms__input__error"
        >
        {{ error }}
        </p>
     </div>
    <div class="profile_image__container">
        <FileUpload
           @upload="handleFileUpload"
          :field="getProfileImage.field"
          :type="getProfileImage.type"
          :value="getProfileImage.value"
          :nameAttr="getProfileImage.nameAttr"
          accept="image/*"
          :maxSize="2000000"
          shape="circle"
      />
    </div>
  </div>
</template>

<script>

  import { mapGetters, mapMutations } from 'vuex';

  import FileUpload from '../../forms/inputs/FileUpload.vue';
  import CheckCircleIcon from '../../Icons/ CheckCircleIcon.vue';

  export default {

    name: 'Customize',

    components: {

      FileUpload,
      CheckCircleIcon,
    },

    data () {

      return {

      }
    },

    computed: {
      ...mapGetters('customize',
        [
          'getBackgroundImage',
          'getProfileImage'
        ]
      ),
    },

    methods: {

      ...mapMutations('customize',
        [
          'SET_IMAGE_FIELD',
          'REMOVE_IMAGE',
        ]
      ),

      clearImage(image) {

        this.REMOVE_IMAGE(image);
      },

      handleFileUpload(payload) {

        this.SET_IMAGE_FIELD(payload);
      }
    }
  }

</script>

<style lang="scss">

  .customize__container {
    box-sizing: border-box;
    height: 100%;
    width: 100%;
    h4 {
      font-weight: 200;
      text-align: center;
      font-size: 1.1rem;
      margin: 0.1rem 0;
      margin-bottom: 1rem;
      letter-spacing: -1px;
      color: $themeLightBlue;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      text-shadow: 2px 1px 3px rgba(0,0,0,0.3);
    }
  }

  .background_image__container {
    box-sizing: border-box;
    width: 90%;
    height: 250px;
    padding: 1rem;
    margin: 2rem auto;

  }

  .profile_image__container {
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

  .customize_success__container {

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