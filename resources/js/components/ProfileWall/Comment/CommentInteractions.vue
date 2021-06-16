<template>
  <div class="single_comment_interactions">
    <p v-if="!hasCurUserLiked" @click="emitLike(postComment)">Like</p>
    <p @click="emitUnlike(postComment)" id="unlike_post_comment" v-if="hasCurUserLiked">Unlike</p>
    <span></span>
    <p @click="emitReply(postComment)">Reply</p>
    <p>{{ postComment.posted_date }}</p>
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'CommentInteractions',

    props: {
      postComment: Object,
      currentUserId: Number,
    },

    computed: {
      hasCurUserLiked () {
        return this.postComment.comment_likes.some((like) => like.user_id === this.currentUserId);
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
        this.$emit('unlike', commentLike);
      },

      emitReply(comment) {
        this.$emit('reply', comment);
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