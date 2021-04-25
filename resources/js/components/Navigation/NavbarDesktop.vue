<template>
  <nav class="navbar__desktop">
    <div class="logo__container">
      <LogoName />
      <HamburgerIcon />
    </div>
    <p
      v-if="isMenuIconVisible"
    >
    &nbsp;
   </p>
    <NavigationLinks
      v-if="!isMenuIconVisible && !isLoggedIn"
      rootStyle="nav__links__desktop"
    />
    <AuthNavigationLinks
      v-if="!isMenuIconVisible && isLoggedIn"
      rootStyle="nav__links__desktop"
    />
  </nav>
</template>


<script>

  import { mapState, mapGetters } from 'vuex';

  import HamburgerIcon from '../Icons/HamburgerIcon';
  import LogoName from '../Icons/LogoName';
  import NavigationLinks from './NavigationLinks';
  import AuthNavigationLinks from './AuthNavigationLinks';

  export default {

    name: 'NavbarDesktop',

    props: {

    },

    components: {

      HamburgerIcon,
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
      ),

      ...mapState('navigation',
        [
          'navigationLinks'
        ]
      ),
      ...mapState('hamburgerMenu',
        [
          'isMenuIconVisible'
        ]
      ),
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


  .navbar__desktop {

    background-color: $primaryBlack;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.1rem 0.7rem;

      p {
        margin: 1rem auto;
        text-align: center;
      }
    }

    .logo__container {
      display: flex;
      justify-content: space-between;
    }

  .nav__links__desktop {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0;
      width: 80%;
      margin-bottom: 0;

    li {
      margin: 0 0.5rem;
      list-style-type: none;

      &:first-of-type {
        margin-right: auto;
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
      transition: all 0.25s ease-in-out;
      &:hover {
        border-bottom: 3px solid $themeGold;

      }
      &:first-of-type {
        margin-right: auto;
      }
    }
  }

@media(max-width: 600px) {

  .logo__container {
    width: 90%;
  }
}

</style>