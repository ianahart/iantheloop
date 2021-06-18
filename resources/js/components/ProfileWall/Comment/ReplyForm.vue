<template>
   <div v-if="isCommentFormOpen">

    <div class="post_comment_form_container">
          <img
            id="reply_comment_profile_pic"
            v-if="currentUserProfilePic"
            :src="currentUserProfilePic"
          />
          <DefaultProfileIcon
            v-else
            className="default_profile_image_rel_xs"
          />
          <input
            ref="input"
            v-model="input"
            type="text"
            :placeholder="`Replying to ${replyTo}...`"
          />
          <button @click="emitReplyTo">Reply</button>
    </div>
  </div>
</template>

<script>

  import DefaultProfileIcon from '../../Icons/DefaultProfileIcon.vue';

  export default {

    name: 'ReplyForm',

    components: {
      DefaultProfileIcon,
    },

    props: {
      isCommentFormOpen: Boolean,
      currentUserProfilePic: String,
      replyTo: String,
      replyError: Object,
      requestFinished: Boolean,
    },

    data () {

      return {
        input: '',
      }
    },

    watch: {

      requestFinished: function () {
        if (this.requestFinished && !this.replyError) {
            this.input = '';
        }
      }
    },

    computed: {

    },

    methods: {
      emitReplyTo() {
        this.$emit('replyto', this.$refs.input.value);
      },
    }
  }

</script>

<style lang="scss">

#reply_comment_profile_pic {
  width: 30px;
  height: 30px;
}
</style>