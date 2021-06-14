<template>
  <div class="single_comment_container">
    <div class="single_comment_row">
      <div class="comment_profile_picture_container">
        <img
          v-if="postComment.profile_picture"
          :src="postComment.profile_picture"
          :alt="postComment.full_name"
        />
        <DefaultProfileIcon
          v-else
          className="default_profile_image_rel_sm"
        />
      </div>
      <div class="single_comment_body_container">
        <h4>{{ postComment.full_name }}</h4>
        <p>{{ postComment.comment_text }}</p>
      </div>
      <CommentOptions
        @delete="deleteComment"
        :postComment="postComment"
      />
    </div>
     <div class="xs_spacer_top">
       <CommentInteractions
         @like="likeComment"
         @reply="replyToComment"
         :postComment="postComment"
       />
     </div>
    </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import DefaultProfileIcon from '../../Icons/DefaultProfileIcon.vue';
  import CommentInteractions from './CommentInteractions.vue';
  import CommentOptions from './CommentOptions.vue';

  export default {

    name: 'Comment',

    props: {
      postComment: Object,

    },

    components: {
      DefaultProfileIcon,
      CommentInteractions,
      CommentOptions,
    },

    data () {

      return {
        debounceID: '',
      }
    },

    computed: {

    },

    beforeDestroy() {
      clearTimeout(this.debounceID);
    },

    methods: {

      ...mapActions('profileWall',
        [
          'DELETE_COMMENT'
        ]
      ),

      likeComment(comment) {

         console.log('Liking comment: ', comment.id);
      },

      replyToComment(comment) {

        console.log('Replying to comment: ', comment.id);
      },

      async deleteComment(comment) {

        try {

          await this.DELETE_COMMENT(
            {
              commentID: comment.id,
              postID: comment.post_id,
              userID: comment.user_id,
            });

        } catch(e) {

          console.log('Comment.vue: ', e);
        }
      },

      debounce(fn, delay = 400) {
          return ((...args) => {
              clearTimeout(this.debounceID);

              this.debounceID = setTimeout(() => {
                  this.debounceID = null;

                  fn(...args);
              }, delay);
          })();
        }
    },
  }

</script>

<style lang="scss">

  .single_comment_container {
    box-sizing: border-box;
    padding: 0.5rem;
    margin: 1rem 0;
  }

  .single_comment_row {
    display: flex;
  }

  .single_comment_body_container {
    background-color: darken(#fff, 2);
    border-radius: 12px;
    padding: 0.5rem;
    border: 1px solid darken($primaryGray, 1);

    h4 {
      font-weight: 100;
      font-family: 'Secular One', sans-serif;
      margin: 0.1rem 0;
      font-size: 0.85rem;
      color: $primaryBlack;
    }

    p {
      font-size: 0.85rem;
      margin: 0.1rem 0;
      color: lighten($primaryBlack, 2);
    }
  }

  .comment_profile_picture_container {
    margin-right: 0.5rem;
    img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }

    svg {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      color: $themePink;
      background-color: $themeLightBlue;
    }
  }

</style>