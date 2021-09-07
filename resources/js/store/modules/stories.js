import axios from 'axios';


const initialState = () => {

  return {
    stories: [],
    storyTextError: '',
    storyType: 'text',
    validationErrors: [],
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

    SET_FORM_OPEN(state, payload) {
      state.isFormOpen = payload.isFormOpen;
      state.storyType = payload.storyType;

      state.validationErrors = [];
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

    SET_STORY_TEXT_ERROR(state, payload) {
      state.storyTextError = payload;
    },

    CLEAR_STORY_FORM(state) {
       Object.assign(state.newStory,  initialState().newStory);
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
          console.log('stories.js|CREATE_STORY| Success Response: ', response);
          commit('SET_FORM_OPEN', {storyType: 'text', isFormOpen: false});
        }
      } catch(e) {
        console.log('stories.js|CREATE_STORY| Error Response: ', e.response);
        commit('SET_VALIDATION_ERRORS',e.response.data);
      }
    }
  }
};

export default stories;
