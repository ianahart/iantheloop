<template>
<div class="app__container">
    <div class="app__content">
      <Navbar />
      <ProfileDropdown
        v-if="isProfileDropdownOpen"
      />
      <router-view></router-view>
    </div>
    <MessengerTrigger
        v-if="getProfileStatus"
    />
    <MessengerContainer
        v-if="getProfileStatus && isMessengerOpen"
    />
    <Footer />
</div>
</template>

<script>

import '../sass/app.scss';

import { mapState, mapGetters } from 'vuex';

export default {



    name: "App",

    components: {
        Navbar: () => import("./components/Navigation/Navbar.vue"),
        ProfileDropdown: () => import('./components/Dropdowns/ProfileDropdown.vue'),
        MessengerTrigger: () => import('./components/messenger/MessengerTrigger.vue'),
        MessengerContainer: () => import('./components/messenger/MessengerContainer.vue'),
        Footer: () => import('./components/Footer.vue'),
    },

    computed: {

        ...mapState('profileDropdown',
            [
                'isProfileDropdownOpen'
            ]
          ),
        ...mapState('messenger',
           [
               'isMessengerOpen',
           ]
         ),
      ...mapGetters('user',
            [
                'getProfileStatus'
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
