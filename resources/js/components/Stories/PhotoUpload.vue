<template>
  <div class="story_photo_upload_container">
    <input
        name="photo"
        @change="photoFileChange"
        accept="image/*"
        type="file"
      />
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {
    name: 'PhotoUpload',
    props: {
      photoError: String,
    },

    computed: {

    },

    data() {
      return {
        MAX_SIZE: 2000000,
      }
    },

    methods: {
      photoFileChange(e) {
        this.fileChange = true;

        const file = e.srcElement.files[0];
        if (!file) {
          return;
        }

        this.validateFile(file);

          this.readFile(file);
      },

      readFile (file) {

        const reader = new FileReader();

        reader.onload = (e) => {

          const upload = {
            src: e.target.result,
            file,
            field: 'file',
          }
          if (!this.photoError.trim().length) {
            this.$emit('upload', upload);
          }
        }

         reader.readAsDataURL(file);
      },


      validateFile (file) {
        if (file.size > this.MAX_SIZE) {
          this.$emit('error', 'File size exceeded, please keep it under 2MB');
          return;
        }
        this.$emit('error', '');
      },
    },
  }
</script>

<style lang="scss">
  .story_photo_upload_container {
    position: absolute;
    box-sizing: border-box;
    cursor: pointer;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      opacity: 0;
      z-index: 1;

    input {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      opacity: 0;
      z-index: 2;
    }
  }
</style>