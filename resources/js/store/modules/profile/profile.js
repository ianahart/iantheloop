import axios from 'axios';


const initialState = () => {

  return {

    baseProfileData: {},
    fetchError: '',
    restrictedProfile: { status: false, user: null, current_user_does_not_follow: null },
    dataLoaded: false,
    profileNavigation: [],
    currentUserId: null,
    profileStats: null,
    viewingUserId: null,
    currUserFollowing: null,
    isModalOpen: false,
    followStatus: '',
  }
};

const profile = {

  namespaced: true,

  state: initialState(),

  getters: {


    getBaseProfile: (state) => {

      return state.baseProfileData;
    },

    getProfileOwner(state) {
      return parseInt(state.baseProfileData.user_id);
    },

    getProfileLinks(state) {

      let isCurrentUserProfile;

      const links = state.profileNavigation.filter((link) => {

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

    TOGGLE_MODAL: (state) => {

      state.isModalOpen = !state.isModalOpen;
    },

    CLOSE_MODAL: (state, payload) => {

      state.isModalOpen = payload;
    },

    SET_PROFILE_STATS: (state, payload) => {
      state.profileStats = payload.stats;
      state.viewingUserId = payload.stats.user_id;
      state.currUserFollowing = payload.currUserFollowing;
    },

    SET_BASE_PROFILE_DATA: (state,{ profile, currUserHasRequested, currentUserId }) => {
      state.baseProfileData = profile;
      state.followStatus = currUserHasRequested ? 'Requested' : '';
      state.currentUserId = currentUserId;
      const routes = [
        { name: 'Profile', text: 'Profile', id: 0, params: { id: state.baseProfileData.user_id } },
        { name: 'ProfileAbout', text: 'About', id: 1, params: { profileId: state.baseProfileData.id } },
        { name: 'ProfileEdit', text: 'Edit Profile', id: 2, params: { profileId: state.baseProfileData.id } }
      ];

      routes.forEach((route) => {

        state.profileNavigation.push(route);
      });

      state.dataLoaded = true;
    },

    SET_DATA_LOADED(state, payload) {
      state.dataLoaded = payload;
    },

    SET_PARTY(state, payload) {
      state.currentUserId = payload.currentUserId;
      state.viewingUserId = payload.viewingUserId;
    },

    SET_FETCH_ERROR: (state, payload) => {

      state.fetchError = payload;

      state.dataLoaded = true;
    },

    SET_FOLLOW_STATUS: (state, payload) => {

      state.followStatus = payload.follow_status;
    },

    SET_RESTRICTED_PROFILE(state, payload) {
      state.restrictedProfile.status = payload.status;
      state.restrictedProfile.user = payload.user;
      state.restrictedProfile.current_user_does_not_follow = payload.current_user_does_not_follow;
      state.dataLoaded = true;
    }
  },

  actions: {

    async SEND_FOLLOW_REQUEST({ state, commit }) {

      try {

        const response = await axios(
          {
            method: 'POST',
            url: '/api/auth/follow-requests/store',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: {
              requester_user_id: state.currentUserId,
              receiver_user_id: state.viewingUserId,
            },
          }
        );
        commit('SET_FOLLOW_STATUS', response.data);
      } catch (e) {
        console.log('store/profile.js @SEND_FOLLOW_REQUEST Error: ', e.response);
      }
    },

    async FETCH_BASE_PROFILE_DATA({ rootGetters, commit }, payload) {

      try {

        const response = await axios(
          {
            method: 'GET',
            url: `/api/auth/profile/${payload}`,
            headers: {
              'Accept': 'application/json',
            },
          }
        );

        if (response.status === 200) {
          commit('SET_PROFILE_STATS', response.data);
          commit('SET_BASE_PROFILE_DATA',
            {
              profile: response.data.profile,
              currUserHasRequested: response.data.currUserHasRequested,
              currentUserId: rootGetters['user/getUserId'],
            });
        }
      } catch (e) {
        if (e.response.status === 404 && e.response.data.error.toLowerCase() === 'blocked profile') {
           commit('SET_RESTRICTED_PROFILE', {
            status: true,
            user: Object.values(e.response.data.restricted_user).includes(rootGetters['user/getUserId']) ? 'current_user' : 'viewing_user',
            current_user_does_not_follow: e.response.data.current_user_does_not_follow,
           });
        }
      }
    },


    async UPDATE_FOLLOW_STATS({ state, commit }, data) {

      try {

        const response = await axios(
          {
            method: 'PATCH',
            url: `/api/auth/stats/follow/${data.currentUserId}/update`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data,
          }
        );
        commit('SET_PROFILE_STATS', response.data);
      } catch (e) {
        console.log(e.response);
      }
    },

    async UNFOLLOW({ state, commit }, { currentUserId = null, viewingUserId = null } = {}) {

      try {

        let viewingUser = !state.restrictedProfile.status && state.viewingUserId !== null ? state.viewingUserId : viewingUserId;
        let currentUser = !state.restrictedProfile.status && state.currentUserId !== null ? state.currentUserId : currentUserId;

        const response = await axios(
          {
            method: 'PATCH',
            url: `/api/auth/stats/unfollow/${currentUser}/update`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: { viewingUserId: viewingUser },
          }
        );

        commit('SET_PROFILE_STATS', response.data);

      } catch (e) {

        console.log(e.response);
      }
    },
  }
};

export default profile;
