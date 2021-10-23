import axios from 'axios';


const initialState = () => {

  return {

    userId: null,
    isDataLoaded: false,
    networkList: [],
    listCount: 0,
    pagination: null,
    ownerUser: null,
    userError: '',
    error: false,
  }
};

const network = {

  namespaced: true,

  state: initialState(),

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

    SET_OWNER_USER(state, ownerUser) {
      state. ownerUser = ownerUser;
    },

    SET_NETWORK_LIST: (state, network) => {

      state.networkList = [...state.networkList, ...network];
    },

    SET_PAGINATION(state, pagination) {
       state.listCount = pagination.total;
       const paginationObj = {
         first_page_url: pagination.first_page_url,
         from: pagination.from,
         last_page: pagination.last_page,
         last_page_url: pagination.last_page_url,
         next_page_url: pagination.next_page_url,
         path: pagination.path,
       };
       state.pagination = paginationObj;
    },

    SET_ERROR: (state, payload) => {
      if (payload) {
          if (payload.status === 404 && payload.data.error.toLowerCase() === 'user not found') {
            state.error = true;
          } else if (payload.status === 404) {
            state.userError = payload.data.error;
          }
      }
    },

  },
  actions: {

    async GET_FOLLOWING({ state, commit, getters, rootGetters }) {

      try {
       const url = state.pagination !== null ? state.pagination.next_page_url : `/api/auth/network/following/show/${state.userId}`;
       const response = await axios(
          {
            url,
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );
        if (response.status === 200) {
          const { users, owner_user } = response.data;

          commit('SET_NETWORK_LIST', users.data);
          commit('SET_OWNER_USER', owner_user);
          commit('SET_PAGINATION', users);
          commit('SET_DATA_LOADED');
        }

      } catch (e) {
        if (e.response.status !== 403 || e.response.status !== 429) {
          commit('SET_ERROR', e.response);
          commit('SET_DATA_LOADED');
        }
      }
    },

    async GET_FOLLOWERS({ state, commit, getters, rootGetters }) {

      try {

        const url = state.pagination !== null ? state.pagination.next_page_url : `/api/auth/network/followers/show/${state.userId}`;
        const response = await axios(
          {
            url,
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );
       if (response.status === 200) {
          const { users, owner_user } = response.data;
          commit('SET_NETWORK_LIST', users.data);
          commit('SET_OWNER_USER', owner_user);
          commit('SET_PAGINATION', users);
          commit('SET_DATA_LOADED');
        }

      } catch (e) {
        if (e.response.status !== 403 || e.response.status !== 429) {
          commit('SET_ERROR', e.response);
          commit('SET_DATA_LOADED');
        }
      }
    },
  }
};

export default network;
