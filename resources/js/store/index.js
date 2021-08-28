import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

import user from './modules/user.js';
import login from './modules/login.js';
import navigation from './modules/navigation.js';
import hamburgerMenu from './modules/hamburgerMenu.js';
import createAccount from './modules/createAccount.js';
import newsFeed from './modules/newsFeed.js';
import profileDropdown from './modules/profileDropdown.js';
import passwordRecovery from './modules/passwordRecovery.js';
import createProfile from './modules/createProfile/createProfile.js';
import generalDetails from './modules/createProfile/generalDetails.js';
import aboutDetails from './modules/createProfile/aboutDetails.js';
import identity from './modules/createProfile/identity.js';
import workDetails from './modules/createProfile/workDetails.js';
import customize from './modules/createProfile/customize.js';
import profile from './modules/profile/profile.js';
import profileAbout from './modules/profile/profileAbout.js';
import profileEdit from './modules/profile/profileEdit.js';
import network from './modules/network.js';
import posts from './modules/posts.js';
import profileWallSettings from './modules/profileWallSettings.js';
import notifications from './modules/notifications.js';
import messenger from './modules/messenger.js';

Vue.use(Vuex);
export default new Vuex.Store(
  {


    state: {

      isPasswordShowing: false,
      userCount: 0,
    },

    mutations: {
      CHANGE_PASSWORD_ICON: (state) => {
        state.isPasswordShowing = !state.isPasswordShowing;
      },
      SET_USER_COUNT: (state, count) =>{
        state.userCount = count;
      }
    },

    actions: {
      async RETRIEVE_USER_COUNT({ state, commit }) {
        try {

          const response = await axios(
            {
             method: 'GET',
             url: '/api/auth/users/count',
             headers: {
               'Accept': 'application/json',
               'Content-Type': 'application/json',
             }
            });
           console.log('index.js RETRIEVE_USER_TOTAL SUCC: ', response);
           commit('SET_USER_COUNT', response.data.count);
        } catch(e) {
          console.log('index.js RETRIEVE_USER_TOTAL ERROR: ', e.response);
        }
      }
    },


    modules: {
      user,
      login,
      navigation,
      hamburgerMenu,
      createAccount,
      newsFeed,
      profileDropdown,
      passwordRecovery,
      createProfile,
      generalDetails,
      aboutDetails,
      identity,
      workDetails,
      customize,
      profile,
      profileAbout,
      profileEdit,
      network,
      posts,
      profileWallSettings,
      notifications,
      messenger,
    },
  }
);

