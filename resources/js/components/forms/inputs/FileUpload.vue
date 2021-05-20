<template>
  <div class="file_upload__container">
    <p class="forms__input__error" v-if="error.length">{{ error }}</p>
    <div
      @dragenter="handleDrag($event)"
      @dragover.prevent="handleDrag($event)"
      @dragleave.prevent="handleDragLeave($event)"
      @drop.prevent="handleDrop($event)"
      :class="`file_upload__box ${border}`">
      <input
        @change="handleChange($event)"
        class="file_upload"
        :type="type"
        :accept="accept"
        :name="nameAttr"
      />
      <UploadIcon
          v-if="!isDragging"
          className="icon__md__light"
        />
      <PlusPlainIcon
        v-if="isDragging"
        className="icon__md__light"
      />
      <img :class="border" v-if="value.length" :src="value" alt="lines connected by dots" />
    </div>
  </div>
</template>

<script>

  import PlusPlainIcon from '../../Icons/PlusPlainIcon.vue';
  import UploadIcon from '../../Icons/UploadIcon.vue';

  export default {

    name: 'FileUpload',

    props: {
      field: String,
      type: String,
      nameAttr: String,
      value: String,
      accept: String,
      maxSize: Number,
      shape: String,
    },

    components: {

      PlusPlainIcon,
      UploadIcon,
    },

    data () {

      return {
        FileReader: new FileReader(),
        fileChange: false,
        isDragging: false,
        error: '',
        data: {},
      }
    },

    computed: {

      border() {

        return this.shape === 'circle' ? 'upload-circle' : 'upload-square'
      }
    },

    methods: {

      handleDrag (e) {

        if (!this.isDragging) {

          this.isDragging = true;
        }


      },

      handleDragLeave(e) {

        this.isDragging = false;

      },

        readFile (file) {

        const reader = new FileReader();

        reader.onload = (e) => {

          this.data.src = e.target.result;
          this.data.file = file;
          this.data.input = this.nameAttr;

          this.$emit('upload', this.data);
        }

         reader.readAsDataURL(file);
      },

      handleChange(e) {

        this.fileChange = true;

        const file = e.srcElement.files[0];

        if (!file) {

          return;
        }

        this.validateFile(file);

        if (!this.error.length) {

          this.readFile(file);
        }

      },

      handleDrop({ dataTransfer }) {

        this.fileChange = true;

        const file = dataTransfer.files[0];

        if (!file) {

          return;
        }

        this.validateFile(file);

        if (!this.error.length) {

          this.readFile(file);
        }
      },

      validateFile (file) {


        if (file.size > this.maxSize) {

          this.error = 'File size exceeded, please keep it under 2MB';
          return;
        }

        this.error = '';
      },
    },
  }

</script>

