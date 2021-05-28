import axios from 'axios';


const initialState = () => {

  return {

    userId: null,
    isDataLoaded: false,
    page: 1,
    lastIndex: 0,
    networkList: [],
    listCount: 0,
    ownerProfilePic: '',
    ownerFullName: '',
    error: false,
  }
};

const network = {

  namespaced: true,

  state: initialState(),

  getters: {

    endOfList (state) {

      return state.listCount === state.lastIndex ? true: false;

    },

  },

  mutations: {

    RESET_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    SET_USER_ID: (state, payload) => {


      state.userId = payload;
    },

    SET_DATA_LOADED(state) {

      state.isDataLoaded = true;
    },

    SET_NETWORK_LIST: (state, payload) => {

      state.networkList.push(...payload.profiles);
      state.listCount = payload.list_count;
      state.ownerProfilePic = payload.owner_profile_pic;
      state.ownerFullName = payload.owner_name;

      state.lastIndex = state.networkList.length;
      state.page = state.page + 1;
    },

    SET_ERROR: (state, payload) => {

      if (payload.status === 404 && payload.data.error.toLowerCase() === 'user not found.') {

        state.error = true;
      }
    },

  },
  actions: {

    async GET_FOLLOWING ({ state, commit, getters, rootGetters }) {

      try {

       let response;

       response = await axios(
          {
            url: `/api/auth/network/following/show/${state.userId}?page=${state.page}&index=${state.lastIndex}`,
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );

        commit('SET_NETWORK_LIST', response.data);
        commit('SET_DATA_LOADED');

      } catch (e) {

        commit('SET_ERROR', e.response);
        commit('SET_DATA_LOADED');

      }
    },

     async GET_FOLLOWERS ({ state, commit, getters, rootGetters }) {

      try {

       let response;

       response = await axios(
          {
            url: `/api/auth/network/followers/show/${state.userId}?page=${state.page}&index=${state.lastIndex}`,
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );

        commit('SET_NETWORK_LIST', response.data);
        commit('SET_DATA_LOADED');

      } catch (e) {

        commit('SET_ERROR', e.response);
        commit('SET_DATA_LOADED');

      }
    },

  }
};

export default network;
