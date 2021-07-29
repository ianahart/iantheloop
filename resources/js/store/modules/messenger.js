import axios from 'axios';


const initialState = () => {

  return {
    filterBoxValue: '',
    errors: [],
    contacts: [],
    chatWindowReload: 0,
    chatWindowReloadTimes: 0,
    contactsCount: null,
    totalChatMessages: 0,
    conversationId: null,
    chatWindowUserId: null,
    isMessengerOpen: false,
    loadChatMessagesBtn: false,
    isChatWindowOpen: false,
    isFilterBoxVisible: false,
    chatMessagesLoaded: false,
    messengerLoaded: false,
    chatMessages: [],
    chatMessage: {
      recipient: { recipient_user_id: '', recipient_name: '' },
      sender: { sender_user_id: '', sender_name: '',  message: '' }
    },
  }
};

const messenger = {

  namespaced: true,

  state: initialState(),

  getters: {
    getLastChatMessage(state) {
      return state.chatMessages[state.chatMessages.length - 1];

    },
    getServerErrors(state) {
      return state.errors.filter(error => error.type == 'server');
    },

    getClientErrors(state) {
      return state.errors.filter(error => error.type === 'client');
    },

    getContacts(state) {
      return state.contacts.filter((contact) => {

        return contact.formatted_name.toLowerCase().includes(state.filterBoxValue.toLowerCase());
      });
    },

    getChatWindowUser(state) {
      return state.contacts.find(contact => contact.id === state.chatWindowUserId);
    },
  },

  mutations: {

    UPDATE_UNREAD_MESSAGE_COUNT(state, unreadMessage) {
      const contact = state.contacts.findIndex(contact => contact.id === unreadMessage.sender_user_id);
      state.contacts[contact].unread_messages_count++;
    },


    UPDATE_CONTACT_STATUS(state, { user, status }) {

      const contact = state.contacts.findIndex(contact => contact.id === user.id);

      if (contact > -1) {
        state.contacts[contact].status  = status;
      }
    },

    SET_TOTAL_CHAT_MESSAGES(state, payload) {
      state.totalChatMessages = payload;
    },

    SET_MORE_CHAT_MESSAGES_BTN(state, payload) {
      state.loadChatMessagesBtn = payload;
    },

    SET_CONVERSATION_ID(state, payload) {
      state.conversationId = payload;
    },

    RELOAD_CHAT_WINDOW(state) {
      state.chatWindowReload++
      state.chatWindowReloadTimes = state.chatWindowReloadTimes + 1;
    },

    SET_CHAT_MESSAGES_LOADED(state, payload) {
      state.chatMessagesLoaded = payload;
    },

    RECORD_CHAT_MESSAGE(state, message) {
      for (let prop in message ) {
        state.chatMessage[prop] = message[prop];
      }
    },

    OPEN_CHAT_WINDOW(state, payload) {
      state.isChatWindowOpen = true;
      state.chatWindowUserId = payload.id;
    },

    CLOSE_CHAT_WINDOW(state) {
      state.isChatWindowOpen = false;
      state.chatMessages = [];
    },

    SET_MESSENGER_LOADED(state, payload) {
      state.messengerLoaded = payload;
    },

    UPDATE_FILTER_BOX_VALUE(state, filter) {
      state.filterBoxValue = filter;
    },

    TOGGLE_FILTER_BOX_VISIBILITY(state) {
      state.isFilterBoxVisible = !state.isFilterBoxVisible;

      if (!state.isFilterBoxVisible) {
        state.filterBoxValue = '';
      }
    },

    SET_FILTER_BOX_INVISIBLE(state) {
      state.isFilterBoxShowing = false;
    },

    TOGGLE_MESSENGER(state) {
      state.isMessengerOpen = !state.isMessengerOpen;
    },

    CLOSE_MESSENGER(state) {
      state.isMessengerOpen = false;
    },

    RESET_MESSENGER_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    RESET_CHAT_MESSAGE_DATA(state) {
      Object.assign(state.chatMessage, initialState().chatMessage);
    },

    RESET_CHAT_MESSAGES(state) {
      state.chatMessages = [];
    },

    SET_MESSENGER_CONTACTS(state, data) {
      state.contacts = [...state.contacts, ...data.contacts];
      state.contactsCount = data.contacts_count;
    },

    SET_ERRORS(state, { msg, type }) {
      const error = {
        id: state.errors.length + 1,
        msg,
        type,
      }
      state.errors = [...state.errors, error];
    },

    SET_CHAT_MESSAGES(state, messages) {
      state.chatMessages = [...state.chatMessages, ...messages];
    },

    ADD_CHAT_MESSAGE(state, message) {
      state.chatMessages = [message, ...state.chatMessages];
    },

    CLEAR_NOTIFICATIONS(state, { sender, notificationsRead }) {
      const contact = state.contacts.findIndex(contact => contact.id === sender);
      if (notificationsRead) {
        state.contacts[contact].unread_messages_count = 0;
      }
    }
  },

  actions: {
    async GET_MESSENGER_CONTACTS({ state, rootGetters,  commit }) {

      try {
        const response = await axios(
            {
              method: 'GET',
              url: `/api/auth/messenger/${rootGetters['user/getUserId']}/show`,
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
              }
            }
        );

        if (response.status === 200) {
            commit('SET_MESSENGER_CONTACTS', response.data);
            commit('SET_MESSENGER_LOADED', true);
        }

      } catch(e) {

        if (e.response.status === 404) {
            commit('SET_ERRORS', { msg: e.response.data.errors, type: 'server'});
            commit('SET_MESSENGER_LOADED', true);
        }

      }
    },

    async GET_CHAT_MESSAGES({ state, commit, getters }) {

      try {

        let { id, created_at } = state.chatMessages && state.chatMessages.length ? getters.getLastChatMessage : {id: '', created_at: ''};

        const response = await axios({
          method: 'GET',
          url: `/api/auth/messages/${state.chatWindowUserId}/show?id=${id}&createdAt=${created_at}`,
          headers: {
            'Accept' : 'application/json',
            'Content-Type': 'application/json',
          }
        });

        if (response.status === 200) {
          commit('SET_CHAT_MESSAGES', response.data.chat_messages);
          commit('SET_TOTAL_CHAT_MESSAGES', response.data.total);
          commit('CLEAR_NOTIFICATIONS', { sender: state.chatWindowUserId, notificationsRead: response.data.notifications_read });
          commit('SET_CONVERSATION_ID', response.data.conversation_id);
          commit('SET_CHAT_MESSAGES_LOADED', true);

          if (state.totalChatMessages === 0) {
             commit('SET_MORE_CHAT_MESSAGES_BTN', false);
          }
        }

      } catch(e) {
        if (e.response.data.error === 'All messages loaded') {
            commit('SET_MORE_CHAT_MESSAGES_BTN', false);
            commit('SET_TOTAL_CHAT_MESSAGES', 0);
            commit('SET_CONVERSATION_ID', e.response.data.conversation_id);
        }
      }
    },

     async SEND_CHAT_MESSAGE({ state, commit }) {

      try {

          const response = await axios(
            {
              method: 'POST',
              url: '/api/auth/messages',
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
              },
              data: { chat_message: state.chatMessage, conversation_id: state.conversationId },
            }
          );

          if (response.status === 200) {
            commit('SET_CONVERSATION_ID', response.data.conversation_id);
            commit('RESET_CHAT_MESSAGE_DATA');
          }

      } catch (e) {
        console.log('@action messenger.js SEND_CHAT_MESSAGE ERROR',e.response);
      }
    },
  }
};

export default messenger;
