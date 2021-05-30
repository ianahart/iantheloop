import axios from "axios";

const initialState = () => {

  return {

    jwtToken: localStorage.getItem('user'),
    statusToggledBtn: null,
    statusError: '',
    status: JSON.parse(localStorage.getItem('user'))?.status,
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

        // return JSON.parse(state.jwtToken).status;
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
      const user = JSON.parse(localStorage.getItem('user'));
      state.status = user.status;
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
            }
          }
        );

        if (response.status === 200) {

          commit('REMOVE_TOKEN');
        }
      } catch (e) {

        console.log(e);
      }
    },

    async REFRESH_TOKEN({ commit, getters }) {

      try {

        let response;

       const token = getters.getToken;

        response = await axios(
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
        ).then((response) => {

          if (response.status === 200) {

            commit('SET_TOKEN', response.data.access_token);
        }
        })

      } catch (e) {

        /**
         *
         * Handled by axios interceptors
         */
      }
    },

       async UPDATE_USER_STATUS({ commit, getters }) {

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

      } catch (e) {


        commit('SET_STATUS_ERROR', e.response.data.error);
      }
    }

  }
};

export default user;
