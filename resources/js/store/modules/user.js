
const initialState = () => {

  return {
    // state
  }
};

const user = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {


    RESET_USER_MODULE: (state) => {

     Object.assign(state, initialState());
    }
  },

  actions: {

  }
};

export default user;

