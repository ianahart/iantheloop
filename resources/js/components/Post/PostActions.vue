<template>
  <div
    id="postActions"
    class="post_user_actions__container"
  >
    <div
      @click="openFlagModal"
      v-if="currentUserId !== authorUserId && currentUserId !== subjectUserId"
      class="post_user_actions_flag"
    >
      <FlagSolidIcon
        className="flag_solid__icon"
      />
      <p>Flag Post</p>
    </div>
    <div
      @click="deletePost(postId, currentUserId)"
      v-if="currentUserId === authorUserId || currentUserId === subjectUserId"
      class="post_user_actions_delete"
    >
      <div class="post_user_actions_empty_icon"></div>
      <p>Delete</p>
    </div>
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  import FlagSolidIcon from '../Icons/FlagSolidIcon.vue';


  export default {

    name: 'PostActions',

    components: {
      FlagSolidIcon,
    },

    props: {
      subjectUserId: Number,
      authorUserId: Number,
      postId: Number,

    },

    computed: {

      ...mapState('posts',
        [
          'responseError'
        ]
      ),

      ...mapState('profile',
        [
          'currentUserId'
        ]
      ),
    },

    methods: {

      ...mapMutations('posts',
        [
          'OPEN_MODAL'
        ]
      ),

      ...mapActions('posts',
        [
          'DELETE_POST'
        ]
      ),

      openFlagModal () {

        this.OPEN_MODAL({ modal: 'flag_post', activeFlagPostId: this.postId });
      },

      async deletePost(id, currentUserId) {

        await this.DELETE_POST({ id, currentUserId });

        if (this.responseError) {
          this.$router.push({name: 'NotFound'});
        }
      }
    }
  }


</script>

<style lang="scss">

.post_user_actions__container {
  background-color: #FFF;
  border-radius: 8px;
  box-shadow: rgb(0 0 0 / 10%) 0px 20px 25px -5px, rgb(0 0 0 / 4%) 0px 10px 10px -5px;
  box-sizing: border-box;
  padding: 0.3rem;
  position: absolute;
  width: 110px;
  top: 1px;
  right: 12px;

  p {
    font-size: 0.75rem;
    color: lighten($primaryBlack, 5);
    margin: 0.2rem;
    pointer-events: none;
  }
}

.post_user_actions_flag {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  cursor: pointer;

  svg {
    pointer-events: none;
  }
}

.post_user_actions_delete {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  cursor: pointer;
}

.post_user_actions_empty_icon {
  width: 20px;
  height: 20px;
}

</style>