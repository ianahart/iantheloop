import axios from 'axios';


const initialState = () => {

  return {

    contacts: [],
    contacts_count: null,
    isMessengerOpen: false,
    errors: [],
  }
};

const messenger = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {

    TOGGLE_MESSENGER(state) {
      state.isMessengerOpen = !state.isMessengerOpen;
    },

    CLOSE_MESSENGER(state) {
      state.isMessengerOpen = false;
    },

    RESET_MESSENGER_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    SET_MESSENGER_CONTACTS(state, data) {
      state.contacts = [...state.contacts, ...data.contacts];
      state.contacts_count = data.contacts_count;
    },

    SET_ERRORS(state, errors) {
      state.errors = [...state.errors, ...errors];
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
        }

      } catch(e) {

        if (e.response.status === 404) {
            commit('SET_ERRORS', e.response.data.errors);
        }

      }
    }

  }
};

export default messenger;
