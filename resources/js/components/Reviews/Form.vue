<template>
  <div class="review_form_container">
    <form @submit.prevent="handleSubmit">
      <div class="review_form_user_meta">
        <ProfilePicture
            :src="getProfilePic"
            :alt="userName"
          />
          <div class="current_user_name_column">
            <h4>{{ name }}</h4>
            <p>Your review will be posted publicy on the web.</p>
          </div>
      </div>
      <div
      @mouseleave="handleRatingLeave"
      class="review_form_rating">
        <div
           v-for="(star, index) in ratings"
           :key="index"
           @click="setRating"
           @mouseover="!isRatingSet ? highlight(star, 'selected') : ''"
           @mouseleave="!isRatingSet ? unhighlight(star, 'unselected'): ''"
        >
          <StarOutlineIcon
            :className="star.state === 'selected' ? 'star_highlighted' : 'star_unhighlighted'"
          />
        </div>
      </div>
      <TextArea
        :field="form[0].field"
        maxLength="300"
        :type="form[0].type"
        :errors="form[0].errors"
        :label="form[0].label"
        :value="form[0].value"
        :nameAttr="form[0].nameAttr"
        commitPath="reviews/UPDATE_FIELD"
      />
      <div v-if="errors.length" class="reviews_errors_container">
        <p
        v-for="(error, index) in errors"
        :key="index">ths is an error oh boy sdfj dklsfjdkls jdsfkl a </p>
      </div>
      <div class="reviews_form_btn_container">
        <button type="submit">Post</button>
      </div>
    </form>
  </div>
</template>


<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import StarOutlineIcon from '../Icons/StarOutlineIcon.vue';
  import ProfilePicture from './ProfilePicture.vue';
  import TextArea from '../forms/inputs/TextArea.vue';

  export default {
    name: 'Form',
    props: {

    },
    components: {
      StarOutlineIcon,
      ProfilePicture,
      TextArea,
    },

    data () {
      return {
            ratings: [
              {id: 1, state: 'unselected'},
              {id: 2, state: 'unselected'},
              {id: 3, state: 'unselected'},
              {id: 4, state: 'unselected'},
              {id: 5, state: 'unselected'}
              ],
        currentRating: 0,
        isRatingSet:false,

      }
    },

    mounted() {

    },

    beforeDestroy() {

    },

    computed: {
      ...mapGetters('user',
        [
          'getProfilePic',
          'userName',
        ]
      ),
      ...mapState('reviews',
        [
          'form',
          'errors',
        ]
      ),

      name() {
        if (this.userName) {
            return this.userName.
            split(' ')
            .map(word => word.toUpperCase().slice(0,1) + word.toLowerCase().slice(1))
            .join(' ');
          }
        }
    },

    methods: {
        ...mapMutations('reviews',
          [
            'SET_RATING',
            'CLEAR_ERRORS',
          ]
        ),
       ...mapActions('reviews',
        [
          'SUBMIT_REVIEW'
        ]
      ),
      highlight(star, action) {
        const notSequential = this.ratings.some((rating, index) => {
          return rating.state === 'unselected' && index + 1 < star.id;
        });

        if (!notSequential) {
          this.changeRating(star, action);
        }
      },

      unhighlight(star, action) {
        if (this.allRatingsSelected()) {
          this.changeRating(star, action);
        }
      },

      handleRatingLeave() {
        if (!this.isRatingSet) {
          this.resetRating();
        }
      },

      changeRating(star = null, action) {

        this.ratings.forEach(rating => {
          if (action === 'unselected') {
            rating.state = action;
          } else {
            if (rating.id === star.id) {
              rating.state = action;
            }
          }
        });
      },

      resetRating() {
        this.ratings.forEach(rating => {
            rating.state = 'unselected';
          });
      },

      setRating() {

        if (this.isRatingSet) {
          this.isRatingSet = false;
          this.resetRating();
          this.SET_RATING(0);
          return;
        }

        const rating = this.ratings.reduce((acc, cur) => {
          if (cur.state === 'selected') {
              return acc + 1;
          } else {
            return acc + 0;
          }
        }, 0);

        this.isRatingSet = true;
        this.SET_RATING(rating);
      },

      allRatingsSelected() {
       return this.ratings.every(rating => rating.state === 'selected');
      },

      async handleSubmit() {
        this.CLEAR_ERRORS();
        await this.SUBMIT_REVIEW();
      },
    },
  }
</script>


<style lang="scss">
  .review_form_container {
    box-sizing: border-box;
    width: 70%;
    border: 1px solid $mainInputBorder;
    border-radius: 8px;
    border-top-left-radius: 0px;
    border-top-right-radius: 0px;
    margin: 0 auto;
    padding: 1rem;
    margin-bottom: 2.5rem;
  }

  .review_form_user_meta {
    display: flex;
    box-sizing: border-box;
    justify-content: flex-start;
  }

  .current_user_name_column {
    box-sizing: border-box;
    padding: 0.2rem;
    display: flex;
    flex-direction: column;
    padding-left: 0.7rem;

    h4 {
      color: $primaryBlack;
      margin: 0;
    }
    p {
      color: lighten(#6c717b, 5);
      margin-top: 0.1rem;
      font-size: 0.8rem;
    }
  }

  .review_form_rating {
    box-sizing: border-box;
    margin: 1.5rem 0;
    display: flex;
    justify-content: flex-start;
    align-items: center;

    svg {
      height: 26px;
      width: 26px;
      padding: 0 0.05rem;
      color: $mainInputBorder;
      cursor: pointer;
    }
  }

  .star_highlighted {
    fill: darken(yellow, 5);
    transition: all 0.2s ease-in-out;
  }

  .star_unhighlited {
    background-color: transparent;
  }

  .reviews_form_btn_container {
    display: flex;
    justify-content: center;
    margin: 1.5rem 0;

    button {
      border: none;
      background-color: $themeBlue;
      color: $primaryWhite;
      width: 140px;
      font-size: 1rem;
      font-family: 'Open Sans', sans-serif;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
      border-radius: 40px;
      padding: 0.5rem 1rem;
      &:hover {
        opacity: 0.8;
      }
    }
  }

  .reviews_errors_container {
    margin: 1rem 0;
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    flex-direction: column;
    p {
      margin: 0.05rem;
      font-family: 'Open sans',sans-serif;
      color: darken($error, 3);
      font-weight: bold;
      font-size: 0.7rem;
    }
  }

  @media(max-width:600px) {
    .review_form_container {
      width: 100%;
    }
  }
</style>