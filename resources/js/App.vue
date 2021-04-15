<template>
  <div class="app">
    <Navbar />
    <router-view></router-view>
  </div>
</template>

<script>

 import './sass/app.scss';

 export default {

   name: 'App',

   components: {

     Navbar: () => import('./components/Navbar.vue'),
   },
 };

</script>
