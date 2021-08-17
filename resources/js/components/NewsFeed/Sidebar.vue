<template>
  <div class="newsfeed_sidebar_container">
    <h3>Sidebar Container</h3>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {
    name: 'Sidebar',
    props: {

    },
    components: {

    },
    data () {
      return {

      }
    },
    mounted() {

    },
    beforeDestroy() {

    },
    computed: {

    },
    methods: {

    },
  }


</script>

<style lang="scss">
  .newsfeed_sidebar_container {
    margin: 0.5rem;
    box-sizing: border-box;
    border-radius: 0.25rem;
    background-color: #3b3b441f;
    border: 2px solid red;
    flex-grow: 1;
    max-width: 500px;
  }

  @media (max-width:600px) {
    .newsfeed_sidebar_container {
      flex-grow: 1;
      width:100%;
      max-width: 100%;
      margin: 0;
    }
  }

</style>