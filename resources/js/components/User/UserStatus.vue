<template>
  <div class="dropdown_user_status__container">
    <div class="user_status_flex_item">
      <p>{{ status }}</p>
      <p :class="indicatorStyle"></p>
    </div>
    <div @click="toggleUserStatus"  :class="`status_toggle_btn__container ${toggleBorder}`">
      <span v-if="status === 'online'">&check;</span>
      <div :class="`status_toggle_btn ${toggleBtn}`"></div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  export default {

    name: 'UserStatus',

    props: {
      status: String,
    },

    created () {
      this.toggleUserStatus = debounce(this.toggleUserStatus, 350);
      if (this.status === 'online') {

        this.SET_INITIAL_TOGGLE_VALUE(true);
      } else if (this.status === 'offline') {
        this.SET_INITIAL_TOGGLE_VALUE(false);
      }
    },

    computed: {
      indicatorStyle () {
        return this.status === 'online' ? 'user_status_indicator_online' : 'user_status_indicator_offline';
      },
      toggleBtn() {

        return this.status === 'online' ? 'status_toggle_btn_online' : 'status_toggle_btn_offline';
      },
      toggleBorder () {
        return this.status === 'online' ? 'status_toggle_btn_container_online' : 'status_toggle_btn_container_offline';
      },

      ...mapGetters('user',
        [
          'getStatus',
          'getToken',
          'getUserId',
        ]
      ),
      ...mapState('user',
        [
        'statusToggledBtn',
        ]
      ),
    },

    methods: {

      ...mapActions('user',
        [
          'UPDATE_USER_STATUS',
          'SET_INITIAL_STATUS',
        ]
      ),

      ...mapMutations('user',
        [
          'TOGGLE_STATUS_BTN',
          'SET_INITIAL_TOGGLE_VALUE',
        ]
      ),

      ...mapMutations('conversator',
        [
          'UPDATE_CONTACT_STATUS',
          'UPDATE_UNREAD_MESSAGE_COUNT',
        ]
      ),

        async toggleUserStatus () {
          try {
            this.TOGGLE_STATUS_BTN();
              await this.UPDATE_USER_STATUS();
              if (this.getStatus === 'online') {
                this.listenForUserStatusChange();
                this.reJoinNotificationChannel();
              } else {
                Echo.leave('userstatus');
                Echo.leave(`notifications.${this.getUserId}`);
              }
          } catch(e) {
          }
        },
        listenForUserStatusChange() {
          Echo.join('userstatus')
            .joining((user) => {
              this.UPDATE_CONTACT_STATUS({...user, status: 'online' });
            })
            .leaving((user) => {
              this.UPDATE_CONTACT_STATUS({...user, status: 'offline'});
            })
            .error((error) => {
              console.log('Channel Error: ', error);
            });
        },
        reJoinNotificationChannel() {
          Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
          Echo.private(`notifications.${this.getUserId}`)
          .notification((notification) => {
             if (notification.type === 'broadcast.message') {
                  console.log('Login.vue: notification broadcast.message');
                  this.UPDATE_UNREAD_MESSAGE_COUNT(notification);
                  return;
              } else if (notification.type === 'broadcast.interaction') {
                console.log('Login.vue: notification broadcast.intereaction');

              } else {
                 console.log('Login.vue: Line 106: Notification Not recieved: ');
              }
        });
        }
    },
  }
</script>

<style lang="scss">

.dropdown_user_status__container {
  display: flex;
  align-items: center;

  p {
    color: $primaryWhite;
    font-size: 0.8rem;
    font-weight: 100;
  }
}

.user_status_flex_item {
  pointer-events: none;
  display:flex;
  align-items: center;
}

.status_toggle_btn__container {
  position: relative;
  border-radius: 16px;
  width: 50px;
  height: 25px;
  margin-left: auto;
  cursor: pointer;
  padding: 0.1rem;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  background-color: #000;

  span {
    color: $themeLightBlue;
    pointer-events: none;
  }
}

.status_toggle_btn {
  box-sizing: border-box;
  padding: 0.1rem;
  top: 0;
  left:0;
  border-radius: 50px;
  height: 85%;
  width: 15px;
}

.status_toggle_btn_online {
  background-color: $themeLightBlue;
  transition: all 0.4s ease-out;
  transform: translateX(17px);
  pointer-events: none;
}

.status_toggle_btn_offline {
  background-color: $primaryWhite;
  transition: all 0.4s ease-out;
  pointer-events: none;
}

.status_toggle_btn_container_online {
  border: 2px solid $themeLightBlue;
  transition: all 0.4s ease-out;
}

.status_toggle_btn_container_offline {
  border: 2px solid $primaryWhite;
  transition: all 0.4s ease-out;
}


</style>