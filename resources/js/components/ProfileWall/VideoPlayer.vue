<template>
  <div class="video_player__container">
    <video ref="videoPlayer" controls width="200">
      <source :src="src" type="video/mp4">
    </video>

  </div>
</template>


<script>

  export default {

    name: 'VideoPlayer',

    props: {
      src: String,
    },

    components: {

    },

    data () {

      return {
        duration: null,
      }
    },

    computed: {

    },


    methods: {


    }
  }

</script>

<style lang="scss">

.video_player__container {
    box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
    border-radius: 8px;
    width: 100%;
    height: 100%;
    position: relative;

  video {
     border-radius: 8px;
     width: 100%;
     height: 100%;
  }
}





</style>