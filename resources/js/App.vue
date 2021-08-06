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

import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

export default {



    name: "App",

    components: {
        Navbar: () => import("./components/Navigation/Navbar.vue"),
        ProfileDropdown: () => import('./components/Dropdowns/ProfileDropdown.vue'),
        MessengerTrigger: () => import('./components/messenger/MessengerTrigger.vue'),
        MessengerContainer: () => import('./components/messenger/MessengerContainer.vue'),
        Footer: () => import('./components/Footer.vue'),
    },


    async mounted() {
        if (this.getStatus === 'online') {
            this.initUserStatusChannel();
            this.initInteractionChannel();
            await this.FETCH_NAV_NOTIFICATION_ALERTS({ userId: this.getUserId, type: 'App/Notifications/UnreadMessage' });
        }
    },

    beforeDestroy() {
        this.SET_NAV_ALERTS({ nav_interaction_alerts: false, nav_message_alerts: false });
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
                'getUserId',
            ]
        ),
    },

    methods: {
        ...mapMutations('messenger',
            [
                'UPDATE_CONTACT_STATUS',
            ]
        ),
        ...mapMutations('notifications',
            [
                'SET_NAV_ALERTS'
            ]
        ),
        ...mapActions('notifications',
            [
                'FETCH_NAV_NOTIFICATION_ALERTS'
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

      initInteractionChannel() {
          console.log('App.vue: Line 106: Subscribed to InteractionChannel...');
          Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
          Echo.private(`notifications.${this.getUserId}`)
          .notification((notification) => {
              if (notification.type === 'broadcast.interaction') {
                console.log('App.vue: Line 106: Interaction: ', notification);
              }
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
