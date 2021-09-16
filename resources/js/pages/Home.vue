<template>
  <div class="home__container">
    <img
      v-if="!thresholdPassed"
      class="decorative_star_1 decorative_star"
      :src="require('../../assets/decoration-star.svg').default"
      alt="decorative star images"
    />
    <img
      v-if="!thresholdPassed"
      class="decorative_star_2 decorative_star"
      :src="require('../../assets/decoration-star.svg').default"
      alt="decorative star images"
    />
    <section class="home_main">
      <div class="home_main_row">
        <div class="home_tagline_container">
          <h1>Linking people globally</h1>
          <p>
            Prosciutto pancetta short loin pork belly venison pork chop sirloin
            t-bone. Pastrami salami capicola chicken pork jowl tail turkey
            meatball filet mignon.
          </p>
          <div class="home_tagline_actions">
            <router-link class="home_action_btn" :to="{ name: 'CreateAccount' }"
              >Signup</router-link
            >
            <a
              class="home_action_btn"
              href="mailto:fictionalcompany@example.com?subject=Basic Information:"
              >Contact</a
            >
          </div>
        </div>
        <div class="home_main_images_container">
          <img
            :src="require('../../assets/home_main_1.svg').default"
            alt="a user looking through their friend's list"
          />
        </div>
      </div>
      <div class="home_main_statistics">
        <div class="home_main_statistic">
          <h3>{{ userCount }}</h3>
          <p>Happy Users</p>
        </div>
        <div class="home_main_statistic">
          <h3>{{ reviewCount }}</h3>
          <p>Active Reviews</p>
        </div>
        <div class="home_main_statistic">
          <h3>{{ reviewRating }}</h3>
          <p>User Rating</p>
        </div>
      </div>
    </section>
    <section class="home_description">
      <div class="home_description_content">
        <h2>
          A place where location does not matter, stay in the loop with new and
          old friends
        </h2>
        <p>
          Capitalize on low hanging fruit to identify a ballpark value added
          activity to beta test. Override the digital divide with additional
          clickthroughs from DevOps. Nanotechnology immersion along the
          information highway will close the loop on focusing solely on the
          bottom line.
        </p>
      </div>
    </section>
    <section class="home_difference_list">
      <img
        v-if="!thresholdPassed"
        class="decorative_star_1 decorative_star"
        :src="require('../../assets/decoration-star.svg').default"
        alt="decorative star images"
      />
      <div class="home_difference_list_content">
        <img
          class=""
          :src="require('../../assets/home_offers.svg').default"
          alt="a person looking at a list of there friends who are online"
        />
        <div class="home_difference_list_content_text">
          <h2>
            An alternative to bloated social apps, only essential features
            included
          </h2>
          <ul class="home_list_default">
            <li>
              Dynamically procrastinate B2C users after installed base benefits.
            </li>
            <li>
              Taking seamless key performance indicators offline to maximise the
              long tail.
            </li>
            <li>
              Efficiently unleash cross-media information without cross-media
              value.
            </li>
            <li>
              Holistically pontificate installed base portals after maintainable
              products.
            </li>
          </ul>
          <router-link class="home_action_btn" :to="{ name: 'About' }"
            >Learn More</router-link
          >
        </div>
      </div>
    </section>
    <section class="home_service_offers">
      <div class="home_service_offers_container">
        <div class="home_service_offers_text">
          <h2>Built with you in mind</h2>
          <p>
            Objectively seize scalable metrics whereas proactive e-services.
            Seamlessly empower fully researched growth strategies and
            interoperable internal or "organic" sources.
          </p>
          <ul class="home_list_default">
            <li>Dramatically maintain clicks-and-mortar</li>
            <li>Seamlessly visualize quality intellectual capital</li>
            <li>
              Dramatically synthesize integrated schemas with optimal networks.
            </li>
          </ul>
        </div>
        <div class="home_service_offers_icons">
          <div class="service_offers_grid_column">
            <div>
              <div class="service_offer_icon_container">
                <BookIcon />
              </div>
              <p>Entertaining</p>
            </div>
            <div>
              <div class="service_offer_icon_container">
                <CameraSolidIcon />
              </div>
              <p>Media</p>
            </div>
          </div>
          <div class="service_offers_grid_column">
            <div>
              <div class="service_offer_icon_container">
                <FriendsIcon />
              </div>
              <p>Followers</p>
            </div>
            <div>
              <div class="service_offer_icon_container">
                <CheckCircleIcon />
              </div>
              <p>Privacy</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="home_interests">
      <img
        class=""
        :src="require('../../assets/home_interests.svg').default"
        alt="users connected to the same page"
      />
      <img
        v-if="!thresholdPassed"
        class="decorative_star_2 decorative_star"
        :src="require('../../assets/decoration-star.svg').default"
        alt="decorative star images"
      />
      <div class="home_interests_text">
        <h2>Find common interests</h2>
        <p>
          Robotic process automation share purchase agreement spam score
          marketing automation platform lead segmentation share purchase
          agreement pro forma invoice organic search traffic single sign on
          feature.
        </p>
      </div>
    </section>
    <section class="home_reviews">
      <span class="review_quote_icon">&#8222;</span>
      <div
        v-if="testimonials && testimonials.length"
        class="home_reviews_container"
      >
        <div class="home_reviews_swiper">
          <svg
            @click="swipePrev"
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 reviews_swiper_left"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
          <div class="home_reviews_list">
            <transition name="swiper" mode="out-in" appear>
              <div
                :key="testimonials[current].id"
                class="home_review_list_item"
              >
                <ProfilePicture
                  :alt="testimonials[current].full_name"
                  :src="testimonials[current].profile_picture"
                />
                <p>{{ testimonials[current].text }}</p>
                <h4>
                  -
                  {{
                    testimonials[current].full_name
                      .split(" ")
                      .map(
                        (word) =>
                          word.toUpperCase().slice(0, 1) +
                          word.toLowerCase().slice(1)
                      )
                      .join(" ")
                  }}
                </h4>
              </div>
            </transition>
          </div>
          <svg
            @click="swipeNext"
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 reviews_swiper_right"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7"
            />
          </svg>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

