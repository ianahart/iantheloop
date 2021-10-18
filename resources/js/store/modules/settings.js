import axios from 'axios';
const initialState = () => {
  return {
    currentSidebarOption: '',
    initialSearchPath: '/api/auth/settings/block/search',
    serverError: '',
    blockedUsersLoading: false,
    isBlockListOpen: false,
    inputs: {
      blocked_stories: { value: '', type: 'blocked_stories', active: false, error: '' },
      blocked_messages: { value: '', type: 'blocked_messages', active: false, error: '' },
      blocked_profile: { value: '', type: 'blocked_profile', active: false, error: '' },
    },
    searchResults: [],
    blockedUsers: [],
    userToBlock: null,
    blockedUserURL: '',
    security: {
      remember_me: false,
    },
    searchPagination: {
      path: null,
      current_page: null,
      last_page: null,
      next_page_url: null,
    },
  }
};

const settings = {
  namespaced: true,
  state: initialState(),

  getters: {

    getActiveInput(state) {
       return Object.entries(state.inputs).map(input => input[1]).find(input => input.active);
    },
    getUserToBlockReqData(state) {
       const { profile, type } = state.userToBlock;
       return {
         ...profile,
         type: type,
       }
    },
  },

  mutations: {
    SET_REMEMBER_ME(state, payload) {
      state.security.remember_me = payload;
    },

    UPDATE_SECURITY_PROP(state, { prop, value }) {
      for (let stateProp in state.security) {
        if (stateProp === prop) {
            state.security[prop] = value;
        }
      }
    },

    SET_BLOCKED_USERS_LOADING(state, isLoading) {
      state.blockedUsersLoading = isLoading;
    },

    UNBLOCK_USER(state, data) {
      const blockedUserIndex = state.blockedUsers.findIndex(user => user.blocked_by_list.privacy_id === data.privacy_id);
      state.blockedUsers.splice(blockedUserIndex, 1);
    },

    UPDATE_BLOCKED_TYPE(state, { data, types_blocked_count }) {
      const blockedUserIndex = state.blockedUsers.findIndex(user => user.blocked_by_list.privacy_id === data.user.privacy_id);
      state.blockedUsers[blockedUserIndex].blocked_by_list[data.type] = !state.blockedUsers[blockedUserIndex].blocked_by_list[data.type];
      if (types_blocked_count === 0) {
         state.blockedUsers.splice(blockedUserIndex, 1);
      }
    },

    SET_BLOCKED_USERS(state, { blocked_users, action }) {
      state.blockedUsers =  action === 'set' ? [...state.blockedUsers, ...blocked_users] : [];
    },

    SET_USER_TO_BLOCK(state, payload) {
       state.userToBlock = payload;
    },

    SET_BLOCK_INPUTS(state, { type, value, }) {
      for (let input in state.inputs) {
        if (input.toLowerCase() === type.toLowerCase()) {
            state.inputs[input].value = !value.trim().length ? '' : value;
            state.inputs[input].active = !value.trim().length ? false : true;
            state.inputs[input].error = '';
        } else {
            state.inputs[input].value = '';
            state.inputs[input].active = false,
            state.inputs[input].error = '';
        }
      }
    },

    SET_ACTIVE_BLOCK_INPUT(state, type) {
      state.inputs[type].active = true;
    },

    CLEAR_BLOCK_INPUTS(state) {
        Object.assign(state.inputs, initialState().inputs);
    },

    SET_SEARCH_RESULTS(state, { searches, initiator }) {
      const mapper = {
        click: [...state.searchResults, ...searches.data],
        typing: searches.data,
        clear: [],
      }
      state.searchResults = mapper[initiator];
    },

    SET_SEARCH_PAGINATION(state, pagination) {
      for(let prop in pagination) {
        state.searchPagination[prop] = pagination[prop];
      }
    },

    RESET_SEARCH_PAGINATION(state) {
      Object.assign(state.searchPagination, initialState().searchPagination);
    },

    SET_SEARCH_INPUT_ERROR(state, { type, error }) {
        Object.assign(state.inputs, initialState().inputs);
        Object.assign(state.searchResults, initialState().searchResults);
        Object.assign(state.searchPagination, initialState().searchPagination);
       for (let input in state.inputs) {
         if (input.toLowerCase() === type.toLowerCase()) {
           state.inputs[input].error = error[0];
         }
       }
    },

    CLEAR_SEARCH_RESULTS(state) {
      state.searchResults = [];
    },

    SET_IS_BLOCKLIST_OPEN(state, isBlockListOpen) {
       state.isBlockListOpen = isBlockListOpen;
    },

    SET_CURRENT_SIDEBAR_OPTION(state, option) {
      state.currentSidebarOption = option;
    },

     RESET_SETTINGS_MODULE(state) {
      Object.assign(state, initialState());
    },

    SET_SERVER_ERROR(state, serverError) {
      state.serverError = serverError;
    },

    SET_BLOCKED_USERS_URL(state, url) {
      state.blockedUserURL = url;
    }
  },

  actions: {
    async CREATE_USER_SETTINGS({ state, rootGetters, commit }, userId = null) {
      try {
          const currentUserId = !rootGetters['user/getUserId'] || rootGetters['user/getUserId'] === null ? userId : rootGetters['user/getUserId'];
          const response = await axios({
            method: 'POST',
            url: '/api/auth/settings/create',
            data: { current_user_id: currentUserId },
          });
      } catch(e) {
        commit('SET_SERVER_ERROR', e.response.data.error);
      }
    },
    async SEARCH_NETWORK({ state, rootGetters, commit }, { activeInput, initiator }) {
      try {
        const { type, value } = activeInput;
        const pagesExist = state.searchPagination.current_page !== null && state.searchPagination.last_page !== null;

        if (pagesExist) {
          if (state.searchPagination.current_page ===  state.searchPagination.last_page) {
            return;
          }
        }

        const url = state.searchPagination.next_page_url === null ? state.initialSearchPath : state.searchPagination.next_page_url;
        const response = await axios({
          method: 'POST',
          url,
          headers: { Accept: 'application/json', 'Content-Type' : 'application/json' },
          data: { current_user_id: rootGetters['user/getUserId'], type, value }
        });
        if (response.status === 200) {
          if (!response.data.searches.data.length) {
              commit('SET_SEARCH_INPUT_ERROR', { type, value,  error: ['No results found.'] });
              return;
          }
          const { searches } = response.data;
          commit('SET_SEARCH_RESULTS', { searches: searches, initiator });
          commit('SET_SEARCH_PAGINATION', searches.pagination);
        }
      } catch(e) {
        if (e.response.status === 422) {
          const { value:error } = e.response.data.errors;
          commit('SET_SEARCH_INPUT_ERROR', { type:activeInput.type, error });
        }
        if (e.response.status === 404) {
           commit('SET_SEARCH_INPUT_ERROR', { type, error: ['No results found.'] });
        }
      }
    },
    async BLOCK_USER ({ state, rootGetters, commit }) {
        try {
          const data = rootGetters['settings/getUserToBlockReqData'];
          data.current_user_id = rootGetters['user/getUserId'];

         const response = await axios({
           method: 'POST',
           url: '/api/auth/settings/block/store',
           headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
           data,
         });
         if (response.status === 201) {
            commit('SET_USER_TO_BLOCK', null);
         }
        } catch(e) {
          commit('SET_SERVER_ERROR', e.response.data.error);
        }
    },
    async GET_BLOCKED_USERS({ state, rootGetters, commit }) {
      try {
        if (state.blockedUserURL === null) {
          return;
        }
         const response = await axios({
           method: 'GET',
           url: state.blockedUserURL === '' ? `/api/auth/settings/block/${rootGetters['user/getUserId']}/show` : state.blockedUserURL,
           headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
         });
         if (response.status === 200) {

           const { data, next_page_url } = response.data.blocked_users;

           commit('SET_BLOCKED_USERS', { blocked_users: data, action: 'set' });
           commit('SET_BLOCKED_USERS_URL', next_page_url);
           commit('SET_BLOCKED_USERS_LOADING', false);
         }
      } catch(e) {
        commit('SET_SERVER_ERROR', e.response.data.error);
      }
    },
    async UPDATE_BLOCKED_USER({ state, rootGetters, commit }, {data, is_toggled}) {
      try {
        const { privacy_id, type } = data;

        const blockedUserIndex = state.blockedUsers.findIndex(blockedUser => blockedUser.blocked_by_list.privacy_id === privacy_id);

        const userData = {
          ...Object.assign({}, state.blockedUsers[blockedUserIndex]),
          type,
          is_toggled: !is_toggled,
          current_user_id: rootGetters['user/getUserId'],
        };

        const response = await axios({
          method: 'PATCH',
          url: `/api/auth/settings/block/${privacy_id}/update`,
          headers: { 'Accept': 'application/json', 'Content-Type':'application/json' },
          data: userData,
        });
        if (response.status === 200) {
          commit('UPDATE_BLOCKED_TYPE', { data, types_blocked_count: response.data.types_blocked_count });
        }
      } catch(e) {
        if (e.response.status === 500) {
          commit('SET_SERVER_ERROR', e.response.data.error);
        }
      }
    },
    async UNBLOCK_USER({ state, rootGetters, commit }, { blocked_by_list }) {

      try {
        const response = await axios({
          method: 'DELETE',
          url: `/api/auth/settings/block/${blocked_by_list.privacy_id}/delete?userId=${rootGetters['user/getUserId']}`,
          headers: { 'Accept' : 'application/json', 'Content-Type': 'application/json' },
        });

         if (response.status === 200) {
            commit('UNBLOCK_USER', blocked_by_list);
         }
      }  catch(e) {
        commit('SET_SERVER_ERROR', e.response.data.error);
      }
    },
    async UPDATE_REMEMBER_ME({ state, rootGetters, commit }, data) {
      try {

       const response = await axios({
         method: 'PATCH',
         url: `/api/auth/settings/remember-me/${rootGetters['user/getSettingsId']}/update`,
         headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
         data: { current_user_id: rootGetters['user/getUserId'], 'remember_me': state.security.remember_me },
       });
        if (response.status === 200) {
          commit('UPDATE_SECURITY_PROP', { prop: data.prop, value:  response.data.remember_me });
        }
      } catch(e) {
        console.log('UPDATE_REMEMBER_ME, ', e);
        commit('SET_SERVER_ERROR', e.response.data.error);
      }
    },
    async RETRIEVE_SECURITY_SETTINGS({ state,rootGetters, commit }) {
      try {
        const response = await axios({
          method: 'GET',
          url: `/api/auth/settings/remember-me/${rootGetters['user/getSettingsId']}/show`,
          headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        if (response.status === 200) {
          commit('SET_REMEMBER_ME',  response.data.remember_me);
        }
      } catch(e) {
        if (e.response.status === 400) {
          commit('SET_SERVER_ERROR', e.response.data.msg);
        }
      }
    },
    async VALIDATE_REMEMBER_ME({ state, commit }) {
      try {
        await axios({
          method: 'POST',
          url: `/api/auth/settings/remember-me/`,
          headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
      } catch(e) {
          commit('SET_SERVER_ERROR', e.response.data.msg);
      }
    }
  }
}
export default settings;
