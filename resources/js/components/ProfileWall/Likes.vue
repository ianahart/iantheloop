<template>
    <div class="likes__container">
      <div
        v-if="!hasCurUserLiked"
        @click="addLike(data.id)"
        class="thumbs_up__non_active"
      >
        <ThumbsUpIcon
          className="icon__xsm__dark"
        />
       <span>Like</span>
      </div>
      <div
        v-if="hasCurUserLiked"
        @click="removeLike({ postId: data.id, currentUserId })"
        class="thumbs_up__active">
        <transition name="rotate" appear>
          <ThumbsUpSolidIcon
            className="likes_solid__md_icon"
          />
        </transition>
      <span>Like</span>
      </div>
    </div>
</template>

<script>

  import ThumbsUpSolidIcon from '../Icons/ThumbsUpSolidIcon.vue';
  import ThumbsUpIcon from '../Icons/ThumbsUpIcon.vue';

  export default {

    name: 'Likes',

    props: {
     data: Object,
     currentUserId: Number,
    },

    components: {
      ThumbsUpSolidIcon,
      ThumbsUpIcon
    },

    data () {

      return {

      }
    },


    computed : {

      hasCurUserLiked() {

          return this.data.post_likes.some((lk) => lk.user_id === this.currentUserId);
      }

    },

    methods: {

      addLike(id) {

        this.$emit('addlike', id);
      },

      removeLike(post) {

        const like = this.data.post_likes.find((lk) => lk.user_id === post.currentUserId);

        this.$emit('removelike', like);
      }
    },
  }

</script>

<style lang="scss">


.rotate-enter-active, .rotate-leave-active {
  transition: all 0.4s ease-out;
  transform: rotate(-10deg);
}
.rotate-enter ,.rotate-leave-to {
  transform: rotate(50deg);
  opacity: 0;
}

  .likes__container {
    display: flex;
    align-items: center;
    cursor: pointer;

    span {
      font-size: 0.9rem;
    }

  }

  .thumbs_up__non_active {
    cursor: pointer;
    display: flex;
    align-items: center;

    span {
      color: $mainInputLabel;
    }

    svg {
      color: $mainInputLabel;
      margin-right: 0.1rem;
    }
  }

  .thumbs_up__active {
    cursor: pointer;
    display: flex;
    align-items: center;

    span {
      color: $themeLightBlue;
      font-weight: bold;
    }

    svg {
      margin-right: 0.1rem;
      transform: rotate(-10deg);
    }
  }
</style>