import ProfilePicture from "../components/Reviews/ProfilePicture.vue";
import BookIcon from "../components/Icons/BookIcon.vue";
import CameraSolidIcon from "../components/Icons/CameraSolidIcon.vue";
import FriendsIcon from "../components/Icons/FriendsIcon.vue";
import CheckCircleIcon from "../components/Icons/CheckCircleIcon.vue";

export default {
  name: "Home",

  components: {
    BookIcon,
    CameraSolidIcon,
    FriendsIcon,
    CheckCircleIcon,
    ProfilePicture,
  },
  data() {
    return {
      screenWidth: "",
      thresholdPassed: false,
      current: 0,
      swiperID: "",
      timeID: "",
      isSwiping: false,
    };
  },
  async mounted() {
    window.addEventListener("resize", this.handleResize);
    await this.RETRIEVE_USER_COUNT();
    await this.GET_TESTIMONIALS();

    if (this.testimonials.length) {
      this.swipeLoop();
    }
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.handleResize);
    this.CLEAR_TESTIMONIALS();
    window.clearInterval(this.swiperID);
    window.clearTimeout(this.timerID);
  },
  computed: {
    ...mapState(["userCount", "reviewCount", "reviewRating", "testimonials"]),
  },
  methods: {
    ...mapMutations(["CLEAR_TESTIMONIALS"]),
    ...mapActions(["RETRIEVE_USER_COUNT", "GET_TESTIMONIALS"]),

    handleResize(e) {
      if (e.target.innerWidth <= 650) {
        if (!this.thresholdPassed) {
          this.thresholdPassed = true;
        }
      } else {
        if (this.thresholdPassed) {
          this.thresholdPassed = false;
        }
      }
    },

    swipeLoop() {
      clearInterval(this.swiperID);

      this.swiperID = setInterval(() => {
        if (this.current + 1 === this.testimonials.length) {
          this.current = 0;
        } else {
          this.current = this.current + 1;
        }
      }, 5000);
    },

    swipePrev() {
      clearInterval(this.swiperID);
      clearTimeout(this.timerID);

      if (this.current === 0) {
        this.current = this.testimonials.length - 1;
      } else {
        this.current = this.current - 1;
      }
      this.restartSwipeLoop();
    },

    swipeNext() {
      clearInterval(this.swiperID);
      clearTimeout(this.timerID);

      if (this.current + 1 === this.testimonials.length) {
        this.current = 0;
      } else {
        this.current = this.current + 1;
      }
      this.restartSwipeLoop(5000);
    },

    restartSwipeLoop(timeout) {
      this.timerID = setTimeout(() => {
        this.swipeLoop();
      }, timeout);
    },
  },
};
</script>

