import axios from 'axios';

const initialState = () => {

  return {

    followRequests: [],
  }
};

const notifications = {

  namespaced: true,

  state: initialState(),



  getters: {

  },

  mutations: {

   RESET_NOTIFICATIONS_MODULE: (state) => {

     Object.assign(state, initialState());
    },
    SET_FOLLOW_REQUESTS: (state, payload) => {

      state.followRequests = payload;
    },

    SET_REMOVE_REQUEST: (state, payload) => {
      const deniedRequest = state.followRequests.findIndex(request => request.id === payload.requestId);
      state.followRequests.splice(deniedRequest, 1);
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
        if (response.status === 200) {
          commit('SET_FOLLOW_REQUESTS', response.data.follow_requests);
        }
      } catch (e) {

        // console.log('store/notifications @FETCH_FOLLOW_REQUESTS ERROR: ', e.response);
      }
    },

    async REMOVE_REQUEST({ state, commit }, payload) {

      try {

        const response = await axios(
          {
            method: 'DELETE',
            url: `/api/auth/follow-requests/${payload.requestId}/delete?userId=${payload.viewingUserId}`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );
        if (response.status === 200) {
          commit('SET_REMOVE_REQUEST', payload);
        }

      } catch (e) {
        console.log('store/notifications @REMOVE_REQUEST ERROR: ', e.response);
      }
    }
  }
}

export default notifications;