<template>
  <div class="profile_stats__container">
   <div class="profile_stats_row__container">
      <div class="profile_stats__row">
        <div class="profile_stats_display_name">
          <p>{{ getBaseProfile.display_name }}</p>
          <p class="profile_stats_company">{{ getBaseProfile.company }}</p>
        </div>
        <div class="profile_stats_divider"></div>
        <Followers
          :followersCount="profileStats.followers_count"
        />
        <div class="profile_stats_divider"></div>
        <Following
          :followingCount="profileStats.following_count"
        />
      </div>
      <div class="profile_stats__buttons">
        <FollowBtn
          v-if="currentUserId !== parseInt($route.params.id) && currentUserNoActions"
          :stats="profileStats"
        />
        <RequestedBtn
          v-if="currentUserId !== parseInt($route.params.id) && currentUserRequested"
        />
        <FollowingBtn
          v-if="currUserFollowing"
        />

       <div  v-if="isModalOpen">
         <FollowingModal />
       </div>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters } from 'vuex';

  import Followers from './Followers.vue';
  import FollowBtn from './FollowBtn.vue';
  import Following from './Following.vue';
  import FollowingBtn from './FollowingBtn.vue';
  import FollowingModal from './FollowingModal.vue';
  import RequestedBtn from './RequestedBtn.vue';

  export default {

    name: 'ProfileStats',

    props: {

    },

    components: {

      Followers,
      FollowBtn,
      Following,
      FollowingBtn,
      FollowingModal,
      RequestedBtn,
    },

    computed: {

      ...mapState('profile',
        [
          'currentUserId',
          'profileStats',
          'currUserFollowing',
          'isModalOpen',
          'followStatus',
        ]
      ),

      ...mapGetters('profile',
        [
          'getBaseProfile'
        ]
      ),

      currentUserNoActions () {
        return !this.currUserFollowing && !this.followStatus;
      },

      currentUserRequested () {
         return this.followStatus.length && this.followStatus.toLowerCase() === 'requested';
      }
    },
  }
</script>

<style lang="scss">


  .profile_stats__buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
  }

  .profile_stats__container {
    max-width: 650px;
    border-radius: 8px;
    margin-left: auto;
    margin-right: 5.5rem;
    margin-top: 1.3rem;
    padding: 1.2rem 0;
  }

  .profile_stats_row__container {
  display: flex;
  justify-content: space-evenly;
  }

  .profile_stats__row {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    flex-grow: 1;
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


      &:first-of-type {

        color: $themeBlue;
        font-weight: normal;
    }

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

  @media(max-width: 930px) {


  .profile_stats__buttons {
    width: 25%;
    margin: 0 auto;
    margin-top: 2rem;

  }

   .profile_stats_row__container {

     flex-direction: column;
  }

    .profile_stats__container {
      margin-top: 6.9rem;
      margin-left: auto;
      margin-right: 0;
    }

     .profile_stats_display_name {

    display: flex;
    flex-direction: column;
    align-items: flex-start;

    p {
      margin: 0;
      color: $primaryBlack;
      font-weight: bold;
      font-size:0.7rem;


      &:first-of-type {

        color: $themeBlue;
        font-weight: normal;
        font-size: 0.7rem;
    }

      &:last-of-type {
        margin-top: 0.3rem;
        font-size: 0.7rem;
        color: darken($primaryGray, 18);
        font-weight: normal;
      }
    }
  }

  .profile_stats_following,
  .profile_stats_followers {

    p {
      font-size: 0.7rem;
    }
  }
}



</style>