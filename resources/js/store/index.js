import Vue from 'vue';
import Vuex from 'vuex';

/*
MODULES
*/

import auth from './modules/auth.js';
import user from './modules/user.js';
import login from './modules/login.js';
import navigation from './modules/navigation.js';
import hamburgerMenu from './modules/hamburgerMenu.js';
import createAccount from './modules/createAccount.js';
import newsFeed from './modules/newsFeed.js';

Vue.use(Vuex);

export default new Vuex.Store(
  {
    modules: {
      auth,
      user,
      login,
      navigation,
      hamburgerMenu,
      createAccount,
      newsFeed,
    },
  }
);

