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

import { mapState, mapGetters, mapMutations } from 'vuex';

export default {



    name: "App",

    components: {
        Navbar: () => import("./components/Navigation/Navbar.vue"),
        ProfileDropdown: () => import('./components/Dropdowns/ProfileDropdown.vue'),
        MessengerTrigger: () => import('./components/messenger/MessengerTrigger.vue'),
        MessengerContainer: () => import('./components/messenger/MessengerContainer.vue'),
        Footer: () => import('./components/Footer.vue'),
    },


    mounted() {
        if (this.getStatus === 'online') {
            this.initUserStatusChannel();
        }
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
                'getProfileStatus',
                'getStatus',
                'getToken',
            ]
        ),
    },

    methods: {
        ...mapMutations('messenger',
            [
                'UPDATE_CONTACT_STATUS',
            ]
        ),
      initUserStatusChannel() {
            Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
            Echo.join('userstatus')
            .joining((user) => {
              this.UPDATE_CONTACT_STATUS({...user, status: 'online', page: 'App.vue'});
            })
            .leaving((user) => {
              this.UPDATE_CONTACT_STATUS({...user, status: 'offline', page: 'App.vue'});
            })
            .error((error) => {
                console.log('Channel Error: ', error);
            });
      },
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
