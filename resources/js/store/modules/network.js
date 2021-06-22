import axios from 'axios';


const initialState = () => {

  return {

    userId: null,
    isDataLoaded: false,
    page: 1,
    lastIndex: 0,
    listCount: 0,
    lastPageItem: null,
    networkList: [],
    lastCollectionItem: null,
    ownerProfilePic: '',
    ownerFullName: '',
    userError: '',
    error: false,
  }
};

const network = {

  namespaced: true,

  state: initialState(),

  getters: {

    endOfList(state) {
      return state.lastCollectionItem === state.lastPageItem.user_id ? true : false;
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
      state.lastCollectionItem = payload.last_collection_item;
      state.ownerProfilePic = payload.owner_profile_pic;
      state.ownerFullName = payload.owner_name;
      state.listCount = payload.list_count;

      state.lastIndex = state.networkList.length;
      state.lastPageItem = state.networkList[state.networkList.length - 1];

      state.page = state.page + 1;
    },

    SET_ERROR: (state, payload) => {

      if (payload.status === 404 && payload.data.error.toLowerCase() === 'user not found') {
        state.error = true;
      } else if (payload.status === 404) {
        state.userError = payload.data.error;
      }

    },

  },
  actions: {

    async GET_FOLLOWING({ state, commit, getters, rootGetters }) {

      try {

        let response;
        const index = state.lastPageItem === null ? 0 : state.lastPageItem.user_id;

        response = await axios(
          {
            url: `/api/auth/network/following/show/${state.userId}?page=${state.page}&index=${index}`,
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
        console.log(e.response);
        commit('SET_ERROR', e.response);
        commit('SET_DATA_LOADED');

      }
    },

    async GET_FOLLOWERS({ state, commit, getters, rootGetters }) {

      try {

        let response;
        const index = state.lastPageItem === null ? 0 : state.lastPageItem.user_id;

        response = await axios(
          {
            url: `/api/auth/network/followers/show/${state.userId}?page=${state.page}&index=${index}`,
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
