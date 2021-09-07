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
            this.reInitNotificationChannel();
            this.reInitStoriesChannel();

            await this.FETCH_NAV_NOTIFICATION_ALERTS({ userId: this.getUserId, type: ['App/Notifications/UnreadMessage', 'App/Notifications/Interaction'] });
        }
    },

    beforeDestroy() {
        this.SET_NAV_ALERTS({ nav_interaction_alerts: 0, nav_message_alerts: false });
        this.leave(`notifications.${this.getUserId}`);
        Echo.leave(`stories.${this.getUserId}`);
    },

    computed: {

      ...mapState('stories', ['stories']),

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
                'UPDATE_UNREAD_MESSAGE_COUNT',
            ]
        ),
        ...mapMutations('notifications',
            [
                'SET_NAV_ALERTS',
                'SET_CURRENT_INTERACTION_ALERT',
                'SET_CURRENT_INTERACTION_ALERT_ACTIVE'
            ]
        ),
        ...mapMutations('stories',
            [
               'SET_STORIES',
               'SET_CURRENT_USER_STORIES',
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

      reInitStoriesChannel() {
        Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
        Echo.private(`stories.${this.getUserId}`)
            .listen('StoryPhotoProcessed', (event) => {
                if (parseInt(event.data.story.user_id) === parseInt(this.getUserId)) {
                      this.SET_CURRENT_USER_STORIES([event.data]);
                } else {
                    this.SET_STORIES([event.data]);
                }
            });
      },

      reInitNotificationChannel() {
          Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
          Echo.private(`notifications.${this.getUserId}`)
          .notification((notification) => {
                if (notification.type === 'broadcast.message') {
                        this.UPDATE_UNREAD_MESSAGE_COUNT(notification);
                        return;
              } else if (notification.type === 'broadcast.interaction') {
                       this.SET_CURRENT_INTERACTION_ALERT(notification);
                       this.SET_CURRENT_INTERACTION_ALERT_ACTIVE(true);
              } else {
                 console.log('App.vue: Notification Not recieved: ');
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
