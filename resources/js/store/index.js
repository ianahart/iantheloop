import Vue from 'vue';
import Vuex from 'vuex';

/*
MODULES
*/

import auth from './modules/auth.js';
import hamburgerMenu from './modules/hamburgerMenu.js';
import navigation from './modules/navigation.js';

Vue.use(Vuex);

export default new Vuex.Store(
  {
    modules: {
      auth,
      navigation,
      hamburgerMenu,
    },
  }
);

