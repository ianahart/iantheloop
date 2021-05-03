<template>
  <div class="create_profile_sidebar__icon">
<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="path" />
</svg>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'SidebarIcon',

    props: {
      path: String,
    },

    components: {

    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },

    computed: {

    },

    methods: {

    },
  }
</script>

<style lang="scss">

  @import '../../../sass/general/_variables.scss';

.create_profile_sidebar__icon {
  margin-right: 0.7rem;
  display: inline-block;




    svg {
        display: inline-block;
        vertical-align: middle;
      width: 25px;
      height: 25px;
      color: $primaryBlack;
    }
  }

</style>