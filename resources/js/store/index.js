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
    },
  }
);

