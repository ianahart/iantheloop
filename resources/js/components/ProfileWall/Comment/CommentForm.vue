<template>
  <div v-if="isCommentFormOpen">
     <div
      v-if="localError.length || commentErrors.length"
      class="post_comment_error">
      <p
        class="post_comment_error"
        v-if="localError">
        {{ localError }}
      </p>
      <p v-else-if="commentErrors[0].post_id === post.id">{{ commentErrors[0].message }}</p>
    </div>
    <div class="post_comment_form_container">
          <img
            v-if="currentUserProfilePic"
            :src="currentUserProfilePic"
          />
          <DefaultProfileIcon
            v-else
            className="default_profile_image_rel_sm"
          />
          <input
            ref="input"
            v-model="input"
            type="text"
            placeholder="Write a Comment..."
          />
          <button @click="addComment">Post</button>
    </div>
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';
  import DefaultProfileIcon from '../../Icons/DefaultProfileIcon.vue';

  export default {

    name: 'CommentForm',

    props: {
      post: Object,
      currentUserProfilePic: String,
      isCommentFormOpen: Boolean,
      userId: Number,
    },

    components: {
     DefaultProfileIcon,
    },

    data () {

      return {
        input: '',
        localError: '',
        debounceID: '',
        finished: false,
      }
    },

    beforeDestroy() {

      clearTimeout(this.debounceID);
    },

    computed: {
      ...mapState('profileWall',
        [
          'postErrors',
          'commentErrors',
          'requestFinished',
        ]
      ),
    },

    methods: {
      ...mapMutations('profileWall',
        [
          'RESET_COMMENT_ERRORS',
          'SET_REQUEST_FINISHED',
        ]
      ),
      ...mapActions('profileWall',
        [
          'ADD_COMMENT',
        ]
      ),

        debounce(fn, delay = 400) {
          return ((...args) => {
              clearTimeout(this.debounceID);

              this.debounceID = setTimeout(() => {
                  this.debounceID = null;

                  fn(...args);
              }, delay);
          })();
        },

      async addComment() {
        this.localError = '';
        this.RESET_COMMENT_ERRORS();

        if (this.input.trim().length <= 0) {

          this.localError = 'A comment cannot be empty';
          return;
        }
        if (this.input.trim().length >=250) {

          this.localError = 'A comment must be under 250 characters';
          return;
        }

        this.debounce(async () => {
          await this.ADD_COMMENT(
              {
                input: this.$refs.input.value,
                post_id: this.post.id,
                user_id: this.userId,
              });
          if (this.requestFinished) {
            if (!this.commentErrors.length) {
                this.input = '';
                this.SET_REQUEST_FINISHED(false);
                this.$emit('closeform', false);
            }
          }
        }, 400);


      }
    },
  }
</script>

<style lang="scss">

</style>