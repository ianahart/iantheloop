import axios from 'axios';


const initialState = () => {

  return {

    followSuggestionsLoaded: false,
    followSuggestions: [],
    lastFollowSuggestion: null,
    isLoadingData: false,
    endOfFollowSuggestions: false,
    endOfFollowSuggestionsCounter:0,
    errorMessage: '',
  }
};

const newsFeed = {
  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {
    SET_IS_LOADING_DATA(state, payload) {
      state.isLoadingData = payload;
    },

    SET_FOLLOW_SUGGESTIONS_LOADED(state, payload) {
      state.followSuggestionsLoaded = payload;
    },

    SET_FOLLOW_SUGGESTIONS(state, payload) {
      state.followSuggestions = [...state.followSuggestions, ...payload];
    },

    SET_ERROR_MESSAGE(state, payload) {
      state.errorMessage = payload;
    },

    SET_LAST_FOLLOW_SUGGESTION(state, payload) {
      state.lastFollowSuggestion = payload.id;
    },

    SET_FOLLOW_SUGGESTIONS_END(state, payload) {
      state.endOfFollowSuggestions = payload;
    },

    RESET_NEWSFEED_MODULE: (state) => {
     Object.assign(state, initialState());
    },

    INCREMENT_END_OF_FOLLOW_SUGGESTIONS_COUNTER (state)  {
      state.endOfFollowSuggestionsCounter = state.endOfFollowSuggestionsCounter + 1;
    }
  },

  actions: {
    async RETRIEVE_FOLLOW_SUGGESTIONS({ state, rootGetters, commit }) {
      try {
        commit('SET_IS_LOADING_DATA', true);
        const response = await axios(
          {
            method: 'GET',
            url: `/api/auth/follow-suggestions/${rootGetters['user/getUserId']}/show?last=${state.lastFollowSuggestion}&end=${state.endOfFollowSuggestions === false ? '0' : '1'}`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );
          if (response.status === 200) {

            console.log('newsFeed.js | Response:200', response);

            if (response.data.total > 0) {
              commit('SET_FOLLOW_SUGGESTIONS', response.data.follow_suggestions);
              commit('SET_LAST_FOLLOW_SUGGESTION', response.data.follow_suggestions[response.data.follow_suggestions.length - 1]);
              commit('SET_FOLLOW_SUGGESTIONS_END', false);
            } else {
              commit('SET_FOLLOW_SUGGESTIONS_END', true);
              commit('INCREMENT_END_OF_FOLLOW_SUGGESTIONS_COUNTER');
            }
            commit('SET_FOLLOW_SUGGESTIONS_LOADED', true);
            commit('SET_IS_LOADING_DATA', false);
          }
      } catch(e) {
        console.log('newsFeed.js | Response:404', e.response);
        commit('SET_ERROR_MESSAGE', e.response.data.error);
        commit('SET_IS_LOADING_DATA', false);
      }
    }
  }
};

export default newsFeed;
