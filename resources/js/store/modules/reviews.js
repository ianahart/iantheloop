import axios from 'axios';



const initialState = () => {

  return {

    reviews: [],
    reviewsLoaded: false,
    alreadySubmitted: false,
    currentView: 'review',
    authenticated : false,
    pagination: {total: 0, page: 1, last_page: null},
    totalReviews: 0,
    currentUserReview: [],
    currentUserReviewLoaded: false,
    serverError: '',
    filtersEnabled: false,
    filters: [
        {
        field: 'sort_by',
        errors: [],
        label: '',
        form: null,
        value: 'Newest',
        nameAttr: 'newest',
        defaultValue: 'Newest'
      },
    ],
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

    UPDATE_FILTER(state,  { selection }) {
      state.filtersEnabled = true;
      state.filters.forEach(field => {
        field.value = selection.value;
      });
    },

    SET_SERVER_ERROR(state, serverError) {
      state.serverError = serverError;
    },

    SET_CURRENT_VIEW (state, currentView){
      state.currentView = currentView;
    },

    SET_CURRENT_USER_REVIEW_LOADED(state, payload) {
      state.currentUserReviewLoaded = payload;
    },

    SET_PAGINATION (state, payload) {
      Object.assign(state.pagination, payload);
    },

    UPDATE_PAGINATION(state, { order, property }) {
      if (order === 'next' && property === 'page') {
        if (state.pagination.page < state.pagination.last_page) {
          state.pagination.page = state.pagination.page  + 1;
        }
      }

      if (order === 'prev' && property === 'page') {
        if (state.pagination.page > 1) {
          state.pagination.page = state.pagination.page - 1;
        }
      }
    },

    SET_REVIEWS_LOADED(state, payload) {
      state.reviewsLoaded = payload;
    },

    RESET_REVIEW_MODULE(state) {
      Object.assign(state, initialState());
    },

    SET_CURRENT_USER_REVIEW(state, review) {
      state.form[0].value = review[0].text;
      state.rating = review[0].rating;
      state.currentUserReview = [...state.currentUserReview, ...review];
    },

    UPDATE_CURRENT_USER_REVIEW(state, review) {
      state.currentUserReview[0].text = review.text;
      state.currentUserReview[0].is_edited = review.is_edited;
      state.currentUserReview[0].rating = review.rating;
      state.rating = review.rating;

    },

    CLEAR_CURRENT_USER_REVIEW(state) {
      state.currentUserReview = [];
    },

    SET_REVIEWS(state, reviews) {
        state.reviews = [...state.reviews, ...reviews];
    },

    CLEAR_ERRORS(state) {
      state.errors = [];
    },

    SET_ERRORS(state, payload) {
      state.errors = [...state.errors, ...payload];
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

    async RETRIEVE_REVIEWS({ state, commit }, order = null) {
      try {

        if (state.reviews.length) {
          commit('CLEAR_REVIEWS');
        }

        if (state.pagination.last_page !== null) {
          commit('UPDATE_PAGINATION', { order, property: 'page' });
        }

        let filter = '';
        if (state.filtersEnabled) {
          filter = state.filters[0].value.toLowerCase();
        }
         const response= await axios(
          {
            method: 'GET',
            url: `/api/auth/reviews/index?page=${state.pagination.page}&filters=${filter}`,
            headers:{
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            }
          }
        );

        if (response.status === 200) {
            commit('SET_AUTHENTICATED', response.data.authenticated);
            commit('SET_SUBMIT_STATUS', response.data.submit_status);
            commit('SET_REVIEWS', response.data.reviews);
            commit('SET_PAGINATION', response.data.pagination);
            commit('SET_REVIEWS_LOADED', true);
        }
      } catch(e) {
        commit('SET_SERVER_ERROR', e.response.data.error);
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

    async RETRIEVE_REVIEW({ state, rootGetters, commit }) {

      try {

        const response = await axios(
          {
            method: 'GET',
            url: `/api/auth/reviews/${rootGetters['user/getUserId']}/show`,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          }
        );

       if (response.status === 200) {
         commit('SET_CURRENT_USER_REVIEW', response.data.review);
         commit('SET_CURRENT_USER_REVIEW_LOADED', true);
       }
      } catch(e) {
        commit('SET_SERVER_ERROR', e.response.data.error);
      }
    },

    async UPDATE_REVIEW({ state, rootGetters, commit }) {
      try {

        const response = await axios(
          {
            method: 'PATCH',
            url: `/api/auth/reviews/${state.currentUserReview[0].id}/update`,
            headers:{
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: {
              currentUserId: rootGetters['user/getUserId'],
              review: state.form[0].value,
              rating: state.rating,
            }
          }
        );
        if (response.status === 200) {
          commit('UPDATE_CURRENT_USER_REVIEW', response.data.review);
          commit('SET_CURRENT_VIEW', 'review');
        }
      } catch(e) {
        commit('SET_SERVER_ERROR', e.response.data.msg);
      }
  },

  async DELETE_REVIEW({ state, rootGetters, commit }) {
     try {

       const response = await axios(
         {
           method: 'DELETE',
           url: `/api/auth/reviews/${state.currentUserReview[0].id}/delete?userId=${state.currentUserReview[0].user_id}`,
           headers: {
             'Accept': 'application/json',
             'Content-Type': 'application/json',
           },
         }
        );

        if (response.status === 200) {
          commit('CLEAR_CURRENT_USER_REVIEW');
        }
     } catch (e) {
       console.log('DELETE_REVIEW review.js Error: ', e.response);
       commit('SET_SERVER_ERROR', e.response.data.error);
     }
  }
  }
}

export default reviews;