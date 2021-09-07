<template>
  <div
    @click="emitSelected"
    :style="`background: ${backgroundGradient}`"
    class="story_form_background_option"
    ></div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import { randomGradient } from '../../data/randomGradientGenerator.js';

  export default {
    name: 'BackgroundOption',
    props: {

    },

    computed: {
      backgroundGradient() {
      //  <div :style="`background: ${backgroundGradient}`" class="create_story_showcase_options">
        return `${randomGradient()}`;
      },
    },

    methods:{
      emitSelected() {
        this.$emit('selected', this.backgroundGradient);
      }
    }
  }
</script>

<style lang="scss">
  .story_form_background_option {
    box-sizing: border-box;
    height: 24px;
    width: 24px;
    border-radius: 50%;
    border: 2px solid transparent;
    justify-self: center;
    padding: 0.5rem;
    cursor: pointer;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    transition: all 0.35s ease-in-out;
    &:hover {
      transform: scale(1.1);
      border-color: $themePink;
    }
  }
</style>