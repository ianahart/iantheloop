import axios from 'axios';


const initialState = () => {

  return {

    names: '',
  }
};

const newsFeed = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {


    RESET_USER_MODULE: (state) => {

     Object.assign(state, initialState());
    },

  },

  actions: {



  }
};

export default newsFeed;
