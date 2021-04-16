
const hamburgerMenu = {

  namespaced: true,

  state: {

    isMenuIconVisible: false,
    isMenuVisible: false,
  },

  getters: {

  },

  mutations: {
    SHOW_MENU_ICON: (state, payload) => {

      state.isMenuIconVisible = payload;
    },

    HIDE_MENU_ICON: (state, payload) => {

      state.isMenuIconVisible = payload;
    },

    OPEN_MENU: (state, payload) => {

      state.isMenuVisible = payload;
    },

    HIDE_MENU: (state, payload) => {

      state.isMenuVisible = payload;
    }
  },

  actions: {

  }
}

export default hamburgerMenu;