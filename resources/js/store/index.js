import Vue from 'vue';
import Vuex from 'vuex';

/*
MODULES
*/


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

Vue.use(Vuex);

export default new Vuex.Store(
  {

    state: {

      isPasswordShowing: false,
    },

    mutations: {

      CHANGE_PASSWORD_ICON: (state) => {

        state.isPasswordShowing = !state.isPasswordShowing;
      },
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
    },
  }
);

