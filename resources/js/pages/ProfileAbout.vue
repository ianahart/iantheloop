<template>
  <div v-if="dataLoaded" class="about_profile__container">
    <div class="about_profile_top_meta">
      <PreviousRoute
        routeName="Profile"
      />
      <CompletionBar />
    </div>
    <div
      v-if="dataLoaded"
      class="about_profile_main_content"
    >
      <header class="about_profile_top_content">
        <div v-if="getAboutData.profile_picture" class="about_profile_image__container">
          <ProfileImage
            className="profile_image_lg_square"
            :src="getAboutData.profile_picture"
          />
        </div>
        <div v-if="!getAboutData.profile_picture" class="about_profile_default_image__container">
          <DefaultProfileIcon
            className="icon__lg__neutral"
          />
        </div>

        <p>{{ getAboutData.bio }}</p>
      </header>
      <div class="about_profile__sections">
        <section>
          <h3>About</h3>
        </section>
        <section>
          <div class="about_profile_section__column">
            <div>
            <UserSolidIcon
              className="icon__md__neutral"
            />
            <div class="about_profile_identity">
              <div>
                <div class="about_profile__icon_background">
                  <BirthdayIcon
                    className="icon__sm__light"
                  />
                </div>
                <p>
                  <span>{{ getAboutData.birth_month }}</span>
                  <span>{{ getAboutData.birth_day }}</span>
                  <span>{{ getAboutData.birth_year }}</span>
                </p>
              </div>
              <p>{{ getAboutData.full_name }}</p>
              <p>{{ getAboutData.gender }}</p>
            </div>
            </div>
          </div>
            <div class="about_profile_section__column">
              <div>
                <div class="about_profile__icon_background">
                  <HeartSolidIcon
                    className="icon__sm__light"
                  />
                </div>
                <div class="about_profile_relationship">
                  <p><span class="about_profile_field_title">Status: </span>{{ getAboutData.relationship }}</p>
                </div>
                <div>&nbsp;</div>
              </div>
            </div>
        </section>
        <section>
          <div class="about_profile_section__column">
            <div>
              <div class="about_profile__icon_background">
                <WorkSolidIcon
                  className="icon__sm__light"
                />
              </div>
              <div class="about_profile_work">
                <p>
                  <span class="about_profile_field_title">
                    {{ getAboutData.company }}
                  </span>
                </p>
                <div v-if="getAboutData.position">
                  <PinSolidIcon />
                  <p>{{ getAboutData.work_city }}</p>
                </div>
                <p v-if="getAboutData.position">Works as <span class="about_profile_field_title">{{ getAboutData.position }}</span></p>
                <div>
                </div>
              </div>
              </div>
              <div class="about_profile_work__row">
                  <p v-if="getAboutData.month_from || getAboutData.year_from">
                    <span class="about_profile_field_title">
                      From
                    </span>
                    <span>{{ getAboutData.month_from }}, {{ getAboutData.year_from }}</span>
                    <span class="about_profile_field_title">to
                      <span
                      >{{ workToDate }}
                      </span>
                      </span>
                    </p>
                  <p>{{ getAboutData.description }}</p>
              </div>
          </div>
          <div class="about_profile_section__column">
             <div>
              <div class="about_profile__icon_background">
                <HouseSolidIcon
                  className="icon__sm__light"
                />
              </div>
              <div class="about_profile_location">
                <div class="about_profile_location_row">
                  <h5>Town/City:</h5>
                  <p>{{ getAboutData.town }}</p>
                </div>
                <div class="about_profile_location_row">
                  <h5>State:</h5>
                    <p>{{ getAboutData.state }}</p>
                </div>
                <div class="about_profile_location_row">
                  <h5>Country</h5>
                    <p>{{ getAboutData.country }}</p>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section>
          <div class="about_profile_section__column">
             <div class="about_profile_links__container">
              <div class="about_profile__icon_background">
                <LinkIcon
                  className="icon__sm__light"
                />
              </div>
              <div class="about_profile_social_links">
                <p v-for="(link, index) in getAboutData.links" :key="index">
                  {{ link }}
                </p>
              </div>
              </div>
            </div>
          <div class="about_profile_section__column">
            <div class="about_profile_interests">
              <div class="about_profile__icon_background">
                <SearchIcon
                  className="icon__sm__light"
                />
              </div>
              <div class="about_profile_interests_list">
                <p v-for="interest in getAboutData.interests" :key="interest.id">
                  {{ interest.name}}
                </p>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import PreviousRoute from '../components/Navigation/PreviousRoute.vue';
  import CompletionBar from '../components/AboutProfile/CompletionBar.vue';
  import ProfileImage from '../components/Profile/ProfileImage.vue';
  import DefaultProfileIcon from '../components/Icons/DefaultProfileIcon.vue';
  import UserSolidIcon from '../components/Icons/UserSolidIcon.vue';
  import BirthdayIcon from '../components/Icons/BirthdayIcon.vue';
  import HeartSolidIcon from '../components/Icons/HeartSolidIcon.vue';
  import WorkSolidIcon from '../components/Icons/WorkSolidIcon.vue';
  import PinSolidIcon from '../components/Icons/PinSolidIcon.vue';
  import HouseSolidIcon from '../components/Icons/HouseSolidIcon.vue';
  import LinkIcon from '../components/Icons/LinkIcon.vue';
  import SearchIcon from '../components/Icons/SearchIcon.vue';


  export default {

    name: 'ProfileAbout',

    components: {

      PreviousRoute,
      CompletionBar,
      DefaultProfileIcon,
      ProfileImage,
      UserSolidIcon,
      BirthdayIcon,
      HeartSolidIcon,
      WorkSolidIcon,
      PinSolidIcon,
      HouseSolidIcon,
      LinkIcon,
      SearchIcon,
    },

    async mounted () {

      await this.fetchAboutData(this.$route.params.profileId);
      this.setNumOfCompletedFields();
    },

    beforeDestroy () {

         this.RESET_MODULE();
    },

    watch: {

      fetchError() {

        if (this.fetchError.length) {

          this.RESET_MODULE();
          this.$router.push({name: 'NotFound'});
        }
      },
    },


    computed: {

      ...mapState('profileAbout',
          [
          'dataLoaded',
          'fetchError',
          ]
        ),

      ...mapGetters('profileAbout',
        [
          'getAboutData'
        ]
      ),

      workToDate() {

        if (this.getAboutData.work_currently === '1') {

          return 'Currently';
        } else if (this.getAboutData.month_to.length || this.getAboutData.year_to.length) {

          return `${this.getAboutData.month_to} ${this.getAboutData.year_to}`;
        } else {

          return '';
        }
      }
    },

    methods: {

      ...mapMutations('profileAbout',
        [
          'RESET_MODULE',
          'SET_NUM_OF_COMPLETED_FIELDS'
        ]
      ),

      ...mapActions('profileAbout',
        [
          'FETCH_ABOUT_DATA'
        ]
      ),

      async fetchAboutData (profileId) {

        await this.FETCH_ABOUT_DATA(profileId);
      },

      setNumOfCompletedFields() {

        this.SET_NUM_OF_COMPLETED_FIELDS();
      },
    }
  }
