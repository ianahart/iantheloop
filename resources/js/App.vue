<template>
<div class="app__container">
    <div class="app__content">
      <Navbar />
      <ProfileDropdown
        v-if="isProfileDropdownOpen"
      />
      <router-view></router-view>
    </div>
    <Footer />
</div>
</template>

<script>
/*
SASS
*/
import '../sass/app.scss';

import { mapState } from 'vuex';

export default {



    name: "App",

    components: {

        Navbar: () => import("./components/Navigation/Navbar.vue"),
        ProfileDropdown: () => import('./components/Dropdowns/ProfileDropdown.vue'),
        Footer: () => import('./components/Footer.vue'),
    },

    mounted() {

    },


    computed: {

        ...mapState('profileDropdown',
            [
                'isProfileDropdownOpen'
            ]
          ),
    },

    methods: {

    },
};
</script>

<style lang="scss">

    #app, html
     {
        height: 100%;
    }

    body {
        min-height: 100;
    }

    .app__content {
        flex: 1;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .app__container {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        height: 100%;

    }
</style>
