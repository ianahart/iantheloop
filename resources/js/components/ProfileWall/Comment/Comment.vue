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
        <p v-if="postComment.likes > 0" id="post_comment_likes">{{ postComment.likes }}</p>
        <div v-if="postComment.likes > 0" class="post_comment_likes_icon_container ">
            <ThumbsUpSolidIcon />
        </div>
      </div>
      <CommentOptions
        @delete="deleteComment"
        :comment="postComment"
      />
    </div>
     <div class="xs_spacer_top">
       <CommentInteractions
         @like="likeComment"
         @unlike="unlikeComment"
         @replypopup="replyToCommentPopup"
         :postComment="postComment"
         :currentUserId="getUserId"
       />
     </div>
     <div class="xs_spacer_top">
       <div class="comment_reply_errors xs_spacer_bottom">
         <p v-if="replyError !== undefined">{{ replyError.error }}</p>
       </div>
       <ReplyForm
        @replyto="replyToComment"
        :currentUserProfilePic="getProfilePic"
        :isCommentFormOpen="isCommentFormOpen"
        :replyTo="postComment.full_name"
        :replyError="replyError"
        :requestFinished="requestFinished"
       />
     </div>
    <div v-if="shouldReplyThreadStart" class="start_reply_thread">
       <p @click="toggleReplyThread">
         <strong>({{ postComment.reply_comments_count === 1 ? 1 :  postComment.reply_comments_count - 1  }})
         </strong>
         {{ postComment.reply_comments_count === 1 ? 'Reply...' : 'Replies...' }}
       </p>
        <ReplyComment
          :replyComment="firstReplyComment"
          :currentUserId="getUserId"
        />
     </div>
     <div v-if="!shouldReplyThreadStart"  class="xs_spacer_top post_reply_comments_wrapper">
       <ReplyComment
          v-for="replyComment in postComment.reply_comments"
          :key="replyComment.id"
          :replyComment="replyComment"
          :currentUserId="getUserId"
        />
     </div>
     <div class="sm_spacer">
         <p
          v-if="!replyThreadStart && postComment.reply_comments.length !== postComment.reply_comments_count"
          @click="loadMoreReplies"
          class="load_more_replies"
          >
            Keep Reading Replies...
         </p>
     </div>
    </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import DefaultProfileIcon from '../../Icons/DefaultProfileIcon.vue';
  import CommentInteractions from './CommentInteractions.vue';
  import CommentOptions from './CommentOptions.vue';
  import ThumbsUpSolidIcon from '../../Icons/ThumbsUpSolidIcon.vue';
  import ReplyForm from './ReplyForm.vue';
  import ReplyComment from './ReplyComment.vue';

  export default {

    name: 'Comment',

    props: {
      postComment: Object,


    },

    components: {
      DefaultProfileIcon,
      CommentInteractions,
      CommentOptions,
      ThumbsUpSolidIcon,
      ReplyForm,
      ReplyComment,
    },

    data () {

      return {
        debounceID: '',
        isCommentFormOpen: false,
        replyThreadStart: true,
      }
    },

    computed: {
      ...mapState('profileWall',
        [
          'replyErrors',
          'requestFinished',
        ]
      ),

      firstReplyComment() {

        if (this.postComment.reply_comments.length && Object.keys(this.postComment).includes('reply_comments')) {

          return this.postComment.reply_comments.slice(0, 1)[0];
        }
        return undefined;
      },

      shouldReplyThreadStart () {
        return this.replyThreadStart && this.postComment.reply_comments.length;
      },

      replyCommentsExist() {

        return Object.keys(this.postComment).includes('reply_comments') && this.postComment.reply_comments.length;
      },

      replyError () {
        let error;

        if (this.replyErrors.length) {

          error = this.replyErrors.find((errorObj) => errorObj.commentId === this.postComment.id);

          return error ? error : undefined;
        }
        return undefined;
      },

      ...mapGetters('user',
        [
          'getUserId',
          'userName',
          'getProfilePic',
        ]
      ),
    },

    beforeDestroy() {
      clearTimeout(this.debounceID);
    },

    methods: {

      ...mapMutations('profileWall',
        [
          'SET_REPLY_ERRORS',
          'SET_REQUEST_FINISHED',

        ]
      ),

      ...mapActions('profileWall',
        [
          'DELETE_COMMENT',
          'LIKE_COMMENT',
          'UNLIKE_COMMENT',
          'ADD_REPLY_COMMENT',
          'REFILL_REPLIES',
        ]
      ),

      loadMoreReplies () {
        const comment = this.postComment.reply_comments[this.postComment.reply_comments.length - 1];

        const lastReplyComment = {
          post_id: this.postComment.post_id,
          comment_id: this.postComment.id,
          last_reply_comment_id: comment.id,
        };

        this.debounce(async() => {
          await this.REFILL_REPLIES(lastReplyComment);
        }, 300);
      },

      toggleReplyThread() {
        this.replyThreadStart = !this.replyThreadStart;
      },

      validateReply(commentText, commentId) {
        let error;
        if (commentText.trim().length === 0) {
          error = 'Sorry, cannot reply with an empty message';
        }

        if (commentText.trim().length > 250) {
          error = 'Please keep messages under 250 characters';
        }
        if (error) {
          this.SET_REPLY_ERRORS({ error, commentId, action: 'set' });
          return;
        }

      },

      replyToComment (commentText) {
          this.SET_REPLY_ERRORS({ error: null, commentId: this.postComment.id, action: 'unset' })
          this.validateReply(commentText, this.postComment.id);

          if (this.replyError) {
            return;
          }
        try {

          const replyComment = {
              reply_to_comment_id: this.postComment.id,
              user_id: this.getUserId,
              post_id: this.postComment.post_id,
              input: commentText,
            };

          this.debounce(async() => {
            await this.ADD_REPLY_COMMENT(replyComment);
            if (this.requestFinished) {

              if (!this.replyError) {
                this.SET_REQUEST_FINISHED(false);
                this.isCommentFormOpen = false;

              } else {
                this.SET_REQUEST_FINISHED(false);
              }
            }
          }, 350);

        } catch(e) {

        }
      },

      unlikeComment(commentLike) {
          try {
              this.debounce(async() => {
                 await this.UNLIKE_COMMENT(commentLike);
         }, 300);
        } catch(e) {

        }
      },

      likeComment(comment) {
        try {
              this.debounce(async() => {
                 await this.LIKE_COMMENT(
                  {
                    user_id: this.getUserId,
                    liked_by: this.userName.toLowerCase(),
                    comment_id: comment.id,
                    post_id: comment.post_id,
                    action: 'like',
                  });
         }, 300);
        } catch(e) {

        }
      },


      replyToCommentPopup() {


        this.isCommentFormOpen = !this.isCommentFormOpen;
      },

      async deleteComment(comment) {

        try {

          await this.DELETE_COMMENT(
            {
              commentID: comment.id,
              postID: comment.post_id,
              userID: comment.user_id,
              type: 'comment',
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
    position: relative;
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

  .post_comment_likes_icon_container {
    position: absolute;
    bottom: -6px;
    right: -2px;
    height: 17px;
    width: 17px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    background-color: $themeRoyalBlue;
    margin-right: 0.1rem;

    svg {
        color: #fff;
        height: 12px;
        width: 12px;
    }
  }

  #post_comment_likes {
    position: absolute;
    right: 15px;
    bottom: -6px;
    font-size: 0.85rem;
    color: $themeRoyalBlue;
    margin: 0 0.2rem;
  }

  .comment_reply_errors {
    color: $error;
    font-size: 0.8rem;
    margin: 0.1rem;
  }

  .post_reply_comments_wrapper {
    padding: 0.5rem;
    box-sizing: border-box;
    margin-left: 1.75rem;
  }

  .start_reply_thread {
    p:first-of-type {
      font-size: 0.75rem;
    }
    p {
      cursor: pointer;
      transition: all 0.25s ease-in-out;
      color: gray;
      font-size:0.65rem;
      margin: 0.1rem 0;

      &:hover {
        color: darken(gray, 5);
      }
    }
  }
  .load_more_replies {
    color: gray;
    font-size: 0.8rem;
    transition: all 0.25s ease-in-out;
    margin-left: 3.5rem;
    cursor: pointer;
    &:hover {
      color: darken(gray, 5);
    }
  }


</style>