<template>
  <svg id="notification___icon" xmlns="http://www.w3.org/2000/svg" :class="`h-6 w-6 ${className}`" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
  </svg>
</template>

<script>

  import { mapState, mapActions, mapGetters, mapMutations } from 'vuex';

  export default {

    name: 'NotificationsIcon',

    props: {
      className: String,
    },

    components: {

    },

    data () {

      return {

      }
    },

    mounted () {

    },

    created () {

    },

    computed: {

    },

    methods: {
    },
  }

</script>


<style lang="scss">

#notification___icon {
  pointer-events: none;
  cursor: pointer;
  }

</style>