</script>

<style lang="scss">

.about_profile__container {
  box-sizing: border-box;
  background-color: lighten($primaryGray, 2);
  height: 100%;
  width: 100%;
  padding: 0.5rem;

  p {
  -ms-word-break: break-all;
  word-break: break-all;
  word-break: break-word;
  hyphens: auto;
  }

  h3 {
    font-family: 'Open Sans', sans-serif;
  }
}

.about_profile_top_meta {
  display: flex;
  justify-content: space-between;
}

.about_profile_main_content {
  box-sizing: border-box;
  border-radius: 8px;
  box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
  background-color: #fff;
  margin: 5rem auto;
  padding-top: 2px;
  max-width: 940px;
  height: auto;
}

.about_profile_top_content {
  box-sizing: border-box;
  width: 95%;
  padding: 1rem;
  padding-top: 2rem;
  display: flex;
  justify-content: space-between;
  background-color: lighten($primaryGray, 3);
  border-radius: 8px;
  margin: 0 auto;
  margin-top: 2rem;
  align-items: center;
  min-height: 400px;

  div {
    width: 45%;
  }

  p {
    width: 45%;
    line-height: 1.6;
    text-indent: 16px;
    color: $primaryBlack;
    font-family: 'Open Sans', sans-serif;
    margin: 0;
  }
}

