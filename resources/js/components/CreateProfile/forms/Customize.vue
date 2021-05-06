<template>
  <div class="customize__container">
    <h4>Customize your profile by uploading pictures</h4>
    <div class="background_image__container">
      <p class="customize__label">{{ getBackgroundImage.label }}:</p>
      <p class="customize__meta_info"><em>*Please keep all files/pictures under 2MB</em></p>
      <FileUpload
        :field="getBackgroundImage.field"
        :type="getBackgroundImage.type"
        :value="getBackgroundImage.value"
        :nameAttr="getBackgroundImage.nameAttr"
        :errors="getBackgroundImage.errors"
        accept="image/*"
      />
    </div>
    <!-- <div class="profile_image__container">

    </div> -->
  </div>
</template>

<script>

  import { mapGetters } from 'vuex';

  import FileUpload from '../../forms/inputs/FileUpload.vue';

  export default {

    name: 'Customize',

    components: {

      FileUpload,
    },

    data () {

      return {

      }
    },

    computed: {
      ...mapGetters('customize',
        [
          'getBackgroundImage'
        ]
      ),
    },

    methods: {

    }
  }

</script>

<style lang="scss">

  // @import '../../../../sass/general/_variables.scss';

  .customize__container {

    h4 {
      font-weight: 200;
      text-align: center;
      font-size: 1.1rem;
      margin: 0.1rem 0;
      letter-spacing: -1px;
      color: $themeLightBlue;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      text-shadow: 2px 1px 3px rgba(0,0,0,0.3);
    }
  }

  .background_image__container {
    box-sizing: border-box;
    height: 250px;
    width: 100%;
    padding: 1rem;
    margin: 0 auto;
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

</style>