<style lang="scss">
.swiper-enter-active,
.swiper-leave-active {
  transition: all 0.65s;
}
.swiper-enter,
.swiper-leave-to {
  opacity: 0;
  transform: translate(-200px);
}

.home__container {
  box-sizing: border-box;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  display: flex;
  margin: 0 auto;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  position: relative;
}
.home_main {
  box-sizing: border-box;
  margin: 0 auto;
  margin-top: 6.5rem;
  width: 1140px;
}

.home_main_row {
  display: flex;
  justify-content: space-evenly;
}
.home_main_statistics {
  display: flex;
  justify-content: space-evenly;
}
.home_main_statistic {
  box-sizing: border-box;
  margin-top: 3rem auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  h3 {
    font-size: 6rem;
    color: $primaryBlack;
    font-family: "Open Sans", sans-serif;
    margin: 0;
    margin-bottom: 0.05rem;
  }
  p {
    margin-top: -1rem;
    color: #6c717b;
  }
}
.home_tagline_container {
  box-sizing: border-box;
  width: 100%;
  display: flex;
  align-items: center;
  flex-direction: column;
  padding-right: 1rem;
  h1 {
    font-size: 4rem;
    color: $primaryBlack;
    font-family: "Open Sans", sans-serif;
    font-weight: 700;
    margin-top: 8rem;
    text-align: left;
  }
  p {
    font-size: 1.125rem;
    color: #6c717b;
    line-height: 1.65;
  }
}

.home_list_default {
  padding: 0;
  li {
    margin: 0.8rem 0;
    font-family: "Open Sans", sans-serif;
    font-size: 1rem;
    color: #6c717b;
    &::before {
      content: "\25A0";
      color: lighten($themeLightBlue, 5);
      font-weight: bold;
      display: inline-block;
      width: 1.5rem;
      margin-left: -1.2rem;
    }
  }
}
.home_main_images_container {
  box-sizing: border-box;
  padding-left: 1rem;
  width: 690x;
  img {
    width: 600px;
  }
}
.decorative_star {
  z-index: -20;
  position: absolute;
}
.decorative_star_1 {
  height: 500px;
  width: 500px;
  left: -140px;
  top: 150px;
  margin-left: 2rem;
}
.decorative_star_2 {
  width: 600px;
  height: 600px;
  right: 0px;
  top: 50px;
}

.home_action_btn {
  box-sizing: border-box;
  padding: 1rem 1.5rem 1rem 1.5rem;
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  line-height: inherit;
  text-align: center;
  transition: all 0.35s ease-in-out;
  margin: 0 1rem;
  font-weight: bold;
  font-family: "Secular One", sans-serif;
  cursor: pointer;
  width: 170px;
  text-decoration: none;
  display: inline-block;
}

.home_tagline_actions {
  box-sizing: border-box;
  a {
    &:first-of-type {
      background-color: $themeLightBlue;
      color: $primaryWhite;
      &:hover {
        border: 1px solid $themeLightBlue;
        background: transparent;
        color: $themeLightBlue;
      }
    }
    &:last-of-type {
      color: $themePink;
      background-color: transparent;
      border: 1px solid $themePink;
      &:hover {
        background-color: $themePink;
        color: $primaryBlack;
      }
    }
  }
}

