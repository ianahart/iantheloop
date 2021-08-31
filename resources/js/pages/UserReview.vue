<template>
  <div v-if="currentUserReview.length && currentUserReviewLoaded" class="user_review_container">
    <header>
      <h2>Your Current Review</h2>
    </header>
    <div class="user_review_background">
        <div class="user_review_actions_container">
          <button
            @click="back"
            v-if="currentView === 'form'"
            >Your Review</button>
          <button
            v-if="currentView === 'review'"
            @click="edit"
            class="user_review_edit_btn
            ">
            <PenSolidIcon />
            Edit
          </button>
          <button
            @click="remove"
            class="user_review_delete_btn"
            >
            <TrashCanIcon />
            Delete
          </button>
        </div>
      <div class="user_review_column">
        <transition name="left-fade" appear>
          <div v-if="currentView === 'review'" class="current_user_review">
            <Review
              v-for="review in currentUserReview" :key="review.id"
              :review="review"
            />
          </div>
        </transition>
          <transition name="right-fade" appear>
            <div v-if="currentView === 'form'" class="current_user_edit_form">
              <Form
                 type="update"
              />
            </div>
          </transition>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import Form from '../components/Reviews/Form.vue';
  import Review from '../components/Reviews/Review.vue';
  import PenSolidIcon from '../components/Icons/PenSolidIcon.vue';
  import TrashCanIcon from '../components/Icons/TrashCanIcon.vue';

  export default {

    name: 'UserReview',
    components: {
      Form,
      Review,
      PenSolidIcon,
      TrashCanIcon,
    },
    data() {
      return {

      }
    },

    async created() {
      await this.RETRIEVE_REVIEW();
    },

    beforeDestroy() {
      this.RESET_REVIEW_MODULE();
    },

    computed: {
      ...mapState('reviews',
        [
          'currentUserReview',
          'currentUserReviewLoaded',
          'currentView',
        ]
      ),
    },

    methods: {
      ...mapMutations('reviews',
        [
          'RESET_REVIEW_MODULE',
          'SET_CURRENT_VIEW',
        ]
      ),
      ...mapActions('reviews',
        [
          'RETRIEVE_REVIEW',
          'DELETE_REVIEW',
        ]
      ),

      edit() {
        this.SET_CURRENT_VIEW('form');
      },

      async remove() {
        await this.DELETE_REVIEW();
        if (!this.currentUserReview || !this.currentUserReview.length) {
          this.$router.push({name: 'Reviews'});
        }
      },

      back() {
        this.SET_CURRENT_VIEW('review');
      },
    },
  }

</script>

<style lang="scss">

.left-fade-enter-active, .left-fade-leave-active {
  transition: all 0.35s;
}
.left-fade-enter, .left-fade-leave-to  {
  opacity: 0;
  transform: translateX(150px);
}

.right-fade-enter-active, .right-fade-leave-active {
  transition: all 0.35s;
}
.right-fade-enter, .right-fade-leave-to  {
  opacity: 0;
}


  .user_review_container {
    box-sizing: border-box;
    max-width: 1140px;
    width: 100%;;
    height: 100%;
    margin: 0 auto;
    margin-top: 2rem;
    h2 {
      color: #8e8e94;
      font-size: 20px;
      font-family: 'Open Sans', sans-serif;
      letter-spacing: 1px;
      text-align: center;
    }
  }

  .current_user_edit_form {
    box-sizing: border-box;
    width: 75%;
  }

  .user_review_actions_container {
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: flex-end;

    button {
      display: flex;
      justify-content: center;
      align-items: center;
      box-sizing: border-box;
      width: 140px;
      border-radius: 10px;
      padding: 0.7rem 1.5rem;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
      border: none;
      font-family: 'Open Sans', sans-serif;
      box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
      margin: 1.2rem 1.5rem;
      font-weight: bold;
      &:hover {
        opacity: 0.6;
      }
    }
  }

  .user_review_delete_btn {
    background-color: darken(lightgrey, 2);
    color: lighten($primaryBlack, 3);

    svg {
      height: 20px;
      width: 20px;
      color: lighten($primaryBlack, 3);
    }
  }

  .user_review_edit_btn {
    background-color: darken($themeLightBlue, 3);
    color: darken($primaryWhite, 3);
    svg {
      height: 20px;
      width: 20px;
      color: darken($primaryWhite, 10);
    }
  }

.current_user_review {
  display: flex;
  justify-content: center;
}

  .user_review_column {
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 3rem auto;
    padding: 1rem;
    border-radius: 8px;
  }

  .user_review_background {
    background-color: #fafafa;
  }

  @media(max-width:600px) {
    .user_review_column {
      width: 95%;
    }

    .user_review_actions_container {
      flex-direction: column;
      align-items: center;
      button {
        width: 120px;
      }
    }

    .current_user_edit_form {
      width: 95%;
    }
  }

</style>