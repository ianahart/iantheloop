import axios from 'axios';


const initialState = () => {

  return {

    stories: [],
  }
};

const stories = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {
    RESET_STORIES_MODULE: (state) => {
     Object.assign(state, initialState());
    },
  },

  actions: {

  }
};

export default stories;
