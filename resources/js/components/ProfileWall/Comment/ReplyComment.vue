<template>
  <div class="post_reply_comment_container">
    <div class="post_reply_comment_row">
      <div class="post_reply_comment_profile_container">
        <img
          class="profile_image_rel_xs"
          v-if="replyComment.profile_picture"
          :src="replyComment.profile_picture"
          :alt="replyComment.full_name"
        />
        <DefaultProfileIcon
        v-else
        className="default_profile_image_rel_xs"
        />
      </div>
      <div class="post_reply_comment_body">
        <h4>{{ replyComment.full_name }}</h4>
        <p>{{ replyComment.comment_text }}</p>

         <p v-if="replyComment.likes > 0" id="post_reply_comment_likes">{{ replyComment.likes }}</p>
        <div v-if="replyComment.likes > 0" class="reply_comment_thumbs_icon">
            <ThumbsUpSolidIcon />
        </div>


      </div>
      <div class="reply_comment_options">
        <CommentOptions
          :comment="replyComment"
          @delete="deleteReplyComment"
        />
      </div>
    </div>
        <CommentInteractions
         @like="likeReplyComment"
         @unlike="unlikeReplyComment"
         :comment="replyComment"
         :currentUserId="currentUserId"
         commentType="reply"
       />
  </div>
</template>


<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import DefaultProfileIcon from '../../Icons/DefaultProfileIcon.vue';
  import HorizontalDotsIcon from '../../Icons/HorizontalDotsIcon.vue'
  import CommentOptions from './CommentOptions.vue';
  import CommentInteractions from './CommentInteractions.vue';
  import ThumbsUpSolidIcon from '../../Icons/ThumbsUpSolidIcon.vue';

  export default {

    name: 'ReplyComment',

    props: {
      replyComment: Object,
      currentUserId: Number,
      originalComment: Number,
      currentUserName: String,
    },

    components: {
      DefaultProfileIcon,
      HorizontalDotsIcon,
      CommentOptions,
      CommentInteractions,
      ThumbsUpSolidIcon,
    },


    computed: {

    },

    methods: {

      ...mapActions('profileWall',
        [
          'DELETE_REPLY_COMMENT',
          'LIKE_COMMENT',
          'UNLIKE_COMMENT',
        ]
      ),

      likeReplyComment(comment) {
        try {
              this.debounce(async() => {
                 await this.LIKE_COMMENT(
                  {
                    user_id: this.currentUserId,
                    liked_by: this.currentUserName.toLowerCase(),
                    comment_id: comment.id,
                    post_id: comment.post_id,
                    type: 'reply',
                    action: 'like',
                    parent_id: comment.reply_to_comment_id,
                  });
         }, 300);
        } catch(e) {

        }
      },


            unlikeReplyComment(comment) {
        try {

              this.debounce(async() => {
                 await this.UNLIKE_COMMENT(
                  {
                    user_id: this.currentUserId,
                    id: comment.id,
                    parent_id: this.originalComment,
                    comment_id: comment.comment_id,
                    post_id: comment.post_id,
                    type: 'reply',
                    action: 'unlike',

                  });
         }, 300);
        } catch(e) {

        }
      },

      async deleteReplyComment(data)  {


        const replyComment = {
             commentID: data.id,
              replyID: data.reply_to_comment_id,
              postID: data.post_id,
              userID: data.user_id,
              type: 'reply_comment',
        }
        await this.DELETE_REPLY_COMMENT(replyComment);
      },

      debounce(fn, delay = 400) {
        return ((...args) => {
          clearTimeout(this.debounceID);

          this.debounceID = setTimeout(() => {
              this.debounceID = null;

              fn(...args);
          }, delay);
        })();
      },

    }
  }

</script>

<style lang="scss">

.post_reply_comment_container {
  margin: 0rem 0 0rem 3.5rem;
  position: relative;
}

.reply_comment_posted_date {

  display: flex;
  justify-content: space-between;
  padding-left: 0.5rem;
  padding-top: 0.5rem;

  p {
    color: gray;
    font-size: 0.65rem;
    margin: 0.1rem 0;
  }
}

.post_reply_comment_row {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  padding-top: 0;
}

.post_reply_comment_body {
  position: relative;
  box-sizing: border-box;
  background-color: darken(#fff, 2);
  border-radius: 12px;
  padding: 0.5rem;
  border: 1px solid darken($primaryGray, 1);
  margin-right: 0.3rem;

  h4 {
    font-family: 'Secular One', sans-serif;
    font-weight: 100;
    margin: 0.1rem 0;
    font-size: 0.85rem;
    color: $primaryBlack;
  }

  p {
    color: lighten($primaryBlack, 3);
    font-size: 0.85rem;
    margin: 0.1rem 0;
  }
}

.reply_comment_thumbs_icon {
  position: absolute;
  right: -4px;
  bottom: -10px;

  svg {
    background-color: darken($themeRoyalBlue, 5);
    color: #fff;
    height: 12px;
    width: 12px;
    border-radius: 50%;
    padding: 0.1rem;
  }
}

#post_reply_comment_likes {
  position: absolute;
  right: 12px;
  bottom: -5px;
  color: darken($themeRoyalBlue, 5);
}

.post_reply_comment_profile_container {
  margin-right: 0.5rem;
  box-sizing: border-box;

  svg {
    color: $themePink;
    background-color: $themeLightBlue;

    path {
      pointer-events: none;
    }
  }
}

.reply_comment_options {
  position: relative;
  align-self: flex-start;
}
</style>