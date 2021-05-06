<template>
    <svg @click="openMenu" v-if="isMenuIconVisible"  xmlns="http://www.w3.org/2000/svg" :class="`h-6 w-6 ${className}`" fill="none" viewBox="0 0 24 24" stroke="currentColor">
       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</template>




<script>

  import { mapState } from 'vuex';

  export default {

    name: 'HamburgerIcon',

    props: {
      className: String,
    },


    computed: {

      ...mapState( 'hamburgerMenu',
        [
          'isMenuIconVisible',
        ]
      ),
    },

    methods: {

      openMenu() {

        this.$store.commit('hamburgerMenu/OPEN_MENU', true);
      }
    }
  };

</script>


<style lang="scss">
/*
  IMPORTS
*/


</style>

