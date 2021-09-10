import axios from 'axios';


const initialState = () => {

  return {
    stories: [],
    storyError: '',
    storyType: 'text',
    validationErrors: [],
    isLightBoxActive: false,
    lightBoxStory: '',
    currentUserHasStories: false,
    userIdClicked: null,
    currentUserStories: [],
    newStory: {
      text: '',
      file: {file: null, src: ''},
      alignment: 'center',
      color: 'black',
      duration: {name: '10s', value: 10000},
      font_size: '12px',
      background: 'linear-gradient(90deg, #833ab4 0%, #91fd1d 50%, #fcb045 100%);',
    },
    isDashboardOpen: false,
    isFormOpen: false,
    storiesLocation: 'newsfeed', // newsfeed default | storiesdashboard
  }
};

const stories = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {
    SET_USER_ID_CLICKED(state, userId) {
       state.userIdClicked = userId;
    },

    SET_IS_LIGHTBOX_ACTIVE(state, payload) {
      state.isLightBoxActive = payload;
    },

    SET_LIGHTBOX_STORY(state, { story, btn, user }) {
      const currentStoryIndex = state[user].findIndex(el => el.id === story);

      if (btn === 'next') {
        if (currentStoryIndex >= 0 && currentStoryIndex + 1 < state[user].length) {

          state.lightBoxStory = state[user][currentStoryIndex + 1];
        } else {
          state.isLightBoxActive = false;
          state.lightBoxStory = '';
          state[user] = [];
        }
      } else if (btn === 'prev') {
        if (currentStoryIndex >= 0 && currentStoryIndex - 1 >= 0) {
          state.lightBoxStory = state[user][currentStoryIndex -1];
        } else {
          state.isLightBoxActive = false;
          state.lightBoxStory = '';
          state[user] = [];
        }
      }
    },

    SET_FORM_OPEN(state, payload) {
      state.isFormOpen = payload.isFormOpen;
      state.storyType = payload.storyType;

      state.validationErrors = [];
    },

    SET_STORIES(state, payload) {
      console.log('stories.js|SET_STORIES: ', payload);
      state.stories = [...state.stories, ...payload];
    },

    SET_CURRENT_USER_STORIES(state, payload) {
       state.currentUserHasStories = payload.length ? true : false;

       console.log('stories.js|SET_CURRENT_USER_STORIES: ', payload);

       state.currentUserStories = [...state.currentUserStories, ...payload];
       state.lightBoxStory = state.currentUserStories.slice(0,1)[0];

       if (state.currentUserHasStories) {
         state.userIdClicked = state.lightBoxStory.user_id;
       }
    },

    SET_CURRENT_USER_HAS_STORIES(state, count) {
      state.currentUserHasStories = count > 0 ? true : false;
    },

    SET_VALIDATION_ERRORS(state, { errors }) {
      const validation = [];
      for (let prop in errors) {
        validation.push(...errors[prop]);
      }

      state.validationErrors = [...state.validationErrors, ...validation];
    },

    CLEAR_VALIDATION_ERRORS(state) {
       state.validationErrors = [];
    },

    SET_STORY_ERROR(state, payload) {
      state.storyError = payload;
    },

    CLEAR_STORY_FORM(state) {
       Object.assign(state.newStory,  initialState().newStory);
       state.storyError = '';
       state.validationErrors = [];
    },

    SAVE_PHOTO_FILE(state, photo) {
      state.newStory.file.file = photo.file;
      state.newStory.file.src = photo.src;
    },

    UPDATE_STORY_FIELD(state, payload) {
       for(let prop in state.newStory) {

         if (prop === payload.field) {

           if (prop !== 'duration') {
              state.newStory[prop] = payload.value;
              break;
           } else {
              let duration = parseInt(payload.value.slice(0, 2));

              state.newStory[prop].value = duration * 1000;
              state.newStory[prop].name = payload.value;
              break;
           }
         }
       }
    },

    SET_STORIES_LOCATION(state, location) {
      state.storiesLocation = location;
    },

    SET_DASHBOARD_OPEN(state, payload) {
      state.isDashboardOpen = payload;
    },

    RESET_STORIES_MODULE: (state) => {
     Object.assign(state, initialState());
    },
  },

  actions: {
    async CREATE_STORY({ state, rootGetters, commit }) {

      try {

       const formData = new FormData();

       const allowedKeys = Object.keys(state.newStory).filter(key => key !== 'file');
       const { file } = state.newStory.file;
       const data = { user_id: rootGetters['user/getUserId'], story_type: state.storyType };

       formData.append('file', file ?? '');

        allowedKeys.forEach(key => {
          data[key] = state.newStory[key];
        });

       formData.append('data', JSON.stringify(data));

        const response = await axios(
          {
            method: 'POST',
            url: '/api/auth/stories/create',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: formData,
          }
        );

        if (response.status === 201) {
          console.log('stories.js|CREATE_STORY| Success (201): ', response);
          commit('SET_FORM_OPEN', {storyType: 'text', isFormOpen: false});
          commit('CLEAR_STORY_FORM');
        }
      } catch(e) {
        console.log('stories.js|CREATE_STORY| Error (400): ', e.response);
        if (e.response.status === 422) {
          commit('SET_VALIDATION_ERRORS',e.response.data);
        } else if (e.response.status === 400) {
          commit('SET_STORY_ERROR', e.response.data.error);
        }

      }
    },

    async RETRIEVE_STORY({ state, rootGetters, commit }, userId) {

      try {

        const response = await axios(
          {
            method: 'GET',
            url: `/api/auth/stories/${userId}/show`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );

        if (response.status === 200) {
          console.log('stories.js|RETRIEVE_STORY| Success (200): ', response);
          if (parseInt(rootGetters['user/getUserId']) === parseInt(userId)) {
              commit('SET_CURRENT_USER_STORIES', response.data.stories);
          } else {
            console.log('stories.js|RETRIEVE_STORY| should set user stories not current user stories');
          }
        }
      } catch(e) {
          console.log('stories.js|RETRIEVE_STORY| Error (404): ', e.response);
      }

    },

    async ACTIVE_STORY_COUNT({ state, commit }, userId) {
        try {
          const response = await axios(
            {
              method: 'GET',
              url: `/api/auth/stories/${userId}/count/show`,
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
              }
            }
          );

          if (response.status === 200) {
            commit('SET_CURRENT_USER_HAS_STORIES', response.data.user_stories_count);
          }
        } catch(e) {
          console.log('stories.js|ACTIVE_STORY_COUNT| Error (404): ', e.response);
        }
    },
    async RETRIEVE_BASE_STORIES_DATA() {

      try {

          const response = await axios(
            {
              method: 'GET',
              url: '/api/auth/stories/index',
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
              },
            }
          );

          console.log('stories.js|RETRIEVE_BASE_STORIES_DATA|Success (200): ', response);
      } catch(e) {
          console.log('stories.js|RETRIEVE_BASE_STORIES_DATA|Error (404): ', e.response);
      }
    }

  }
};

export default stories;

