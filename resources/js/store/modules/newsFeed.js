import axios from 'axios';


const initialState = () => {

  return {

    names: '',
  }
};

const newsFeed = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {


    RESET_USER_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    GET_NAMES: (state, payload) => {

      state.names = payload;
    },
  },

  actions: {

    async GET_NAMES ({ commit, getters, rootGetters }) {

      try {

       let response;

       response = await axios(
          {
            url: '/api/auth/newsfeed',
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );
          console.log('NEWSFEED:', response);
          commit('GET_NAMES', response.data);

      } catch (e) {

        console.log(e.response);
      }
    },

  }
};

export default newsFeed;
