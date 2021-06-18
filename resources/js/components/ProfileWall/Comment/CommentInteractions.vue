<template>
  <div class="single_comment_interactions">
    <p v-if="!hasCurUserLiked" @click="emitLike(comment)">Like</p>
    <p @click="emitUnlike(comment)" id="unlike_post_comment" v-if="hasCurUserLiked">Unlike</p>
    <span></span>
    <p v-if="commentType === 'comment'" @click="emitReplyPopup">Reply</p>
    <p>{{ comment.posted_date }}</p>
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'CommentInteractions',

    props: {
      comment: Object,
      currentUserId: Number,
      commentType: String,
    },

    computed: {
      hasCurUserLiked () {

          return this.comment.comment_likes.some((like) => like.user_id === this.currentUserId);

      },
    },

    methods: {

      emitLike(comment) {

        this.$emit('like', comment);
      },

      emitUnlike(comment) {
        const commentLike = comment.comment_likes.find((like) => like.user_id === this.currentUserId);
        commentLike.post_id = comment.post_id;
        commentLike.action = 'unlike';
        commentLike.type = comment.reply_to_comment_id === null ? 'comment' : 'reply';

        this.$emit('unlike', commentLike);
      },

      emitReplyPopup() {
        this.$emit('replypopup');
      }
    }
  }

</script>

<style lang="scss">
  .single_comment_interactions {
    display: flex;
    align-items: center;

    p {
      margin: 0.1rem 0.3rem;
      color: $themeLightBlue;
      font-size: 0.75rem;
      cursor: pointer;
      transition: all 0.25s ease-out;

      &:hover {
        color: darken($themeBlue, 5);
      }
      &:last-of-type {
        color: gray;
        font-size: 0.6rem;
      }
    }

    span {
      background-color: darken($primaryBlack, 5);
      height: 3px;
      width: 3px;
      border-radius: 50%;
    }
  }

  #unlike_post_comment {
      margin: 0.1rem 0.3rem;
      color: $themeRoyalBlue;
      font-weight: 800;
      font-size: 0.75rem;
      cursor: pointer;
      transition: all 0.25s ease-out;
  }
</style>