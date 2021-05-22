import axios from 'axios';


const initialState = () => {

  return {

    baseProfileData: [],
    fetchError: '',
    dataLoaded: false,
    profileNavigation: [],
    currentUserId: null,
    profileStats: null,
    viewingUserId: null,
  }
};

const profile = {

  namespaced: true,

  state: initialState(),

  getters: {

    getBaseProfile: (state) => {

      return state.baseProfileData;
    },

    getProfileLinks (state) {

      let isCurrentUserProfile;

     const links =  state.profileNavigation.filter((link) => {

          if (link.name === 'Profile') {

            isCurrentUserProfile = parseInt(link.params.id) === state.currentUserId;
          }

          if (isCurrentUserProfile) {

            return link;
          } else {

            if (link.name !== 'ProfileEdit') {
              return link;
            }
        }
      });
      return links;
    },
  },

  mutations: {


    RESET_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    SET_PROFILE_STATS: (state, payload) => {

      state.profileStats = payload;
      state.viewingUserId = payload.user_id;
    },

    SET_BASE_PROFILE_DATA: (state, payload) => {

      state.baseProfileData = payload;

      state.currentUserId = JSON.parse(localStorage.getItem('user')).user_id;

      const routes = [
        {name: 'Profile', text: 'Profile', id: 0, params: {id: state.baseProfileData.user_id}},
        {name: 'ProfileAbout', text: 'About', id: 1, params: {profileId: state.baseProfileData.id}},
        {name: 'ProfileEdit', text: 'Edit Profile', id:2, params: {profileId: state.baseProfileData.id}}
      ];

      routes.forEach((route) => {

        state.profileNavigation.push(route);
      });

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
         commit('SET_PROFILE_STATS', response.data.stats);
         commit('SET_BASE_PROFILE_DATA', response.data.profile);

      } catch (e) {

        commit('SET_FETCH_ERROR',e.response.data.msg);
      }
    },

    async UPDATE_FOLLOW_STATS ({ state, commit }) {

      try {

        const response = await axios(
            {
              method:'PATCH',
              url: `/api/auth/stats/follow/${state.currentUserId}/update`,
              headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json',
              },
              data: {viewingUserId: state.viewingUserId},
            }
        );

        console.log('UPDATE_FOLLOW_STATS RESPONSE: ', response);
        commit('SET_PROFILE_STATS', response.data.stats);

      } catch(e) {

        console.log(e.response);
      }
    },
  }
};

export default profile;
