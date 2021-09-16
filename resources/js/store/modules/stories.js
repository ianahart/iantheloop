import axios from 'axios';


const initialState = () => {

  return {
    stories: [],
    carousel: [],
    carouselNavigation: { start: 0, end: 0, navigation: '', seenPages: [] ,url: '/api/auth/stories/index', baseUrl: '/api/auth/stories/index'},
    baseStories: [],
    currentUserHasStories: false,
    pagination: { current_page: 1, path: '/api/auth/stories/index' },
    userIdClicked: null,
    currentUserStories: [],
    validationErrors: [],
    isLightBoxActive: false,
    lightBoxStory: '',
    storyError: '',
    storyType: 'text',
    newStory: {
      text: '',
      file: { file: null, src: '' },
      alignment: 'center',
      color: 'black',
      duration: { name: '10s', value: 10000 },
      font_size: '12px',
      background: 'linear-gradient(90deg, #833ab4 0%, #91fd1d 50%, #fcb045 100%);',
    },
    isDashboardOpen: false,
    isFormOpen: false,
    storiesLocation: '',
  }
};

const stories = {

  namespaced: true,

  state: initialState(),

  getters: {

  },

  mutations: {

     NAVIGATE_CAROUSEL(state, navigation) {
         state.carouselNavigation.navigation = navigation;
          if (state.carouselNavigation.start === 0 && state.carouselNavigation.end === 0) {
           state.carouselNavigation.start = 0;
           state.carouselNavigation.end = state.pagination.per_page;
           state.carousel = state.baseStories.slice(state.carouselNavigation.start, state.carouselNavigation.end);
            return;
         }

         if (state.carouselNavigation.navigation === 'next') {
          state.carousel = state.baseStories.slice(state.carouselNavigation.end, state.carouselNavigation.end + state.pagination.per_page);
          state.carouselNavigation.end = state.carouselNavigation.end + state.pagination.per_page;
          state.carouselNavigation.start = state.carouselNavigation.end - state.pagination.per_page;
            return;
         } else if (state.carouselNavigation.navigation === 'prev') {
            state.carouselNavigation.end = state.carouselNavigation.end - state.pagination.per_page;
            state.carouselNavigation.start = state.carouselNavigation.start - state.pagination.per_page;
            state.carousel = state.baseStories.slice(state.carouselNavigation.start, state.carouselNavigation.end);
            return;
         }
     },

    SET_USER_ID_CLICKED(state, userId) {
       state.userIdClicked = userId;
    },

    SET_PAGINATION(state, pagination) {
      for (let prop in pagination) {
        const newsFeedFilter = prop !== 'data' && prop !== 'current_page';
        const storiesDashboardFilter = prop !== 'data';
        const filteredApplied = state.location === 'NewsFeed' ? newsFeedFilter : storiesDashboardFilter;

        if (filteredApplied) {
           state.pagination[prop] = pagination[prop];
        }
      }
    },

    SET_PAGINATION_PAGE(state, action) {
      if (action === 'next') {
        state.pagination.current_page = state.pagination.current_page + 1;
        state.carouselNavigation.url = `${state.carouselNavigation.baseUrl}?page=${state.pagination.current_page}`;

      } else if (action === 'prev') {
        state.pagination.current_page = state.pagination.current_page -1;
        state.carouselNavigation.url = `${state.carouselNavigation.baseUrl}?page=${state.pagination.current_page}`;
      }
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

    SET_BASE_STORIES(state, payload) {
      state.baseStories = [...state.baseStories, ...payload];
    },

    CLEAR_BASE_STORIES(state) {
      state.baseStories = [];
       state.pagination = { current_page: 1, path: '/api/auth/stories/index' };
       state.carouselNavigation = { start: 0, end: 0, navigation: '', seenPages: [] ,url: '/api/auth/stories/index', baseUrl: '/api/auth/stories/index'};
    },

    SET_STORIES(state, payload) {
      if (payload.length === 1 && payload[0].displayed_time.toLowerCase() === 'just now') {
        if (payload[0].user_id === state.stories[0].user_id) {
           state.stories = [...state.stories, ...payload];
            return;
        }
      } else {
        state.stories = [];
        state.stories = [...state.stories, ...payload];

        state.isLightBoxActive = true;
        state.lightBoxStory = state.stories.slice(0,1)[0];
      }
    },

    CLEAR_CURRENT_USER_STORIES(state) {
      state.currentUserStories = [];
    },

    SET_CURRENT_USER_STORIES(state, payload) {
       state.currentUserHasStories = payload.length ? true : false;
       state.currentUserStories = [...state.currentUserStories, ...payload];
       state.lightBoxStory = state.currentUserStories.slice(0,1)[0];

       if (state.currentUserHasStories) {
         state.userIdClicked = state.lightBoxStory.user_id;
       }
    },

    CLEAR_LIGHTBOX_STORY(state) {
      state.lightBoxStory = '';
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

    REMOVE_CURRENT_USER_STORY(state, lightBoxStory) {
      const storyIndex = state.currentUserStories.findIndex(story => story.id === lightBoxStory.id);
      if (state.currentUserStories.length === 1) {
        state.lightBoxStory = '';
        state.currentUserHasStories = false;
        return;
      }
      if (state.currentUserStories.length - 1 < storyIndex +1) {
        state.lightBoxStory = '';
        state.isLightBoxActive = false;
        return;
      }
      state.lightBoxStory = state.currentUserStories[storyIndex + 1];
      state.currentUserStories.splice(storyIndex,1);
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
          commit('SET_FORM_OPEN', {storyType: 'text', isFormOpen: false});
          commit('CLEAR_STORY_FORM');
        }
      } catch(e) {
        if (e.response.status === 422) {
          commit('SET_VALIDATION_ERRORS',e.response.data);
        } else if (e.response.status === 400) {
          commit('SET_STORY_ERROR', e.response.data.error);
        }

      }
    },

    async RETRIEVE_STORY({ state, rootGetters, commit }, userId) {

      try {
        state.lightBoxStory = '';
        state.isLightBoxActive = false;
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
          if (parseInt(rootGetters['user/getUserId']) === parseInt(userId)) {
              commit('SET_CURRENT_USER_STORIES', response.data.stories);
          } else {
            commit('SET_STORIES', response.data.stories);
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
    async RETRIEVE_BASE_STORIES_DATA({ state, commit }) {

      try {
        const paginationExists = Object.keys(state.pagination).length > 2;
        let url;

        if (state.storiesLocation === 'NewsFeed' && state.carouselNavigation.seenPages.includes(state.pagination.current_page)) {
            return;
          } else {
            state.carouselNavigation.seenPages.push(state.pagination.current_page);
          }

        if (state.storiesLocation === 'NewsFeed') {
           url = paginationExists && state.storiesLocation === 'NewsFeed' ? `${state.carouselNavigation.url}` : state.carouselNavigation.baseUrl;

        }  else  {
          url = state.pagination.next_page_url ? state.pagination.next_page_url : state.pagination.path;
        }

          const response = await axios(
            {
              method: 'GET',
              url,
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
              },
            }
          );

          if (response.status === 200) {
            const { stories } = response.data

            commit('SET_PAGINATION', stories);
            commit('SET_BASE_STORIES', stories.data);
          }
      } catch(e) {
          console.log('stories.js|RETRIEVE_BASE_STORIES_DATA|Error (404): ', e.response);
      }
    },

    async REMOVE_CURRENT_USER_STORY ({ state, rootGetters, commit, }, story) {
      try {
        const response = await axios({
            method: 'DELETE',
            url: `/api/auth/stories/${story.id}/delete?userId=${rootGetters['user/getUserId']}`,
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', }
          });
        if (response.status === 200) {
          commit('REMOVE_CURRENT_USER_STORY', story);
        }
        console.log('stories.js|REMOVE_CURRENT_USER_STORY|Error (200): ', response);
      } catch(e) {
        console.log('stories.js|REMOVE_CURRENT_USER_STORY|Error (403): ', e.response);
      }
    }
  }
};1

export default stories;
