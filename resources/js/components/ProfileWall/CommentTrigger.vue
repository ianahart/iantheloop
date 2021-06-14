<template>
  <div
    class="comment_trigger__container"
    @click="toggleVisibility"
  >
    <CommentIcon
      className="icon__xsm__dark"
    />
    <p>Comment</p>
  </div>
</template>

<script>

  import CommentIcon from '../Icons/CommentIcon.vue';

  export default {

    name: 'CommentTrigger',

    props: {

    },

    components: {

      CommentIcon,
    },

    computed: {

    },

    methods: {

      toggleVisibility () {
        this.$emit('toggle');
      }
    }
  }
</script>

<style lang="scss">

.comment_trigger__container {
  display: flex;
  align-items: center;
  cursor: pointer;

  svg {
    color: $mainInputLabel;
  }

  p {
    font-size: 0.9rem;
    color: $mainInputLabel;
    margin-left: 0.1rem;
  }
}

</style>