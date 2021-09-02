import axios from 'axios';
import { getElementIndex } from '../../helpers/moduleHelpers';

const initialState = () => {

  return {
    followRequests: [],
    interactions: [],
    unreadMessages: [],
    alertTimerID: 0,
    processQueue: ['millie brown', 'adam wayland'],
    currentPageMessages: null,
    interactionCursor: null,
    messageNotificationsAreOpen: false,
    messageNotificationsLoaded: false,
    currentInteractionAlert: null,
    isCurrentInteractionAlertActive: false,
    interactionNotificationsLoaded:false,
    navMessageAlerts: false,
    navInteractionAlerts: 0,
    serverError: '',
  }
};

const notifications = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {

    PROCESS_ENQUEUE(state, payload) {
      state.processQueue = [...state.processQueue, ...payload];
    },

    PROCESS_DEQUEUE(state,payload) {
      const index = state.processQueue.findIndex(queue => queue.sender_user_id === payload.sender_user_id);
      state.processQueue.splice(index, 1);
    },

    SET_CURRENT_INTERACTION_ALERT(state, payload) {
      const notEmpty = payload !== null && state.currentInteractionAlert !== null;

      if (payload !== null) {
        state.navInteractionAlerts = state.navInteractionAlerts + 1;
      }

      if (notEmpty && state.currentInteractionAlert.sender_user_id === payload.sender_user_id) {
          return;
        }
     state.currentInteractionAlert = payload;

    },

    SET_CURRENT_INTERACTION_ALERT_ACTIVE(state, payload) {
      state.isCurrentInteractionAlertActive = payload;
    },

    SET_INTERACTION_CURSOR(state, payload) {
      state.interactionCursor = payload;
    },

    RESET_INTERACTION_CURSOR(state) {
      state.interactionCursor = null
    },

    RESET_INTERACTION_NOTIFICATIONS(state) {
      state.interactions = [];
    },

   SET_SERVER_ERROR(state, payload) {
    state.serverError = payload;
   },

   RESET_NOTIFICATIONS_MODULE: (state) => {
     Object.assign(state, initialState());
    },


    SET_CURRENT_PAGE_MESSAGES(state, payload) {
       if (payload === 'end') {
        state.currentPageMessages = payload;
        return;
      }  else if (payload === 'reset') {
        state.currentPageMessages = 1;
      } else {
        state.currentPageMessages = payload + 1;
      }
    },

    SET_FOLLOW_REQUESTS: (state, payload) => {
      state.followRequests = payload;
    },
    SET_REMOVE_REQUEST: (state, payload) => {
      const deniedRequest = state.followRequests.findIndex(request => request.id === payload.requestId);
      state.followRequests.splice(deniedRequest, 1);
    },

    SET_NAV_ALERTS(state, { nav_interaction_alerts, nav_message_alerts }) {
      state.navInteractionAlerts = nav_interaction_alerts;
      state.navMessageAlerts = nav_message_alerts;
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

    SET_INTERACTION_NOTIFICATIONS_LOADED(state, payload) {
      state.interactionNotificationsLoaded = payload;
    },

    SET_MESSAGE_NOTIFICATIONS(state, { notifications }) {
      state.unreadMessages = [...state.unreadMessages, ...notifications];
    },

    SET_INTERACTION_NOTIFICATIONS(state, { notifications }) {
      for (let notification in notifications) {
        state.interactions.push(notifications[notification]);
      }
    },

    CLEAR_MESSAGE_NOTIFICATIONS(state) {
      state.unreadMessages = [];
    },

    MARK_NOTIFICATION_AS_READ(state, payload) {
      const index = getElementIndex(state.unreadMessages, 'sender_user_id', payload.sender_user_id);
      state.unreadMessages[index].new_notifications = false;
      state.unreadMessages[index].latest_read_at = 'Read Just now';
    },

    DELETE_MESSAGE_NOTIFICATIONS(state, sender) {
      const index = getElementIndex(state.unreadMessages, 'sender_user_id', sender);
      state.unreadMessages.splice(index, 1);
    },

    DELETE_INTERACTION_NOTIFICATION(state, payload) {
      const index = getElementIndex(state.interactions, 'id', payload.id);
      state.interactions.splice(index, 1);

      if (state.navInteractionAlerts > 0) {
        state.navInteractionAlerts = state.navInteractionAlerts - 1;
      }
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
            url: `/api/auth/user/notifications/messages/${rootGetters['user/getUserId']}/show?page=${state.currentPageMessages}&type=${type}`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );
          if (response.status === 200) {
            commit('SET_MESSAGE_NOTIFICATIONS', response.data);
            commit('SET_CURRENT_PAGE_MESSAGES', response.data.current_page_messages);
            commit('MESSAGE_NOTIFICATIONS_LOADED', true);
          }
      } catch(e) {

        // console.log('notifications.js: FETCH_MESSAGE_NOTIFICATIONS() line 161 Error:', e);
      }
    },

    async FETCH_INTERACTION_NOTIFICATIONS({ state, rootGetters, commit }, type) {
      try {



        const response = await axios(
          {
            method: 'GET',
            url: state.interactionCursor === null ? `/api/auth/user/notifications/interactions/${rootGetters['user/getUserId']}/show?cursor=&type=${type}`: state.interactionCursor + '&type='+ type,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );

        if (response.status === 200) {
          console.log(response.data)
          commit('SET_INTERACTION_CURSOR', response.data.next_page_url);
          commit('SET_INTERACTION_NOTIFICATIONS', response.data);
          commit('SET_INTERACTION_NOTIFICATIONS_LOADED', true);
        }

      } catch(e) {
        // console.log('notifications.js: FETCH_INTERACTION_NOTIFICATIONS() line 165 Error:', e);
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
          if (response.status === 200) {
            commit('DELETE_MESSAGE_NOTIFICATIONS', payload.sender);
          }
      } catch(e) {
        // console.log('notifications.js: DELETE_MESSAGE_NOTIFICATIONS() line 155 Error:', e.response);
      }
    },

    async FETCH_NAV_NOTIFICATION_ALERTS({ state, commit }, payload) {
      try {
        const response = await axios(
          {
            method: 'GET',
            url: `/api/auth/user/notifications/alerts/${payload.userId}/show?type=${btoa(JSON.stringify({ type: payload.type }))}`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );

        if (response.status === 200) {
          commit('SET_NAV_ALERTS', response.data);
        }
      } catch(e) {
        commit('SET_SERVER_ERROR', e.response.data.error);
      }
    },

    async DELETE_INTERACTION_NOTIFICATION({ state, rootGetters, commit }, payload) {
      try {

        const response = await axios(
          {
            method: 'DELETE',
            url: `/api/auth/user/notifications/interactions/${payload.id}/delete?userId=${payload.notifiable_id}`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );


        if (response.status === 200) {
           commit('DELETE_INTERACTION_NOTIFICATION', payload);
        }

      } catch(e) {
        console.log('notifications.js:DELETE_INTERACTION_NOTIFICATION Error:', e.response);

      }
    },
  }
}

export default notifications;