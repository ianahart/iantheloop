<template>
  <div class="review_container">
    <section>
      <header>
        <div class="review_header_row">
          <div class="review_user_meta">
            <ProfilePicture
              :src="review.profile_picture"
              :alt="review.full_name"
            />
            <div class="review_user_name_rating">
              <h4>{{ review.full_name }}</h4>
                <div>
                  <StarOutlineIcon
                    v-for="(star, index) in stars" :key="index"
                    :className="review.rating > index ? 'star_set' : 'star_unset'"
                  />
                </div>
              </div>
          </div>
          <p class="review_posted_date">{{ postedDate }}</p>
        </div>
        <div class="review_review">
          <div class="review_review_title">
            <PenSolidIcon />
            <p>{{ firstName }} said:</p>
          </div>
          <p class="review_review_text"><span>&#9776;</span><em>{{ review.text }}<span>&#9776;</span></em></p>
          <p v-if="review.is_edited === 1" class="review_review_edited">(Edited)</p>
        </div>
      </header>
    </section>
  </div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import ProfilePicture from './ProfilePicture.vue';
  import StarOutlineIcon from '../Icons/StarOutlineIcon.vue';
  import PenSolidIcon from '../Icons/PenSolidIcon.vue';

  export default {

    name: 'Review',
    props: {
      review: Object,
    },
    components: {
      ProfilePicture,
      StarOutlineIcon,
      PenSolidIcon,
    },

    data() {
      return {
        stars: [1,2,3,4,5],
      }
    },

    mounted() {
      this.postedDate;
    },

    computed: {
      firstName() {
        return this.review.full_name.split(' ')[0];
      },

      postedDate() {
        const posted = new Date(this.review.created_at);
        return posted.toLocaleDateString();
      },
    },

    methods: {

    },
  }

</script>

<style lang="scss">
  .review_container {
    box-sizing: border-box;
    margin: 1.2rem 0;
    background-color: lighten($primaryGray, 5);
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    min-width: 450px;
    border-radius: 8px;
    width: 70%;
    min-height: 400px;

    section {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
    }

    header {
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      width: 100%;
    }
  }


  .review_review_edited {
    margin: 1.2rem 0rem 0.5rem 0rem;
    font-size: 0.8rem;
    color: darken(lightgrey, 15);
  }

  .review_header_row {
    padding: 0.5rem;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    background-color: $themeRoyalBlue;
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
  }

  .review_user_meta {
      display: flex;
      align-items: center;
  }

  .review_user_name_rating {
    margin-left: 1.25rem;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    h4 {
      margin: 0.09rem;
      font-family: 'Secular One', sans-serif;
      color: darken($primaryWhite, 3);
    }
    svg {
      height: 24px;
      width: 24px;
      padding: 0 0.09rem;
      color: $mainInputBorder;
      height: 20px;
      width: 20px;
    }
  }

  .review_review {
    margin-top: 2.9rem;
    box-sizing: border-box;
    width: 70%;
    padding: 0.5rem;
    p {
      line-height: 1.65;
    }
  }

  .review_review_title {
    display:flex;
    align-items: center;
    justify-content: flex-start;

    p {
      color: darken(#6c717b, 3);
      font-weight: bold;
    }
    svg {
      height: 28px;
      width: 28px;
      color: darken($primaryGray, 4);
      fill: darken($primaryGray, 4);
      margin-right: 0.3rem;
    }
  }

  .review_review_text {
    span {
      color: $themePink;
      margin: 0 0.5rem;
      width: 5px;
    }
      font-size: 1.125rem;
      color: lighten(#6c717b, 4);
    em {
      padding-top: 0.3rem;
    }
  }

  .review_posted_date {
    color: lighten($themePink, 9);
    font-family: 'Open Sans', sans-serif;
    font-size: 0.85rem;
    font-weight: bold;
  }

  .star_set {
      fill: darken(yellow, 5);
  }

  .star_unset {
    fill: transparent;
  }

  @media(max-width: 600px) {
    .review_container {
      min-width: 95%;
      width: 95%;
      margin-left: auto;
      margin-right: auto;
      min-height: 0;

      svg {
        width: 20px;
        height: 20px;
      }

      p {
        font-size: 0.75rem;
      }
    }

    .review_user_name_rating {
      h4 {
        font-size: 0.8rem;
      }
    }

    .review_review {
      width: 95%;
      margin-top: 1.4rem;
    }
  }

</style>