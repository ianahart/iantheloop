import axios from 'axios';


const initialState = () => {

  return {
    /*Potential things to keep track of with a message*/
    // outgoingMessage: '',
    // incomingMessage: '',
    filterBoxValue: '',
    errors: [],
    contacts: [],
    contactsCount: null,
    chatWindowUserId: null,
    isMessengerOpen: false,
    isChatWindowOpen: false,
    isFilterBoxVisible: false,
    messengerLoaded: false,
    chatMessage: {
      recipient: { recipientID: '', recipientName: '', message: '' },
      sender: { senderID: '', senderName: '' }
    },
  }
};

const messenger = {

  namespaced: true,

  state: initialState(),

  getters: {
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

    async SEND_CHAT_MESSAGE({ state, commit }) {

      try {

          console.log('Message Sent: ', state.chatMessage);
          //  commit('RESET_CHAT_MESSAGE_DATA');


      } catch (e) {

      }
    }
  }
};

export default messenger;
