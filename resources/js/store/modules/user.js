import axios from "axios";


const initialState = () => {

  return {

    jwtToken: localStorage.getItem('user'),
    statusToggledBtn: null,
    statusError: '',
    status: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).status : 'online',
    isUserLoggedIn: false,
  }
};

const user = {

  namespaced: true,

  state: initialState(),

  getters: {

    getToken (state)  {

      if (state.jwtToken) {

        return JSON.parse(state.jwtToken).access_token;
      }
    },

    getProfileStatus (state) {

      if (state.jwtToken) {

        return JSON.parse(state.jwtToken).profile_created;
      }
    },
       getUserId (state) {

      if (state.jwtToken) {

        return JSON.parse(state.jwtToken).user_id;
      }
    },

        getProfilePic (state) {

      if (state.jwtToken) {

        return JSON.parse(state.jwtToken).profile_pic;
      }
    },

    userName (state) {

      if (state.jwtToken) {

        return JSON.parse(state.jwtToken).name;
      }
    },

    isLoggedIn (state) {

      return !!state.jwtToken;
    },

    getStatus(state) {

       if (state.jwtToken) {


        return state.status;
      }
    }
  },

  mutations: {

    RESET_USER_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    SET_AUTH_STATUS: (state, payload) => {

      state.isUserLoggedIn = payload;
    },

    TOGGLE_STATUS_BTN: (state) => {

      state.statusToggledBtn = !state.statusToggledBtn;
      state.status = state.statusToggledBtn ? 'online' : 'offline';
    },

    SET_TOKEN(state,payload) {
      state.jwtToken = payload;

      localStorage.setItem('user', payload);
      state.status = JSON.parse(payload).status;
    },

    REMOVE_TOKEN(state) {

      localStorage.removeItem('user');

      state.jwtToken = '';
    },

    SET_INITIAL_TOGGLE_VALUE: (state, payload) => {

      state.statusToggledBtn = payload;
    },

    SYNC_NEW_STATUS: (state, payload) => {
      const user = JSON.parse(localStorage.getItem('user'));

      user.status = payload.new_user_status;
      state.status = payload.new_user_status;

      localStorage.setItem('user', JSON.stringify(user));
    },

    SET_STATUS_ERROR: (state, payload) => {

      state.statusError = payload;
    }
  },

  actions: {
    async LOGOUT({ commit, getters }) {

      try {

        let response;

        const token = getters.getToken;

        response = await axios(
          {
            method: 'POST',
            url: '/api/auth/logout',
            headers: {

              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },

            data: token,
          }
        );

        if (response.status === 200) {

          commit('REMOVE_TOKEN');
        }
      } catch (e) {


      }
    },


  async REFRESH_TOKEN({ commit, getters }) {



        let response;

       const token = getters.getToken;

        return await axios(
          {
            method: 'POST',
            url: '/api/auth/token/refresh',
            headers: {

              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: {

              token,
            },
          }
        );
    },

       async UPDATE_USER_STATUS({ getters, rootGetters, state, commit }) {

      try {

        let response;

       const userId = getters.getUserId;
       const status = getters.getStatus;

        response = await axios(
          {
            method: 'PATCH',
            url: `/api/auth/user/status/${userId}/update`,
            headers: {

              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: {

              status,
            },
          }
        );

          commit('SYNC_NEW_STATUS', response.data);

          const fullName = rootGetters['user/userName']
          .split(' ')
          .map(name => name.slice(0, 1)
          .toUpperCase() + name.slice(1)
          .toLowerCase())
          .join(' ');

          const user = {
            id:  rootGetters['user/getUserId'],
            full_name: fullName,
            status: rootGetters['user/getStatus'],
          };

          commit('messenger/UPDATE_CONTACT_STATUS', {user, status: rootGetters['user/getStatus']}, { root: true });


      } catch (e) {


        commit('SET_STATUS_ERROR', e.response.data.error);
      }
    },

 async UPDATE_USER_COLUMN({ getters, rootGetters, state, commit }, payload) {

      try {
        const response = await axios(
          {
            method: 'PATCH',
            url: `/api/auth/user/${payload.curUserId}/update`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: payload,
          }
        );
      } catch (e) {
        console.log(e)
      }
    }
  }
};

export default user;
