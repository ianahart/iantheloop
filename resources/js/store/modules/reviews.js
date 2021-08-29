import axios from 'axios';


const initialState = () => {

  return {

    reviews: [],
    reviewsLoaded: false,
    alreadySubmitted: false,
    authenticated : false,
    page: 1,
    totalReviews: 0,
    form: [
      {
      field: 'review',
      errors: [],
      label: 'Write Review',
      value: '',
      size: 'md',
      type: 'text',
      nameAttr: 'review',
      }
    ],
    errors: [],
    rating: 0,
  }
};

const reviews = {

  namespaced: true,

  state: initialState(),



  getters: {

  },

  mutations: {

    SET_PAGE(state, payload) {
      state.page = payload;
    },

    SET_TOTAL(state, payload) {
      state.totalReviews = payload;
    },

    SET_REVIEWS_LOADED(state, payload) {
      state.reviewsLoaded = payload;
    },

    RESET_REVIEW_MODULE(state) {
      Object.assign(state, initialState());
    },

    SET_REVIEWS(state, reviews) {
        state.reviews = [...state.reviews, ...reviews];
    },

    CLEAR_ERRORS(state) {
      state.errors = [];
    },

    SET_ERRORS(state, payload) {
      state.errors = [...state.errors, ...payload];
      console.log(state.errors);
    },

    SET_AUTHENTICATED(state, payload) {
      state.authenticated = payload;
    },

    CLEAR_REVIEWS(state) {
      state.reviews = [];
    },

    UPDATE_FIELD(state, payload) {
      state.form.forEach(field => {
        if (payload.error.length) {
          field.errors.push(payload.error);
        }
        delete payload.error;
        Object.assign(field, payload);
      });
    },

    SET_SUBMIT_STATUS(state, status) {
      state.alreadySubmitted = status;
    },

    SET_RATING(state, payload) {
      state.rating = payload;
    },
  },

  actions: {
    /** GET ALL REVIEWS route -> /api/auth/reviews */

    async RETRIEVE_REVIEWS({ state, commit }, order = null) {
      try {

        // if (state.reviews.length) {
        //   commit('CLEAR_REVIEWS');
        // }

        /**Filters: */
        // 1.)'created_at' -> ASC -> newest
        // 2.)'created_at' -> DESC -> oldest
        // 3.) 'rating' -> ASC -> highest rated
        // 4.) 'rating -> DESC -> lowest rated
        /** order */
        // if next +1 to page
        // if prev -1 to page

         const response= await axios(
          {
            method: 'GET',
            url: `/api/auth/reviews/index?page=${state.page}&filters=null`,
            headers:{
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );

        if (response.status === 200) {
            console.log('reviews.js Retrieve_REVIEW Success: ', response);
            commit('SET_AUTHENTICATED', response.data.authenticated);
            commit('SET_SUBMIT_STATUS', response.data.submit_status);
            commit('SET_REVIEWS', response.data.reviews);
            commit('SET_PAGE', response.data.page);
            commit('SET_TOTAL', response.data.total);
            commit('SET_REVIEWS_LOADED', true);
        }
      } catch(e) {
     console.log('reviews.js RETRIEVE_REVIEWS Error: ', e.response);
      }
    },

    async SUBMIT_REVIEW({ state, rootGetters, commit }) {
      try {
        const response = await axios(
          {
            method: 'POST',
            url: '/api/auth/reviews/create',
            headers:{
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: {
              currentUserId: rootGetters['user/getUserId'],
              review: state.form[0].value,
              rating: state.rating,
            }
          });

          if (response.status === 201) {
              commit('SET_SUBMIT_STATUS', true);
          }
      } catch(e) {
        if (e.response.status === 422) {
          commit('SET_ERRORS', e.response.data.errors.review);
        }
      }
    },
  }
}

export default reviews;