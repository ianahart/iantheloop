<template>
    <div class="navbar__parent">
     <NavbarDesktop
      v-if="!isMenuVisible"
    />
    <NavbarMobile
      v-if="isMenuVisible"
    />
    </div>
</template>

<script>


  import { mapState, mapMutations } from 'vuex';
  import NavbarDesktop from './NavbarDesktop';
  import NavbarMobile from './NavbarMobile';

  export default {

    name: 'Navbar',

    props: {

    },

    components: {

      NavbarDesktop,
      NavbarMobile,
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
    computed: {

        ...mapState('hamburgerMenu',
          [
            'isMenuIconVisible',
            'isMenuVisible'
          ]
        ),
    },
    methods: {

        ...mapMutations('profileDropdown',
          [
            'CLOSE_PROFILE_DROPDOWN'
          ]
        ),
        ...mapMutations('navigation',
          [
            'CLOSE_NOTIFICATIONS'
          ]
        ),

      onResize () {

        if (window.innerWidth <= 600) {
          this.$store.commit('hamburgerMenu/SHOW_MENU_ICON', true);
          this.CLOSE_PROFILE_DROPDOWN(false);
          this.CLOSE_NOTIFICATIONS();
        }

        if (window.innerWidth >= 600) {
          this.$store.commit('hamburgerMenu/HIDE_MENU_ICON', false);
          this.$store.commit('hamburgerMenu/HIDE_MENU', false);
          this.CLOSE_NOTIFICATIONS();


        }
      }
    },
  };

</script>

<style lang="scss">


</style>