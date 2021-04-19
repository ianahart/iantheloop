<template>

  <div class="entry__navbar">
    <EntryNavbarDesktop
      v-if="!isMenuVisible"
    />
    <EntryNavbarMobile
      v-if="isMenuVisible"
    />
  </div>
</template>


<script>

  import { mapState } from 'vuex';

  import EntryNavbarDesktop from './EntryNavbarDesktop.vue';
  import EntryNavbarMobile from './EntryNavbarMobile.vue';

  export default {

    name: 'EntryNavbar',

    props: {

    },

    components: {

      EntryNavbarDesktop,
      EntryNavbarMobile,
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

        ...mapState('hamburgerMenu',
          [
            'isMenuIconVisible',
            'isMenuVisible'
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

  .entry__navbar {
    position: relative;
    height: 100%;

  }

</style>