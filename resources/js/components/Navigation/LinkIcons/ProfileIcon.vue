<template>
  <div @click="toggleDropdown" class="profile__icon__container">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 profile__icon" fill="#3F6078" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div class="profile_online_indicator"></div>
  </div>
</template>


<script>

  import { mapState, mapActions, mapGetters, mapMutations } from 'vuex';

  export default {

    name: 'ProfileIcon',

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

    svg {
      fill: $themeDarkGreen;
    }
  }

  .profile__icon {
    color: $mainInputLabel;
    height: 42px;
    width: 42px;
    cursor: pointer;
  }

  .profile_online_indicator {
    height: 6px;
    width: 6px;
    border-radius: 50%;
    background-color: limegreen;
    position: absolute;
    bottom: 10px;
    right: 2px;
    border: 2px solid #000;
  }

</style>