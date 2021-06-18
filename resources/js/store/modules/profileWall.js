import axios from 'axios';


const initialState = () => {

  return {
    postsLoaded: false,
    modalIsOpen: false,
    responseError: false,
    flagPostFinished: false,
    requestFinished: false,
    morePosts: true,
    postInputTextLength: null,
    activeFlagPostId: null,
    lastPostItem: 0,
    alreadyFlaggedError: '',
    activeModal: '',
    currentUserFullName: '',
    currentUserFirstName: '',
    postInputPlaceholder: '',
    postInputText: '',
    postInputPhoto: { src: '', file: null, input: ''},
    postInputVideo: {src: '', file: null, input: ''},
    commentsLoaded: {message: '', postId: ''},
    posts: [],
    postErrors: [],
    commentErrors:[],
    replyErrors: [],
    flaggedOptions:[
      {id: 0, reasonText: 'Violence', selected: false},
      {id: 1, reasonText: 'Nudity', selected: false},
      {id: 2, reasonText: 'Harassment', selected: false},
      {id: 3, reasonText: 'False Information', selected: false},
      {id: 4, reasonText: 'Spam', selected: false},
      {id: 5, reasonText: 'Hate Speech', selected: false},
    ],
  }
};

const profileWall = {

  namespaced: true,

  state: initialState(),

  getters: {

    unselectedFlaggedOptions (state) {
         return state.flaggedOptions.filter((flaggedOption) => !flaggedOption.selected);
    },

    selectedFlaggedOptions (state) {
      return state.flaggedOptions.filter((flaggedOption) => flaggedOption.selected);
    }
  },

  mutations: {

    SET_FLAGGED_OPTION: (state, payload) => {
      const index = state.flaggedOptions.findIndex(flaggedOption => flaggedOption.id === payload.option.id);
      state.flaggedOptions[index].selected = payload.action === 'selected' ? true : false;
    },

    SET_POSTS: (state, payload) => {

      if (payload.posts !== null && payload.more_records) {

        state.posts.push(...payload.posts);
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
      payload.new_post.post_likes = [];
      payload.new_post.post_comments = [];

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

    OPEN_MODAL: (state, payload) => {

      state.modalIsOpen = true;
      state.activeModal = payload.modal;

      if (payload.activeFlagPostId !== null) {
        state.activeFlagPostId = payload.activeFlagPostId;
      }
    },

    CLOSE_MODAL: (state) => {

      state.modalIsOpen = false;
      state.activeModal = '';
      state.activeFlagPostId = null;
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

    RESPONSE_ERROR: (state) => {

      state.responseError = true;
    },


    LIKE_POST:(state, { new_like }) => {
      const postIndex = state.posts.findIndex((post) => post.id === new_like.post_id);

      state.posts[postIndex].post_likes =  [];
      state.posts[postIndex].post_likes.push(new_like);
      state.posts[postIndex].likes = state.posts[postIndex].likes + 1;
    },

    UNLIKE_POST: (state, payload) => {

      const postIndex = state.posts.findIndex(post => post.id === payload.post_id);
      const likeIndex = state.posts[postIndex].post_likes.findIndex(lk => lk.id === payload.id);

      state.posts[postIndex].post_likes.splice(likeIndex, 1);
    },

    SET_ALREADY_FLAGGED_ERROR: (state, payload) => {

      state.alreadyFlaggedError = payload;
    },

    CLEAR_ALREADY_FLAGGED_ERROR:(state) => {

      state.alreadyFlaggedError = '';
    },

    RESET_FLAGGED_OPTIONS: (state) => {

      state.flaggedOptions.forEach((flaggedOption) => {

        flaggedOption.selected = false;
      });
    },

    FLAG_POST_FINISHED : (state, payload) => {
      state.flagPostFinished = payload;
    },

    SET_COMMENT: (state, { latest_comment }) => {

      const index = state.posts.findIndex(post => post.id === latest_comment.post_id);
      if (!Object.keys(state.posts[index]).includes('post_comments')) {

        state.posts[index].post_comments = [];
        state.posts[index].post_comments.unshift(latest_comment);
        state.posts[index].comments_count++;

        return;
      }
      state.posts[index].post_comments.unshift(latest_comment);
      state.posts[index].comments_count++;
    },

    SET_REPLY_COMMENT: (state, { reply_comment }) => {

        const index = state.posts.findIndex((post) => post.id === reply_comment.post_id);
        const commentIndex = state.posts[index].post_comments.findIndex((postComment) => postComment.id === reply_comment.reply_to_comment_id);

        if (!Object.keys(state.posts[index].post_comments[commentIndex]).includes('reply_comments')) {

          state.posts[index].post_comments[commentIndex].reply_comments = [];
            return;
        }

      state.posts[index].post_comments[commentIndex].reply_comments.unshift(reply_comment);
      state.posts[index].post_comments[commentIndex].reply_comments_count++;
    },

    SET_COMMENT_ERROR:(state, payload) =>{

      if (payload.response.status === 422) {

        const [ error ] = payload.response.data.errors.input;

        state.commentErrors.push(
          {
            message: error,
            post_id: payload.post_id
          }
        )
      } else if (payload.response.status > 399 && payload.response.status < 422) {
        state.commentErrors.push(
          {
            message: payload.response.data.error[0],
            post_id: payload.post_id,
          }
        )
      }
    },

    SET_REQUEST_FINISHED: (state, payload) => {

      state.requestFinished = payload;
    },

    RESET_COMMENT_ERRORS: (state) => {

      state.commentErrors = [];
    },

    DELETE_COMMENT: (state, payload) => {
      const postIndex = state.posts.findIndex((post) => post.id === payload.postID);
      const commentIndex = state.posts[postIndex].post_comments.findIndex((comment) => comment.id === payload.commentID);
      state.posts[postIndex].post_comments.splice(commentIndex, 1);
    },

    REFILL_COMMENTS: (state, payload) => {

      state.posts[payload.postIndex].post_comments.push(...payload.post_comments);
    },

    SET_COMMENTS_LOADED:(state, payload) => {

      state.commentsLoaded = payload;
    },

    REACT_COMMENT: (state, payload) => {
      const postIndex = state.posts.findIndex((post) => post.id === payload.post_id);
      const commentIndex = state.posts[postIndex].post_comments.findIndex((comment) => comment.id === payload.comment_id);

      if (payload.action === 'like') {
        state.posts[postIndex].post_comments[commentIndex].comment_likes.push(payload);
        state.posts[postIndex].post_comments[commentIndex].likes++;
      } else if (payload.action === 'unlike') {
        const index = state.posts[postIndex].post_comments[commentIndex].comment_likes.findIndex((like) => like.id === payload.id)
        state.posts[postIndex].post_comments[commentIndex].comment_likes.splice(index, 1);
        state.posts[postIndex].post_comments[commentIndex].likes--;
      }
    },

    SET_REPLY_ERRORS: (state, payload) => {

      if (payload.action === 'set') {
        state.replyErrors.push(payload);
        return;
      } else {
        const error = state.replyErrors.findIndex((error) => error.commentId === payload.commentId);
        state.replyErrors.splice(error, 1);
      }
    },

    SET_REFILL_REPLIES: (state, { replyComments, postId, commentRepliedTo }) => {

      const post = state.posts.findIndex(post => post.id === postId);
      const postComment = state.posts[post].post_comments.findIndex(comment => comment.id === commentRepliedTo);
      state.posts[post].post_comments[postComment].reply_comments.push(...replyComments);
    },

    DELETE_REPLY_COMMENT: (state, payload) => {
      const post = state.posts.findIndex(post => post.id === payload.postID);
      const comment = state.posts[post].post_comments.findIndex(postComment => postComment.id === payload.replyID);
      const replyComment = state.posts[post].post_comments[comment].reply_comments.findIndex(replyComment => replyComment.id === payload.commentID);

      state.posts[post].post_comments[comment].reply_comments.splice(replyComment, 1);
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
          commit('DELETE_POST', payload.id);
      } catch (e) {
        commit('RESPONSE_ERROR');
      }
    },

    async LIKE_POST({ state, commit }, payload) {

      try {

        let response = await axios(
          {
            method: 'POST',
            url: `/api/auth/post-likes/store`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: {
              current_user_id: payload.currentUserId,
              post_id: payload.postId,
            }
          }
        );

       commit('LIKE_POST', response.data);
      } catch (e) {

        console.log(e.response);
      }
    },

    async UNLIKE_POST({ state, commit }, payload) {

      try {

        let response = await axios(
          {
            method: 'DELETE',
            url: `/api/auth/post-likes/${payload.id}/delete`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: payload,
          }
          )
        commit('UNLIKE_POST', payload);
      } catch(e) {

      }
    },

  async FLAG_POST ({ state, getters, commit }, payload) {

    try {

      const response = await axios(
          {
            method: 'POST',
            url: `/api/auth/flagged-posts/store`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: {
                post_id: payload.postId,
                user_id: payload.userId,
                reasons: getters.selectedFlaggedOptions,
            },
          }
        );

        commit('FLAG_POST_FINISHED', true);

    } catch (e) {

          commit('SET_ALREADY_FLAGGED_ERROR', e.response.data.error);
          commit('FLAG_POST_FINISHED', true);
    }
  },

  async ADD_COMMENT ({ state, commit }, payload) {

    try {

      const response = await axios(
        {
          method: 'POST',
          url: `/api/auth/comments/store`,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          data: payload,
        }
      );

      commit('SET_COMMENT', response.data);

      commit('SET_REQUEST_FINISHED', true);
    } catch(e) {
      commit('SET_COMMENT_ERROR', { response: e.response, post_id: payload.post_id });
      commit('SET_REQUEST_FINISHED', true);
    }
  },

  async DELETE_COMMENT({ state, commit }, payload) {

    try {

      const response = await axios(
        {
          method: 'DELETE',
          url: `/api/auth/comments/${payload.commentID}/delete?uid=${payload.userID}&type=${payload.type}`,
          headers: {
             'Accept': 'application/json',
             'Content-Type': 'application/json',
          }
        }
      );

      if (response.status === 200) {

        commit('DELETE_COMMENT', payload);
      }
    } catch(e) {
     // returns a 403
     // setup later on
    }
  },

  async REFILL_COMMENTS ({ state, commit }, payload) {

    try {

      const postIndex = state.posts.findIndex(post => post.id === payload);
      const lastComment = state.posts[postIndex].post_comments[state.posts[postIndex].post_comments.length - 1];

      const response = await axios(
          {
            method: 'GET',
            url: `/api/auth/posts/${payload}/comments/show?last=${lastComment.id}`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );

        if (response.status === 200) {
          commit('REFILL_COMMENTS',
          {
            post_comments: response.data.post_comments,
            postIndex,
          });
        }

    } catch(e) {

      commit('SET_COMMENTS_LOADED',{message: e.response.data.error, postId: payload});

    }
  },
  async LIKE_COMMENT({ state, commit }, payload) {

    try {

      const response = await axios(
        {
          method: 'POST',
          url: `/api/auth/comment-likes/store`,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          data: payload,
        }
      );

        if (response.status === 200) {
            payload.id = response.data.comment_like;
            commit('REACT_COMMENT', payload);
        }

    } catch(e) {

      // console.log('Actions @LIKE_COMMENT (error): ', e.response);
    }
  },

  async UNLIKE_COMMENT( {state, commit}, payload) {

    try {

      const response = await axios(
        {
          method: 'DELETE',
          url: `/api/auth/comment-likes/${payload.id}/delete?commentId=${payload.comment_id}&action=${payload.action}&userId=${payload.user_id}`,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
        }
      );

      if (response.status === 200) {

        commit('REACT_COMMENT', payload);
      }

    } catch(e) {
        // console.log('Actions @UNLIKE_COMMENT (error): ', e.response);
    }
  },

  async ADD_REPLY_COMMENT({ state, commit }, payload) {

    try {

      const response = await axios(
        {
          method: 'POST',
          url: '/api/auth/comments/reply/store',
          headers: {
            'Accept' : 'application/json',
            'Content-Type': 'application/json',
          },

          data: payload,
        }
      );

      commit('SET_REPLY_COMMENT', response.data);
      commit('SET_REQUEST_FINISHED', true);
    } catch(e) {

      const { error } = e.response.data

      commit('SET_REPLY_ERRORS', { error, commentId: payload.reply_to_comment_id, action: 'set' })
      commit('SET_REQUEST_FINISHED', true);
    };
  },

  async REFILL_REPLIES({ state, commit }, payload) {

    try {

      const response = await axios(
        {
          method: 'GET',
          url: `/api/auth/posts/${payload.post_id}/comments/reply/show?last=${payload.last_reply_comment_id}&replyTo=${payload.comment_id}`,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          }
        }
      );

      commit('SET_REFILL_REPLIES', response.data);

    } catch (e) {

      console.log('action: @REFILL_REPLIES Error: ', e.response);
    }
  },

  async DELETE_REPLY_COMMENT({ state, commit }, payload) {

    try {

      const response = await axios(
        {
          method: 'DELETE',
          url: `/api/auth/comments/reply/${payload.commentID}/delete?=uid=${payload.userID}&type=${payload.type}`,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
        }
      );

      if (response.status === 200) {

        commit('DELETE_REPLY_COMMENT', payload);
      };

    } catch (e) {

      console.log('DELETE_REPLY_COMMENT--action--error: ', e.response);
    }
  },
 }
};

export default profileWall;


