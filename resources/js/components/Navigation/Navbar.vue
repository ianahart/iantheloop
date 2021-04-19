<template>
    <div class="navbar__parent">
      <EntryNavbar />
      <!-- <AuthNavbar /> -->
    </div>
</template>

<script>

  import EntryNavbar from './EntryNavbar.vue';
  import AuthNavbar from './AuthNavbar.vue';

  export default {

    name: 'Navbar',

    props: {

    },

    components: {

      EntryNavbar,
      AuthNavbar,
    },

    data () {

      return {

      }
    },

    created () {

      this.onResize();
    },

    mounted () {


      window.addEventListener('resize', this.onResize);
    },

    beforeDestroy() {

      window.removeEventListener('resize', this.onResize);
    },

    methods: {

      onResize () {

        if (window.innerWidth <= 600) {

          this.$store.commit('hamburgerMenu/SHOW_MENU_ICON', true);
        }

        if (window.innerWidth >= 600) {

          this.$store.commit('hamburgerMenu/HIDE_MENU_ICON', false);

          this.$store.commit('hamburgerMenu/HIDE_MENU', false);
        }
      }
    },
  };

</script>

<style lang="scss">
/*
IMPORTS
*/
   @import '../../../sass/general/_variables.scss';
  @import '../../../sass/general/_base.scss';

  .navbar__parent {

      height: 55px;
  }

</style>