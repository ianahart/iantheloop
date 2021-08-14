<template>
  <div v-if="post.post_comments.length" class="comments_container">
    <Comment
      v-for="post_comment in post.post_comments"
      :key="post_comment.id"
      :postComment="post_comment"
    />
  </div>
</template>

<script>

  import Comment from './Comment.vue';

  export default {

    name: 'Comments',

    props: {
      post: Object,

    },

    components: {
      Comment,
    },

    data () {

      return {
      }
    },

    computed: {

    },

    methods: {

    }
  }
</script>

<style lang="scss">

</style>