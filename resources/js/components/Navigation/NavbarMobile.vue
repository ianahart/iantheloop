<template>
  <transition name="fade" appear>
    <nav class="navbar__mobile">
      <CloseIcon />
      <div class="navigation__links_container">
        <LogoName />
        <NavigationLinks
          v-if="!isLoggedIn"
          rootStyle="nav__links__mobile"
        />
        <AuthNavigationLinks
          v-if="isLoggedIn"
          rootStyle="nav__links__mobile"
        />
      </div>
    </nav>
  </transition>
</template>


<script>

  import { mapGetters } from 'vuex';

  import HamburgerIcon from '../Icons/HamburgerIcon'
  import LogoName from '../Icons/LogoName';
  import CloseIcon from '../Icons/CloseIcon';
  import NavigationLinks from './NavigationLinks';
  import AuthNavigationLinks from './AuthNavigationLinks';

  export default {

    name: 'NavbarMobile',

    props: {

    },

    components: {

      HamburgerIcon,
      CloseIcon,
      LogoName,
      NavigationLinks,
      AuthNavigationLinks,
    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },

    beforeDestroy () {

    },

    computed: {

      ...mapGetters('user',
          [
            'isLoggedIn',
          ]
      )
    },

    methods: {

    }
  };

</script>



<style lang="scss">
/*
IMPORTS
*/
  @import '../../../sass/general/_variables.scss';
  @import '../../../sass/general/_base.scss';

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
  .navbar__mobile {
    background-color: $primaryBlack;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 10;
    box-shadow: 0px 0px 9px 3px rgba(41,41,41,.25);


    div {
      display: flex;
      justify-content: center;
      text-align: center;
      margin: 0 auto;
    }
    }

    .nav__links__mobile {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0;
      width: 80%;

    li {
      margin-top: 0.5rem;
      list-style-type: none;
      transition: all 0.25s ease-in-out;
      &:hover {
        border-bottom: 3px solid $themeGold;
      }
    }

    a {
      font-size: 0.85rem;
      color: $primaryWhite;
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      letter-spacing: 0.1rem;
      font-weight: 100;
      text-decoration: none;
      text-transform: uppercase;
    }
  }

  .navigation__links_container {

    display: flex;
    flex-direction: column;
    align-items: center;
  }



</style>