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
    </div>
     <div class="single_comment_interactions">
       <p @click="likeComment(postComment)">Like</p>
       <span></span>
       <p @click="replyToComment(postComment)">Reply</p>
       <p>{{ postComment.posted_date }}</p>
     </div>
    </div>
</template>

<script>

  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';

  export default {

    name: 'Comment',

    props: {
      postComment: Object,

    },

    components: {
      DefaultProfileIcon,
    },

    data () {

      return {

      }
    },

    computed: {

    },

    methods: {

      likeComment(comment) {

         console.log('Liking comment: ', comment.id);
      },

      replyToComment(comment) {

        console.log('Replying to comment: ', comment.id);
      },
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

  .single_comment_interactions {
    margin-top: 0.3rem;
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
</style>