import axios from 'axios';

const initialState = () => {

  return {

    navigationLinks: [
      { component: 'Home', linkText: 'home' },
      { component: 'Login', linkText: 'login' },
      { component: 'CreateAccount', linkText: 'create account' },
      { component: 'AboutLooped', linkText: 'about' },
      { component: 'Reviews', linkText: 'Reviews' },
    ],
    authNavigationLinks: [
      { component: 'Home', linkText: 'home' },
      { component: 'AboutLooped', linkText: 'about' },
      { component: 'Reviews', linkText: 'Reviews' },
    ],
    notificationsAreOpen: false,
  }
};

const navigation = {

  namespaced: true,

  state: initialState(),



  getters: {

  },

  mutations: {

    TOGGLE_NOTIFICATIONS: (state) => {
      state.notificationsAreOpen = !state.notificationsAreOpen;
    },

    CLOSE_NOTIFICATIONS: (state) => {
      state.notificationsAreOpen = false;
    },

    RESET_NAV_MODULE(state) {
      Object.assign(state, initialState());
    },
  },

  actions: {

  }
}

export default navigation;