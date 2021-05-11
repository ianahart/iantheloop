import axios from "axios";

const initialState = () => {

  return {

    jwtToken: localStorage.getItem('user'),
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
  },

  mutations: {


    RESET_USER_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    SET_AUTH_STATUS: (state, payload) => {

      state.isUserLoggedIn = payload;
    },

    SET_TOKEN(state,payload) {

      state.jwtToken = payload;

      localStorage.setItem('user', payload);

    },

    REMOVE_TOKEN(state) {

      localStorage.removeItem('user');

      state.jwtToken = '';
    },
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
    }


  }
};

export default user;
