<template>
  <transition name="fade" appear>
    <nav class="navbar__entry__mobile">
      <CloseIcon />
      <div class="navigation__links_container">
        <Logo />
        <NavigationLinks
          rootStyle="entry__nav__links__mobile"
        />
      </div>
    </nav>
  </transition>
</template>


<script>

  import HamburgerIcon from '../Icons/HamburgerIcon'
  import Logo from '../Icons/Logo';
  import CloseIcon from '../Icons/CloseIcon';
  import NavigationLinks from './NavigationLinks';

  export default {

    name: 'EntryNavbarMobile',

    props: {

    },

    components: {

      HamburgerIcon,
      CloseIcon,
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
  .navbar__entry__mobile {
    background-color: $primaryBlack;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 300px;
    z-index: 10;
    box-shadow: 0px 0px 9px 3px rgba(41,41,41,.25);


    div {
      display: flex;
      justify-content: center;
      text-align: center;
      margin: 0 auto;
    }
    }

    .entry__nav__links__mobile {
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