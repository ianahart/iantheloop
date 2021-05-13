import axios from 'axios';


const initialState = () => {

  return {

    baseProfileData: [],
    fetchError: '',
    dataLoaded: false,
    profileNavigation: [
      {name: 'Profile', text: 'Profile', id: 0},
      {name: 'Bio', text: 'Bio', id: 1},
      {name: 'EditProfile', text: 'Edit Profile', id:2}
    ],
  }
};

const profile = {

  namespaced: true,

  state: initialState(),

  getters: {

    getBaseProfile: (state) => {

      return state.baseProfileData;
    },

  },

  mutations: {


    RESET_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    SET_BASE_PROFILE_DATA: (state, payload) => {

      state.baseProfileData = payload;

      state.dataLoaded = true;


    },

    SET_FETCH_ERROR: (state, payload) => {

      state.fetchError = payload;

      state.dataLoaded = true;
    }
  },

  actions: {

    async FETCH_BASE_PROFILE_DATA ({commit }, payload) {

      try {

        const response = await axios(
            {
              method:'GET',
              url: `/api/auth/profile/${payload}`,
              headers: {
                'Accept' : 'application/json',
              },
            }
          );

         commit('SET_BASE_PROFILE_DATA', response.data.profile);

      } catch (e) {

        commit('SET_FETCH_ERROR',e.response.data.msg);
      }
    }

  }
};

export default profile;
