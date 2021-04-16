<template>
  <nav class="navbar__entry__desktop">
    <div class="logo__container">
      <Logo />
      <HamburgerIcon />
    </div>
    <p
      v-if="isMenuIconVisible"
    >
    &nbsp;
   </p>
    <NavigationLinks
      v-if="!isMenuIconVisible"
      rootStyle="entry__nav__links__desktop"
    />
  </nav>
</template>


<script>

  import { mapState } from 'vuex';

  import HamburgerIcon from '../Icons/HamburgerIcon';
  import Logo from '../Icons/Logo';
  import NavigationLinks from './NavigationLinks';

  export default {

    name: 'EntryNavbarDesktop',

    props: {

    },

    components: {

      HamburgerIcon,
      Logo,
      NavigationLinks,
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
  @import '../../sass/general/_variables.scss';
  @import '../../sass/general/_base.scss';


  .navbar__entry__desktop {

    background-color: $primaryBlack;
    display: flex;
    justify-content: space-evenly;
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

  .entry__nav__links__desktop {
      display: flex;
      justify-content: center;
      padding: 0;
      width: 80%;
      height: 25px;
      margin-bottom: 0;

    li {
      margin: 0 0.5rem;
      list-style-type: none;
      transition: all 0.25s ease-in-out;
      &:hover {
        border-bottom: 3px solid $themeGold;

      }
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
    }
  }

@media(max-width: 600px) {

  .logo__container {
    width: 90%;
  }
}

</style>