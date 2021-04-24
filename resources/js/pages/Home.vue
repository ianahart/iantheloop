<template>
  <div class="home-page">
    <h1>HOME PAGE</h1>
    <!-- <AuthHome /> -->
    <NoAuthHome />
    <router-link :to="{name: 'NewsFeed'}">News feed</router-link>
  </div>
</template>

<script>

  import AuthHome from '../components/Home/AuthHome.vue';
  import NoAuthHome from '../components/Home/NoAuthHome.vue';

  export default {

    name: 'Home',

    components: {

      AuthHome,
      NoAuthHome,
    },

    props: {

    },

    created () {

    },

    mounted () {

    },

    methods: {

    },
  }

</script>