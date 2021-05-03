<template>
  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 chevron__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
  </svg>
</template>

<script>

  export default {

    name: 'ChevronIcon',

    props: {

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

    }
  }

</script>


<style lang="scss">

  @import '../../../sass/general/_variables.scss';


  .chevron__icon {
    height: 15px;
    width: 15px;
    color: $themeLightBlue;
    padding: 0 0.4rem;
    pointer-events: none;
  }
</style>


