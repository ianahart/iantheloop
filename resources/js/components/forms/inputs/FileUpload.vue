<template>
  <!-- <div class="file_upload__container">
    <input
      class="file_upload"
      :value="value"
      :type="type"
      :accept="accept"
      :name="nameAttr"
    />

  </div> -->
    <UploadIcon />
</template>

<script>

  import UploadIcon from '../../Icons/UploadIcon.vue';

  export default {

    name: 'FileUpload',

    props: {
      field: String,
      type: String,
      nameAttr: String,
      errors: Array,
      value: String,
      accept: String,
    },

    components: {

      UploadIcon,
    },

    data () {

      return {

      }
    },

    methods: {

    },
  }

</script>

<style lang="scss">

  .file_upload__container {
    border-radius: 8px;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    position: relative;
    width:100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
  }

  .file_upload {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    opacity: 0;
    cursor: pointer;
  }

</style>