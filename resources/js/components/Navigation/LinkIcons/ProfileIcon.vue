<template>
  <div id="profileIcon" @click="toggleDropdown" class="profile__icon__container">
    <svg v-if="!getProfilePic" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 profile__icon" fill="#3F6078" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <img v-if="getProfilePic" :src="getProfilePic" alt="profile picture" />
    <div v-if="isLoggedIn && getStatus === 'online'" :class="'profile_online_indicator ' + indicatorStyle"></div>
  </div>
</template>


<script>

  import { mapState, mapActions, mapGetters, mapMutations } from 'vuex';

  export default {

    name: 'ProfileIcon',

    props: {
      location: String,
    },

    computed: {

    indicatorStyle () {

        if (this.getStatus === 'online' && this.location !== 'dropDown') {
          return 'user_status_indicator_online';
        } else if(this.getStatus === 'offline') {
          return 'user_status_indicator_online';
        } else {
          return '';
        }
      },
        ...mapGetters('user',
        [
          'userName',
          'getProfileStatus',
          'getProfilePic',
          'isLoggedIn',
          'getStatus',
        ]
      ),
    },

    methods: {
      ...mapMutations('profileDropdown',
        [
          'TOGGLE_PROFILE_DROPDOWN',
          'CLOSE_PROFILE_DROPDOWN'
        ]
      ),

      toggleDropdown () {

        this.TOGGLE_PROFILE_DROPDOWN();
      }
    },
  }

</script>


<style lang="scss">

  .profile__icon__container {
    position: relative;
    cursor: pointer;

    svg {
      fill: $themePink;
      pointer-events: none;
    }

    img {
      height: 42px;
      width: 42px;
      border-radius: 50%;
      pointer-events: none;
    }
  }

  .profile__icon {
    color: $themeLightBlue;
    height: 42px;
    width: 42px;
    pointer-events: none;
  }

  .profile_online_indicator {
    border-radius: 50%;
    position: absolute;
    bottom: 10px;
    right: 2px;
    pointer-events: none;
  }

</style>