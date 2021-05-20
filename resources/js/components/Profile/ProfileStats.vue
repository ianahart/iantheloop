<template>
  <div class="profile_stats__container">
   <div class="profile_stats__row">
     <div class="profile_stats_display_name">
       <p>{{ getBaseProfile.display_name }}</p>
       <p class="profile_stats_company">{{ getBaseProfile.company }}</p>
     </div>
     <div class="profile_stats_divider"></div>
     <Followers />
    <div class="profile_stats_divider"></div>
    <Following />
    <FollowBtn />
  </div>
  </div>
</template>

<script>

  import { mapGetters } from 'vuex';

  import Followers from './Followers.vue';
  import FollowBtn from './FollowBtn.vue';
  import Following from './Following.vue';

  export default {

    name: 'ProfileStats',

    props: {

    },

    components: {

      Followers,
      FollowBtn,
      Following,
    },

    computed: {

      ...mapGetters('profile',
        [
          'getBaseProfile'
        ]
      ),
    },
  }
</script>

<style lang="scss">

  .profile_stats__container {
    max-width: 650px;
    border-radius: 8px;
    margin-left: auto;
    margin-right: 5.5rem;
    margin-top: 1.3rem;
    padding: 1.2rem 0;
  }

  .profile_stats__row {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
  }

  .profile_stats_divider {
    height: 50px;
    border-right: 1px solid $primaryGray;
    border-left: 1px solid $primaryGray;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .profile_stats_display_name {

    display: flex;
    flex-direction: column;
    align-items: flex-start;

    p {
      margin: 0;
      color: $primaryBlack;
      font-weight: bold;

      &:last-of-type {
        margin-top: 0.3rem;
        font-size: 0.8rem;
        color: darken($primaryGray, 18);
        font-weight: normal;
      }
    }
  }

  .profile_stats_company {
    color: darken($primaryGray, 18);
    font-size: 0.8rem;
    font-weight: normal;
  }

  .profile_stats_display_name {
    p {
      &:first-of-type {

        color: $themeBlue;
        font-weight: normal;
      }
    }
  }

  @media(max-width: 600px) {

    .profile_stats__container {
      margin-top: 6.9rem;
      margin-left: auto;
      margin-right: auto;
    }
  }

</style>