<template>
  <transition name="flag-post-modal" appear>
    <div v-if="modalIsOpen && activeModal === 'flag_post' && post.id === activeFlagPostId" class="flag_post_options__container">
      <div
        @click="exitModal"
        class="flag_post__exit">
        <CloseIcon
          className="icon__md__light"
        />
      </div>
      <div class="flagged_post_divider"></div>
      <div class="flagged_post__options_instructions">
        <p>Please select from below the reason(s) you are reporting this post</p>
        <p id="already_flagged_error" v-if="alreadyFlaggedError.length">{{ alreadyFlaggedError }}</p>
      </div>
      <div class="flagged_post_divider"></div>
      <div class="flag_post__options">
        <FlaggedOption
          v-for="unselectedFlaggedOption in unselectedFlaggedOptions"
          :key="unselectedFlaggedOption.id"
          :option="unselectedFlaggedOption"
          @selection="addOption"
        />
      </div>
      <div class="flagged_post_divider"></div>
      <div class="flag_post__options_selected">
        <FlaggedOption
          v-for="selectedFlaggedOption in selectedFlaggedOptions"
          :key="selectedFlaggedOption.id"
          :option="selectedFlaggedOption"
          @deselection="removeOption"
        />
      </div>
      <div v-if="selectedFlaggedOptions.length" class="flag_post_options_btn_container">
        <button @click="flagPost(post.id)">Report</button>
      </div>
    </div>
  </transition>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import FlaggedOption from './FlaggedOption.vue';
  import CloseIcon from '../Icons/CloseIcon.vue'

  export default {

    name: 'FlaggedOptions',

    props: {
      post: Object,
    },

    components: {
      FlaggedOption,
      CloseIcon,
    },

    computed: {
      ...mapGetters('posts',
        [
          'selectedFlaggedOptions',
          'unselectedFlaggedOptions',
        ]
      ),

      ...mapState('posts',
        [
          'modalIsOpen',
          'activeModal',
          'activeFlagPostId',
          'flaggedOptions',
          'alreadyFlaggedError',
        ]
      ),
    },

    methods: {

      ...mapMutations('posts',
        [
          'CLOSE_MODAL',
          'SET_FLAGGED_OPTION',
          'RESET_FLAGGED_OPTIONS',
        ]
      ),

      addOption(payload) {

          this.SET_FLAGGED_OPTION({ option: payload, action: 'selected' });
      },

      removeOption(payload) {

        this.SET_FLAGGED_OPTION({ option: payload, action: 'deselected' });
      },

      flagPost(postId) {
        this.$emit('flagpost', postId);
      },

      exitModal() {
        this.RESET_FLAGGED_OPTIONS();
        this.CLOSE_MODAL();
      },
    },
  }
</script>

<style lang="scss">


.flag-post-modal-enter-active, .flag-post-modal-leave-active {
  transition: all 0.4s ease-out;
}
.flag-post-modal-enter ,.flag-post-modal-leave-to {
  transform: translate(50px, 100px);
  opacity: 0;
}

.flag_post_options__container {
  background-color: lighten($primaryBlack, 1);
  box-sizing: border-box;
  border-radius: 8px;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
  position: absolute;
  padding: 0.5rem;
  width: 100%;
  top:0;
  left:0;
  z-index: 16;
}

.flagged_post__options_instructions {

  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;

  p {
    margin: 0.1rem;
    font-size: 0.85rem;
    font-family: 'Open Sans', sans-serif;
    color: darken(#fff, 5);
  }
}

#already_flagged_error {
    margin: 0.1rem;
    font-size: 0.85rem;
    font-family: 'Open Sans', sans-serif;
    color: $error;
}

.flagged_post_divider {
  width: 100%;
  border-bottom: 1px solid gray;
  margin: 0.2rem 0;
}

.flag_post__exit {
  width: 100px;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  margin-left: auto;
}

.flag_post_options_btn_container {
  margin: 1.8rem auto 1rem auto;
  display: flex;
  justify-content: flex-end;

  button {

    cursor: pointer;
    color: #fff;
    background-color: $themeBlue;
    transition: all 0.4s ease-out;
    border-radius: 8px;
    padding: 0.3rem 0.4rem;
    border: none;
    width: 150px;
    height: 35px;

    &:hover {
      background-color: lighten($themeBlue, 8);
    }
  }
}


</style>