import axios from 'axios';

const initialState = () => {

  return {
    validationError: '',
    searchValue: '',
    serverError: '',
    paginationSearchURL: '/api/auth/searches/search',
    paginationRecentSearchURL: '',
    searchActive: false,
    searchResults: [],
    recentSearches: [],
    searchIsFocused: false,
  }
};

const userSearch = {
  namespaced: true,
  state: initialState(),

  getters: {

  },

  mutations: {

    SET_SEARCH_IS_FOCUSED(state, payload) {
      state.searchIsFocused = payload;
    },

    SET_SEARCH_ACTIVE(state, payload) {
       state.searchActive = payload;
    },

    REMOVE_RECENT_SEARCH(state, payload) {
       if (payload.id === null && payload.searched_user_id === null) {
         state.recentSearches = [];
         return;
       }
       const recentSearchIndex = state.recentSearches.findIndex(recentSearch => recentSearch.id === payload.id);
       state.recentSearches.splice(recentSearchIndex, 1);
    },

    SET_SEARCH_VALUE(state, searchValue) {
      state.paginationSearchURL = state.paginationSearchURL === initialState().paginationSearchURL ?
      state.paginationSearchURL : initialState().paginationSearchURL;
      state.searchValue = searchValue.trim();
    },

    SET_VALIDATION_ERROR(state, error) {
       state.searchResults = [];
       state.validationError = error;
    },

    SET_SERVER_ERROR(state, error) {
      state.serverError = error;
    },

    SET_SEARCH_RESULTS(state, { initiator, data }) {
      if (!data.length) {
        state.validationError = 'No matches found';
        return;
      }
      if (initiator === 'key') {
        state.searchResults = data;
      } else if (initiator === 'btn') {
        state.searchResults = [...state.searchResults, ...data];
      }
    },

    SET_RECENT_SEARCHES(state, recentSearches) {
       state.recentSearches = [...state.recentSearches, ...recentSearches];
    },

    CLEAR_RECENT_SEARCHES (state) {
      state.recentSearches = [];
    },

    CLEAR_SEARCH_RESULTS(state) {
      state.searchResults = [];
    },

    SET_PAGINATION_SEARCH_URL(state, nextURL) {
      state.paginationSearchURL = nextURL;
    },

    SET_PAGINATION_RECENT_SEARCH_URL(state, nextURL) {
      state.paginationRecentSearchURL = nextURL;
    },

    RESET_USER_SEARCH_MODULE(state) {
      Object.assign(state, initialState());
    },
  },

  actions: {
    async POPULATE_SEARCH_RESULTS({ state, commit }, payload) {

      try {
        if (state.paginationSearchURL === null && payload.initiator === 'btn') {
          return;
        }
        const data = { user_id: payload.userId, search_value: state.searchValue };
        const response = await axios({
            method: 'POST',
            url: state.paginationSearchURL,
            headers:{ 'Accept': 'application/json', 'Content-Type': 'application/json' },
            data,
          });
        if (response.status === 200) {
            if (Array.isArray(response.data.search_results)) {
              commit('SET_VALIDATION_ERROR', 'No matches found');
              return;
            }
            const { data, next_page_url } = response.data.search_results;
            commit('SET_SEARCH_RESULTS', { initiator: payload.initiator, data });
            commit('SET_PAGINATION_SEARCH_URL', next_page_url);
        }
      } catch(e) {
        if (e.response.status === 422) {
          commit('SET_VALIDATION_ERROR', e.response.data.errors.search_value[0]);
        } else {
          commit('SET_SERVER_ERROR', e.response.data.error);
        }
      }
    },

    async SAVE_SEARCH_RESULT({ state, commit }, data) {
      try {
        await axios({
          method: 'POST',
          url: '/api/auth/searches/store',
          headers:{ 'Accept': 'application/json', 'Content-Type': 'application/json' },
          data,
        })
      } catch(e) {
        commit('SET_SERVER_ERROR', e.response.data.error);
      }
    },
   async RECENT_SEARCHES({ state, rootGetters, commit }) {
     try {
       if (state.paginationRecentSearchURL === null) {
         return;
       }
       state.paginationRecentSearchURL = state.paginationRecentSearchURL === '' ?
       `/api/auth/searches/${rootGetters['user/getUserId']}/show` : state.paginationRecentSearchURL;

       const response = await axios({
         method: 'GET',
         url: `${state.paginationRecentSearchURL}`,
         headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
       });
       if (response.status === 200) {
         commit('SET_RECENT_SEARCHES', response.data.recent_searches.data);
         commit('SET_PAGINATION_RECENT_SEARCH_URL', response.data.recent_searches.next_page_url);
       }
     } catch(e) {
       commit('SET_SERVER_ERROR', e.response.data.error);
     }
   },
   async REMOVE_RECENT_SEARCH({ state, rootGetters, commit }, payload) {
     try {
       const response = await axios({
         method: 'DELETE',
         url: `/api/auth/searches/${payload.id}/delete?ids=${encodeURIComponent(JSON.stringify(payload))}`,
         headers: { 'Accept' : 'application/json', 'Content-Type': 'application/json' },
       });
       if (response.status === 200) {
         commit('REMOVE_RECENT_SEARCH', payload);
       }
     } catch(e) {
       commit('SET_SERVER_ERROR', e.response.data.error);
     };
   }
  }
}

export default userSearch;