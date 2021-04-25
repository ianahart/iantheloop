import axios from 'axios';


const initialState = () => {

  return {

    isProfileDropdownOpen: false,
  }
};

const profileDropdown = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {

    TOGGLE_PROFILE_DROPDOWN: (state) => {

      state.isProfileDropdownOpen = !state.isProfileDropdownOpen;
    },

     CLOSE_PROFILE_DROPDOWN: (state, payload) => {

      state.isProfileDropdownOpen = payload;
    },

    RESET_USER_MODULE: (state) => {

     Object.assign(state, initialState());
    },
  },

  actions: {

  }
};

export default profileDropdown;
