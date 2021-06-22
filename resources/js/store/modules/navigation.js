import axios from 'axios';

const initialState = () => {

  return {

    navigationLinks: [
      { component: 'Home', linkText: 'home' },
      { component: 'Login', linkText: 'login' },
      { component: 'CreateAccount', linkText: 'create account' },
      { component: 'AboutLooped', linkText: 'about' },
    ],
    authNavigationLinks: [
      { component: 'Home', linkText: 'home' },
      { component: 'AboutLooped', linkText: 'about' },
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
    async FETCH_FOLLOW_REQUESTS({ state, commit }, payload) {

      try {

        const response = await axios(
          {
            method: 'GET',
            url: `/api/auth/follow-requests/index?userId=${payload}`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );

      } catch (e) {


      }
    }
  }
}

export default navigation;