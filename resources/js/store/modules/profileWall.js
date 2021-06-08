import axios from 'axios';


const initialState = () => {

  return {

    currentUserFullName: '',
    currentUserFirstName: '',
    postsLoaded: false,
    modalFormIsOpen: false,
    isInputActive: false,
    postErrors: [],
    postInputPlaceholder: '',
    postInputPhoto: { src: '', file: null, input: ''},
    postInputVideo: {src: '', file: null, input: ''},
    postInputText: '',
    postInputTextLength: null,
    lastPostItem: 0,
    morePosts: true,
    posts: [],
  }
};

const profileWall = {

  namespaced: true,

  state: initialState(),

  getters: {


  },

  mutations: {

    SET_POSTS: (state, payload) => {

      if (payload.posts !== null && payload.more_records) {

        state.posts.push(...payload.posts.data);
        state.lastPostItem = payload.last_post_item;
        state.morePosts = true;

      } else {
        state.morePosts = false;
      }
    },

    SET_POST_SEEN: (state, seenId) => {

        const postIndex = state.posts.findIndex((post) => post.id === seenId);

        state.posts[postIndex].seen = true;
    },

    SET_POST: (state, payload) => {

      payload.new_post.seen = false;

      state.posts.unshift(payload.new_post);
    },

    SET_POSTS_LOADED: (state, payload) => {

      state.postsLoaded = payload;
    },

    RESET_POST_ERRORS: (state) => {

      state.postErrors = [];
    },

    RESET_POST_STATE: (state) => {
      state.postInputText = '';
      const defaultValues = ['', null, ''];

      const videoKeys = Object.keys(state.postInputVideo).map((key, index) => ({[key]: defaultValues[index]}));
      const photoKeys = Object.keys(state.postInputPhoto).map((key, index) => ({[key]: defaultValues[index]}));

      state.postInputVideo = Object.assign({}, ...videoKeys);
      state.postInputPhoto = Object.assign({}, ...photoKeys);
    },

    REMOVE_POST_MEDIA: (state, payload) => {
      let media;

      if (payload === 'photo') {

        media = 'postInputPhoto';
      } else {
        media = 'postInputVideo';
      }

      for (let prop in state[media]) {
        state[media][prop] = prop === 'file' ? null : '';
      }
    },

    SET_FILE: (state, payload) => {
      let file;

      if (payload.input == 'photo') {
        file = 'postInputPhoto'
      } else {

        file = 'postInputVideo';
      }

        for (let prop in payload) {
          state[file][prop] = payload[prop];
        }
    },

    SET_POST_ERROR: (state, payload) => {

       state.postErrors.push(payload);
    },

    SET_POST_ERRORS: (state, payload) => {

      for (let error in payload) {

        state.postErrors.push(...payload[error]);
      }

    },

    SET_POST_INPUT_TEXT: (state, text) => {

        state.postInputText = text;
        state.postInputTextLength = state.postInputText.trim().length;
    },

    SET_INITIAL_POST_INPUT_TEXT: (state, payload) => {

      const currentUser = `Want to share your thoughts, ${state.currentUserFirstName}?`;
      const userViewed = `Write something to ${payload.viewUserFirstName}...`;

      state.postInputPlaceholder = parseInt(payload.baseProfileUserId) === payload.currentUserId ? currentUser : userViewed;
    },

    OPEN_MODAL_FORM: (state) => {

      state.modalFormIsOpen = true;
    },

    CLOSE_MODAL_FORM: (state) => {

      state.modalFormIsOpen = false;
    },

    CURRENT_USER_NAME: (state, payload) => {
      state.currentUserFullName = payload
        .split(' ')
        .map((word) => {

          return word.substr(0, 1).toUpperCase() + word.substr(1);
        })
        .join(' ');

      let firstName = payload.split(' ')[0];
      state.currentUserFirstName = firstName.substr(0, 1).toUpperCase() + firstName.substr(1);
    },

    RESET_MODULE: (state) => {

      Object.assign(state, initialState());
    },

    DELETE_POST: (state, postDelId) => {
      const delIndex = state.posts.findIndex(post => post.id === postDelId);
      state.posts.splice(delIndex, 1);
    },
  },

  actions: {

    async CREATE_POST ({ state, commit }, payload) {

      try  {

         const data = new FormData();

      let rawData = {
        subject_user_id: payload.subject_user_id,
        author_user_id: payload.author_user_id,
        post_text: state.postInputText,
      }

      data.append('data', JSON.stringify(rawData));
      data.append('videofile', state.postInputVideo.file ?? '');
      data.append('photofile', state.postInputPhoto.file ?? '');

       let response;

        response = await axios(
          {
            url: '/api/auth/posts/store',
            method: 'POST',
            'contentType': false,
            'processData': false,
            headers: {

              'Accept' : 'application/json',
            },
              data
          }
        );

          commit('SET_POST', response.data);

      } catch (e) {

        commit('SET_POST_ERRORS', e.response.data.errors);
      }
    },

    async LOAD_POSTS ({ state, commit }, payload) {

      try {

        let response;

        response = await axios(
          {
            method: 'GET',
            url: `/api/auth/posts?subjectId=${payload}&lastPost=${state.lastPostItem}`,
            headers: {
              'Accept' : 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );
          commit('SET_POSTS', response.data);
          commit('SET_POSTS_LOADED', true);
      } catch(e){
        commit('SET_POSTS_LOADED', false);
      }
    },

    async DELETE_POST({ state, commit }, payload) {

      try {

        let response = await axios(
          {
            method: 'DELETE',
            url: `/api/auth/posts/${payload.id}/delete?user=${payload.currentUserId}`,
            headers: {
              'Accept' : 'application/json',
              'Content-Type': 'application/json'
            }
          }
        );
          console.log('DELETE_POST suc: ', response);
          commit('DELETE_POST', payload.id);
      } catch (e) {

        console.log('DELETE_POST err: ', e.response);
      }
    }
  }
};

export default profileWall;
