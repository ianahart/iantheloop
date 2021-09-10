<template>
  <div :style="durationBarStyle" :class="`story_duration_bar ${this.durationBarClass}`"></div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {
    name: 'DurationBar',
    props: {
     userId: Number,
     durationBar: Object,
     activeDurationBar: Number,
    },

    data () {
      return {
        durationID: '',
        intervalID: '',
        barWidth: window.innerWidth <= 600 ? 40 : 80,
        windowInnerWidth: window.innerWidth,
      }
    },

    mounted() {
      clearInterval(this.intervalID);
      clearTimeout(this.durationID);
      window.addEventListener('resize', this.adjustWidth);

      if (this.isActive) {
        this.setDuration();
      }
    },

    beforeDestroy() {
      clearTimeout(this.durationID);
      clearInterval(this.intervalID);
      window.removeEventListener('resize', this.adjustWidth);
    },

    watch: {

      activeDurationBar() {
          clearInterval(this.intervalID);
          clearTimeout(this.durationID);

          if (this.isActive) {
            this.setDuration();
          } else {
            this.barWidth = window.innerWidth <= 600 ? 40 : 80;
          }
      }
    },

    computed: {
       durationBarClass() {
         return this.isActive ? 'active_duration_bar' : 'unactive_duration_bar';
       },

       durationBarStyle() {
          return this.isActive ? { width: this.barWidth.toString() + 'px' } : { width: this.isMobile ? '40px' : '80px' };
       },

       isActive() {
         return this.activeDurationBar === parseInt(this.durationBar.id);
       },

       isMobile() {
         return this.windowInnerWidth <= 600 ? true :false;
       },

       getPixels() {
         if (!this.isActive) {
            return;
          }
        let pixels;

        if (this.durationBar.duration === '5000') {
          pixels = this.calculatePixels(80, 5);
        } else if(this.durationBar.duration === '10000') {
          pixels = this.calculatePixels(80, 10);
        } else if (this.durationBar.duration === '15000') {
          pixels = this.calculatePixels(80, 15);
        }

        return pixels;
       },

    },
    methods: {

      calculatePixels(pixels, duration) {
        return this.isMobile ? Math.floor(Math.floor(pixels / duration) / 2) : Math.floor(pixels / duration);
      },

      adjustWidth(e) {
        this.windowInnerWidth = e. target.innerWidth;
        this.barWidth = this.isMobile ? 40 : 80;
      },

       setDuration() {
         this.barWidth = this.barWidth - this.getPixels;

         this.intervalID = setInterval(() => {
            this.barWidth = this.barWidth - this.getPixels;
         }, 1000);

         this.durationID = setTimeout(() => {
           this.$emit('animate', { story: this.durationBar });

         }, parseInt(this.durationBar.duration));
       },
    },
  }
</script>

<style lang="scss">
  .story_duration_bar {
    box-sizing: border-box;
    height: 3px;
    background-color: #fff;
    transition: width 1s linear;
  }

  .active_duration_bar {
    transform: scaleY(1.8);
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
  }

  .unactive_duration_bar {
    transform: scale(1);
  }

  @media(max-width:600px) {
    .unactive_duration_bar {
      min-width: 40px;
      width: 40px;
    }
  }
</style>
