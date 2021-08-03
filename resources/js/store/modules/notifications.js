import axios from 'axios';
import { getElementIndex } from '../../helpers/moduleHelpers';

const initialState = () => {

  return {
    followRequests: [],
    notifications: [],
    messageNotificationsAreOpen: false,
    messageNotificationsLoaded: false,
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

    TOGGLE_MESSAGE_NOTIFICATIONS(state) {
      state.messageNotificationsAreOpen = !state.messageNotificationsAreOpen;
    },

    CLOSE_MESSAGE_NOTIFICATIONS(state) {
      state.messageNotificationsAreOpen = false;
    },

    MESSAGE_NOTIFICATIONS_LOADED(state, payload) {
      state.messageNotificationsLoaded = payload;
    },

    SET_MESSAGE_NOTIFICATIONS(state, { notifications }) {
      state.notifications = [...state.notifications, ...notifications];
    },

    CLEAR_MESSAGE_NOTIFICATIONS(state) {
      state.notifications = [];
    },

    MARK_NOTIFICATION_AS_READ(state, payload) {
      const index = getElementIndex(state.notifications, 'sender_user_id', payload.sender_user_id);
      state.notifications[index].new_notifications = false;
      state.notifications[index].latest_read_at = 'Read Just now';
    },

    DELETE_MESSAGE_NOTIFICATIONS(state, sender) {
      const index = getElementIndex(state.notifications, 'sender_user_id', sender);
      state.notifications.splice(index, 1);
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
    },

    async FETCH_MESSAGE_NOTIFICATIONS({ state, rootGetters, commit }, type) {

      try {

        const response = await axios(
          {
            method: 'GET',
            url: `/api/auth/user/notifications/messages/${rootGetters['user/getUserId']}/show?type=App/Notifications/UnreadMessage`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );
          if (response.status === 200) {
            commit('SET_MESSAGE_NOTIFICATIONS', response.data);
            commit('MESSAGE_NOTIFICATIONS_LOADED', true);
          }
      } catch(e) {

        console.log('notifications.js: FETCH_NOTIFICATIONS() line 84 Error:', e);
      }
    },

    async UPDATE_MESSAGE_NOTIFICATIONS({ state, commit }, payload) {

      try {

        const response = await axios({
          method: 'PATCH',
          url: `/api/auth/user/notifications/messages/${payload.recipient}/update`,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          data: { sender: payload.sender, type: payload.type },
        });

      } catch(e) {
        //  console.log('notifications.js: UPDATE_MESSAGE_NOTIFICATIONS() line 155 Error:', e.response);
      }
    },

    async DELETE_MESSAGE_NOTIFICATIONS ({ state, commit }, payload) {
      try {

        const response = await axios(
          {
            method: 'DELETE',
            url:`/api/auth/user/notifications/messages/${payload.recipient}/delete?sender=${payload.sender}&type=${payload.type}`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application.json',
            },
          }
        );
          console.log('notifications.js: DELETE_MESSAGE_NOTIFICATIONS() line 155 Error:', response);
          if (response.status === 200) {
            commit('DELETE_MESSAGE_NOTIFICATIONS', payload.sender);
          }
      } catch(e) {
        console.log('notifications.js: DELETE_MESSAGE_NOTIFICATIONS() line 155 Error:', e.response);
      }
    }
  }
}

export default notifications;