.home_description {
  width: 100%;
  padding: 1rem 0.5rem 6rem 0.5rem;
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  margin: 3rem 0;
  background-color: darken($primaryWhite, 1);
}

.review_quote_icon {
  position: absolute;
  top: -178px;
  color: $themePink;
  left: 0;
  font-size: 11.5rem;
}

.home_reviews_swiper {
  box-sizing: border-box;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.home_reviews_list {
  width: 65%;
}

.home_review_list_item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  h4 {
    color: lighten($primaryBlack, 4);
    font-family: "Secular One", sans-serif;
  }
  p {
    font-size: 1.125rem;
    color: #6c717b;
    font-family: "Open Sans", sans-serif;
    line-height: 1.65;
    font-style: italic;
  }

  img {
    height: 150px;
    width: 150px;
  }
  svg {
    height: 150px;
    width: 150px;
  }
}

.reviews_swiper_left,
.reviews_swiper_right {
  cursor: pointer;
  color: #6c717b;
  height: 40px;
  width: 40px;
  transition: all 0.25s ease-in-out;
  &:hover {
    color: darken(#6c717b, 5);
  }
}

.home_reviews_container {
  box-sizing: border-box;
  margin: 0 auto;
  margin-top: 6.5rem;
  width: 1140px;
  display: flex;
  justify-content: space-evenly;
  align-items: center;
}

.home_interests {
  position: relative;
  box-sizing: border-box;
  margin: 0 auto;
  margin-top: 6.5rem;
  width: 1140px;
  display: flex;
  justify-content: center;
  align-items: center;

  img {
    width: 55%;
  }
}

.home_interests_text {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  width: 45%;
  box-sizing: border-box;
  h2 {
    color: lighten($primaryBlack, 4);
    font-weight: 900;
    box-sizing: border-box;
    margin: 0.1rem auto 2rem auto;
    font-family: "Open Sans", sans-serif;
    width: 80%;
    text-align: right;
  }

  p {
    box-sizing: border-box;
    font-size: 1.05rem;
    color: #6c717b;
    line-height: 1.65;
    margin: 0 0;
    width: 60%;
    text-align: right;
    padding: 0.5rem;
    font-weight: bold;
  }
}

.home_description_content {
  box-sizing: border-box;
  margin: 0 auto;
  margin-top: 6.5rem;
  width: 1140px;
  h2 {
    color: lighten($primaryBlack, 4);
    font-weight: 900;
    box-sizing: border-box;
    margin: 0.1rem auto 2rem auto;
    font-family: "Open Sans", sans-serif;
    width: 80%;
    text-align: center;
  }

  p {
    box-sizing: border-box;
    font-size: 1.05rem;
    color: #6c717b;
    line-height: 1.65;
    margin: 0 auto;
    width: 60%;
  }
}

.home_difference_list {
  position: relative;
  width: 100%;
  padding: 1rem 0.5rem 6rem 0.5rem;
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  margin: 3rem 0;
}

.home_difference_list_content {
  box-sizing: border-box;
  margin: 0 auto;
  margin-top: 6.5rem;
  width: 1140px;
  display: flex;
  justify-content: center;

  img {
    box-sizing: border-box;
    padding-right: 1.2rem;
    width: 45%;
  }
}

.home_difference_list_content_text {
  padding-left: 1.2rem;
  width: 55%;
  box-sizing: border;
  display: flex;
  flex-direction: column;

  a {
    display: block;
    text-align: center;
    background-color: $themeLightBlue;
    color: $primaryWhite;
    &:hover {
      border: 1px solid $themeLightBlue;
      background: transparent;
      color: $themeLightBlue;
    }
  }

  h2 {
    font-family: "Open Sans", sans-serif;
    color: darken($themeBlue, 10);
  }
}

.home_service_offers,
.home_reviews {
  position: relative;
  width: 100%;
  padding: 1rem 0.5rem 6rem 0.5rem;
  box-sizing: border-box;
  margin: 3rem 0;
  background-color: darken($primaryWhite, 1);
  display: flex;
  justify-content: center;
}

.home_service_offers_container {
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  width: 1140px;
}

.home_service_offers_text {
  box-sizing: border-box;
  width: 45%;
  padding-right: 1.2rem;

  h2 {
    font-family: "Open Sans", sans-serif;
    color: darken($themeBlue, 10);
  }

  p {
    color: #6c717b;
    font-size: 0.9rem;
    line-height: 1.6;
    font-family: "Open Sans", sans-serif;
  }
  ul {
    padding-left: 1rem;
  }
}

.home_service_offers_icons {
  box-sizing: border-box;
  width: 55%;
  padding-left: 1.2rem;

  display: grid;
  grid-template-columns: 1fr 1fr;
}

.service_offer_icon_container {
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  background-color: #fff;
  width: 90px;
  height: 90px;
  align-content: center;
  border-radius: 50%;
  padding: 0.5rem;
  svg {
    width: 65px;
    height: 65px;
    color: lighten($themeLightBlue, 3);
  }
}

.service_offers_grid_column {
  box-sizing: border-box;
  p {
    text-align: center;
    width: 37.5%;
    color: #6c717b;
    font-family: "Open Sans", sans-serif;
    font-size: 0.9rem;
    font-weight: bold;
  }
}

@media (max-width: 950px) {
  .home_main {
    width: 95%;
  }

  .home_main_row {
    flex-direction: column;
    align-items: center;
    margin: 1rem auto;
  }
  .home_main_images_container {
    margin: 0 auto;
    margin-top: 2rem;
    padding: 0;
    text-align: center;
    img {
      width: 80%;
    }
  }
  .home_tagline_container {
    h1 {
      text-align: center;
    }
  }
  .decorative_star_1 {
    width: 250px;
    height: 250px;
  }
  .decorative_star_2 {
    width: 350px;
    height: 350px;
  }
  .home_tagline_actions {
    display: flex;
    justify-content: space-between;
  }

  .home_action_btn {
    width: 100px;
    padding: 0.5rem 1rem 0.5rem 1rem;
  }

  .home_main_statistics {
    flex-direction: column;
  }

  .home_main_statistic {
    h3 {
      margin-top: 0.25rem;
      font-size: 2.8rem;
    }
  }

  .home_description_content {
    h2 {
      width: 95%;
      font-size: 1.3rem;
    }
    p {
      width: 95%;
      font-size: 0.85rem;
    }
  }

  .home_difference_list_content {
    flex-direction: column;

    img {
      width: 95%;
      padding-right: 0;
    }
  }

  .home_description {
    margin-bottom: 0;
  }

  .home_difference_list {
    margin-top: 0;
  }

  .home_difference_list_content_text {
    list-style-type: circle;
    margin-top: 2.5rem;
    text-align: center;
    width: 95%;
    padding-left: 0;
  }

  .home_list_default {
    li {
      font-size: 0.85rem;
      text-align: left;

      &::before {
        margin-left: 0;
      }
    }
  }

  .home_service_offers {
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .home_service_offers_container {
    width: 95%;
    flex-direction: column-reverse;
  }

  .home_service_offers_icons {
    padding-left: 0;
    width: 95%;
  }

  .home_service_offers_text {
    padding-right: 0;
    width: 95%;
    li::before {
      margin-left: -1.09rem;
      margin-top: -0.1rem;
    }
  }
  .service_offers_grid_column {
    justify-self: center;

    p {
      width: 40%;
    }
  }
  .service_offer_icon_container {
    width: 60px;
    height: 60px;

    svg {
      width: 40px;
      height: 40px;
    }
  }

  .home_interests {
    margin-top: 2rem;
    text-align: center;
    flex-direction: column;
    justify-content: center;

    img {
      width: 95%;
    }
  }

  .home_interests_text {
    text-align: center;
    width: 95%;
    h2 {
      text-align: center;
    }

    p {
      text-align: center;
      margin: 0 auto;
    }
  }
}
</style>
