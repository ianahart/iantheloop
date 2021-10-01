<template>
  <div class="login__container">
    <div class="login__form__container">
      <form @submit.prevent="submitForm" class="login__form">
        <Header />
        <InputFieldLg
          v-for="(inputField, index) in form"
          :key="index"
          :field="inputField.field"
          :type="inputField.type"
          :errors="inputField.errors"
          :label="inputField.label"
          :value="inputField.value"
          :nameAttr="inputField.namaeAttr"
          :commitPath="'login/UPDATE_FIELD'"
        />
        <div class="login__button__container">
          <button class="button__md">Login Now</button>
          <router-link class="forgot__password__link" :to="{name: 'ForgotPassword'}">Forgot password?</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

  import { mapActions, mapGetters, mapMutations, mapState } from 'vuex';

  import Header from '../components/Login/Header.vue';
  import InputFieldLg from '../components/forms/inputs/InputFieldLg.vue';

  export default {

    name: 'Login',

    components: {

      Header,
      InputFieldLg,
    },


    created () {

      if(this.$route.query.signup) {

          this.clearRegistration();
      }
    },


    beforeDestroy() {

      this.RESET_LOGIN_MODULE();
    },

    computed: {

      ...mapState('login',
        [
          'form',
          'hasErrors',
          'formSubmitted',
        ]
      ),
      ...mapState('conversator',
        [
          'isConversatorOpen'
        ]
      ),
      ...mapGetters('user',
        [
          'getToken',
          'getUserId',
          'getUserSlug',
          'getProfileStatus',
        ]
      ),
    },

    methods: {

      ...mapMutations('createAccount',
        [
          'RESET_MODULE',
        ]
      ),
      ...mapMutations('login',
        [
          'UPDATE_FIELD',
          'CLEAR_ERROR_MSGS',
          'RESET_LOGIN_MODULE'
        ],
      ),
      ...mapMutations('user',
          [
            'SET_AUTH_STATUS',
          ]
        ),
      ...mapMutations('conversator',
        [
          'UPDATE_CONTACT_STATUS',
          'UPDATE_UNREAD_MESSAGE_COUNT',
        ]
      ),
      ...mapMutations('notifications',
        [
          'SET_CURRENT_INTERACTION_ALERT',
          'SET_CURRENT_INTERACTION_ALERT_ACTIVE',
          'PROCESS_ENQUEUE',
        ]
      ),
      ...mapActions('login',
        [
          'SUBMIT_FORM',
        ]
      ),
      ...mapActions('notifications',
        [
          'FETCH_NAV_NOTIFICATION_ALERTS'
        ]
      ),

      clearRegistration() {

        if (this.$route.query.signup) {

            this.RESET_MODULE();
        }
      },

      checkEmptyInputs() {

        this.form.forEach(({ field, value }) => {
          if (value.trim().length <= 0) {
            const error = field + ' is required';
            this.UPDATE_FIELD({field, value, error})
          }
        })
      },

      async submitForm () {
        this.CLEAR_ERROR_MSGS();
        this.checkEmptyInputs();

        if (!this.hasErrors) {
          await this.SUBMIT_FORM();
        }

        if (this.formSubmitted) {
          this.SET_AUTH_STATUS(true);
          this.trackUserStatus();
          this.initNotificationChannel();
          this.RESET_LOGIN_MODULE();

          if (this.getProfileStatus) {
              this.$router.push({ name: 'NewsFeed', params: {slug: this.getUserSlug}});
          } else {
              this.$router.push({ name: 'Home' });
          }
          await this.FETCH_NAV_NOTIFICATION_ALERTS({ userId: this.getUserId, type: ['App/Notifications/UnreadMessage', 'App/Notifications/Interaction'] });
        }
      },

      trackUserStatus() {
        Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
        Echo.join('userstatus').here((user) => {
        })
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
          initNotificationChannel() {
          Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${this.getToken}`;
          Echo.private(`notifications.${this.getUserId}`)
          .notification((notification) => {
             if (notification.type === 'broadcast.message') {
                  this.UPDATE_UNREAD_MESSAGE_COUNT(notification);
                  return;
              } else if (notification.type === 'broadcast.interaction') {
                  this.SET_CURRENT_INTERACTION_ALERT_ACTIVE(true);
                  this.SET_CURRENT_INTERACTION_ALERT(notification);

              } else {
                 console.log('Login.vue: Notification Not recieved: ');
              }
        });
      },

    },
  }

</script>

<style lang="scss">

  .login__container {
    box-sizing: border-box;
    background-image: url('../../assets/login.svg');
    width: 100%;
    height: 100%;
    background-size: cover;
    display: flex;
    justify-content: center;
  }

  .login__form__container {
    width: 448px;
    margin-bottom: 3rem;
    box-sizing: border-box;
    margin-top: 6rem;
  }

  .login__form {
    width: 100%;
    height: auto;
    box-sizing: border-box;
    padding: 1rem;
    background-color: $primaryWhite;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    border-radius: 8px;
  }

 .login__button__container {
   display: flex;
   flex-direction: column;
   align-items: center;
   justify-content: center;
   margin: 3rem auto;

   button {
     width: 150px;
   }
 }

 .forgot__password__link {
   font-size: 0.8rem;
   text-decoration: none;
   color: darken($themeDarkGreen, 20);

   &:hover {
     opacity: 0.7;
   }
 }

</style>

  .login__container {
    box-sizing: border-box;
    background-image: url('../../assets/login.svg');
    width: 100%;
    height: 100%;
    background-size: cover;
    display: flex;
    justify-content: center;
  }

  .login__form__container {
    width: 448px;
    margin-bottom: 3rem;
    box-sizing: border-box;
    margin-top: 6rem;
  }

  .login__form {
    width: 100%;
    height: auto;
    box-sizing: border-box;
    padding: 1rem;
    background-color: $primaryWhite;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    border-radius: 8px;
  }

 .login__button__container {
   display: flex;
   flex-direction: column;
   align-items: center;
   justify-content: center;
   margin: 3rem auto;

   button {
     width: 150px;
   }
 }

 .forgot__password__link {
   font-size: 0.8rem;
   text-decoration: none;
   color: darken($themeDarkGreen, 20);

   &:hover {
     opacity: 0.7;
   }
 }

</style>