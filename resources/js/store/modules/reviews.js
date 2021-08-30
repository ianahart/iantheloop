import axios from 'axios';
import { property } from 'lodash';


const initialState = () => {

  return {

    reviews: [],
    reviewsLoaded: false,
    alreadySubmitted: false,
    authenticated : false,
    pagination: {total: 0, page: 1, last_page: null},
    totalReviews: 0,
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