.about_profile_image__container {
    position: relative;
}

.about_profile_default_image__container {
    height: 200px;
    width: 200px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: $primaryGray;
    border: 1px solid darken($primaryGray, 5);
}

.about_profile__sections {
  box-sizing: border-box;

  section {
    box-sizing: border-box;
    border-bottom: 1px solid $primaryGray;
    display: flex;
    justify-content: space-between;
    margin: 1rem auto;
    padding: 1rem;
    min-height: 100px;
    // align-items: center;
  }
}

.about_profile_section__column > div {
          display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
}

.about_profile_section__column {
        box-sizing: border-box;
        margin: 0 4rem;
        min-width: 320px;
        padding: 0.5rem;

        p, span {
          font-family: 'Open Sans', sans-serif;
          color: darken(gray, 10);
          font-size: 0.87rem;
          font-weight: 400;
        }
}

span.about_profile_field_title {

  font-weight: 600;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  padding: 0 0.5rem;
}

.about_profile_identity {

  span {
    padding: 0.1rem;
  }
}

.about_profile_identity,
.about_profile_work {
  width: 45%;
}

.about_profile_social_links {
  box-sizing: border-box;
  margin-top: 2.5rem;
  display: flex;
  justify-content: center;
  flex-direction: column;

   p{
    overflow-wrap: break-word;
    text-align: center;
    font-size: 0.85rem;
    word-wrap: break-word;
    -ms-word-break: break-all;
    word-break: break-all;
    word-break: break-word;
    -ms-hyphens: auto;
    -moz-hyphens: auto;
    -webkit-hyphens: auto;
    hyphens: auto;
   }
}

.about_profile_links__container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.about_profile_interests {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;

  p {
    overflow-wrap: break-word;
    font-size: 0.85rem;
    word-wrap: break-word;
    -ms-word-break: break-all;
    word-break: break-all;
    word-break: break-word;
    -ms-hyphens: auto;
    -moz-hyphens: auto;
    -webkit-hyphens: auto;
    hyphens: auto;
    margin: 0.2rem 0.5rem;
    padding: 0.2rem 0.5rem;
    border: 1px solid darken($primaryGray, 10);
    border-radius: 8px;
  }
}

.about_profile_interests_list {
  margin-top: 2.5rem;
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
  width: 100%;
}

.about_profile_location  {
  width: 55%;
  box-sizing: border-box;

  p {
    display: flex;
    justify-content: space-between;
  }
}

.about_profile_location_row {
  display: flex;

  h5 {
    text-align: left;
    font-weight: 600;
    letter-spacing: -1px;
    color: darken(gray, 10);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 0 0.5rem;
    margin: 0.2rem 0;
  }

}

.about_profile_work,
.about_profile_location {

  p {

    margin: 0.3rem 0;
  }
}

.about_profile_work > div,
.about_profile_location > div {
  display: flex;
  justify-content: flex-start;
  align-items: center;

  p {
    margin-left: 0.3rem;
  }
}

div.about_profile_work__row {

  display: flex;
  flex-direction: column;
  align-items: flex-start;

  p {
    line-height: 1.6;
    font-family: 'Open Sans', sans-serif;
    text-indent: 12px;
  }
}

.about_profile_identity > div {
    display: flex;
      justify-content: space-between;
}


.about_profile_relationship {
  display: flex;
  justify-content: flex-end;
  width: 70%;
}

.about_profile__icon_background {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: lighten($themeLightBlue, 5);
  border-radius: 50%;
  height: 36px;
  width: 36px;
}

@media (max-width: 886px) {

  .about_profile_top_content  {
    flex-direction: column;
    align-items: center;
    margin: 0 auto;

    p {
      margin-top: 1.2rem;
      width: 100%;
    }
  }

  .about_profile_image__container {
    display: flex;
    justify-content: center;
  }

  .about_profile__sections {

    section {
      flex-direction: column;
    }
  }

  .about_profile_section__column {
    margin: 1.5rem auto;
  }
}

</style>