<template>
  <div
    v-if="getUserId === comment.user_id || getUserId === getProfileOwner || getUserId === postAuthorUserId && postsOrigin ===  'newsfeed'"
    @click="togglePopup"
    class="comment_options_container"
  >
    <HorizontalDotsIcon
      className="horizontal_dots__icon"
      marker="commentOptionsTrigger"
    />
    <div
     v-if="commentOptionsOpen"
      class="comment_options_pop_up"
    >
      <p
        v-if="commentOptionsOpen"
        @click="emitDelete(comment)"
        ref="btn"
      >
        Delete
      </p>
    </div>
  </div>
</template>

<script>
  import { mapsState, mapGetters, mapState } from 'vuex';

  import HorizontalDotsIcon from '../Icons/HorizontalDotsIcon.vue';

  export default {

    name: 'CommentOptions',

    props: {
      comment: Object,
      postAuthorUserId: Number,
    },

    components: {
      HorizontalDotsIcon,
    },

    computed: {
      ...mapState('posts',
        [
          'postsOrigin'
        ]
      ),
      ...mapGetters('profile',
        [
          'getProfileOwner'
        ]
      ),
      ...mapGetters('user',
        [
          'getUserId',
        ]
      ),
    },

    data () {
      return {
        commentOptionsOpen: false,
      }
    },
    mounted() {
      window.addEventListener('click', this.clickAway);
    },

    beforeDestroy() {
      window.removeEventListener('click', this.clickAway);
    },

    methods: {

      emitDelete(comment) {

        this.$emit('delete', comment);
      },

      togglePopup(e) {
        if (e.target.id === 'commentOptionsTrigger' && this.commentOptionsOpen) {
          this.commentOptionsOpen = !this.commentOptionsOpen;
          return;
        }
        this.commentOptionsOpen = true;
      },

      clickAway(e) {
        if (this.commentOptionsOpen && this.$refs.btn) {
          if (e.target.id !== 'commentOptionsTrigger' && e.target !== this.$refs.btn.parentElement) {
                this.commentOptionsOpen = false;
          }
        }
      },
    },
  }

</script>

<style lang="scss">

.comment_options_container {
  margin-left: 0.2rem;
  cursor: pointer;
  position: relative;
}

.comment_options_pop_up {
  position: absolute;
  background-color: darken($primaryGray, 5);
  top:18px;
  border-radius: 8px;
  box-shadow: rgb(0 0 0 / 10%) 0px 20px 25px -5px,
        rgb(0 0 0 / 4%) 0px 10px 10px -5px;
  left: -20px;
  width: 60px;
  box-sizing: border-box;
  padding: 0.5rem;
  transition: all 0.25s ease-out;

  &:hover {
    background-color: darken($primaryGray, 2);
  }

  p {
    color: $mainInputLabel;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.25s ease-out;
    margin: 0.1rem;

    &:hover {
      color: lighten($mainInputLabel, 5);
    }
  }
}

</style>