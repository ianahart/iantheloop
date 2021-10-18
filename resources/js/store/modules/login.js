
import axios from 'axios';

import { inputChange, getFormData } from '../../helpers/moduleHelpers.js';

const initialState = () => {

  return {
    form: [
          {field: 'email', errors: [], label: 'Your Email', value: '',size: 'lg', type: 'text', nameAttr: 'email'},
          {field: 'password', errors: [], label: 'Your Password', value: '',size: 'lg', type: 'password', nameAttr: 'password'},
    ],
    formSubmitted: false,
    hasErrors: false,
    isLoginLoaderShowing: false,
  };
};


const login = {

  namespaced: true,

  state: initialState(),

  getters: {

    formData: (state) => {

      return getFormData(state);
    }
  },

  mutations: {

    SET_IS_LOGIN_LOADER_SHOWING(state, isLoading) {
      state.isLoginLoaderShowing = isLoading;
    },

    UPDATE_FIELD: (state, payload) => {

      inputChange(state, payload);
    },

    RESET_LOGIN_MODULE: (state) => {

      Object.assign(state, initialState());
    },

    CLEAR_ERROR_MSGS: (state) => {

      state.hasErrors = false;

      state.form.forEach((field) => {

         field.errors = [];
        }
      );
    },

    SUBMIT_FORM: (state, payload) => {

      state.formSubmitted = payload.formSubmitted;
    },

    SET_ERRORS: (state, payload) => {

      state.formSubmitted = payload.formSubmitted;

      const keys = Object.keys(payload);

      state.form.forEach((field) => {

        if (keys.includes(field.field)) {

          field.errors.push(payload[field.field]['errors']);

        }
      });
    },
  },

  actions: {

    async SUBMIT_FORM ({ getters, state, commit }) {
      try {
        const form = getters.formData;
        const response = await axios(
          {
            method: 'POST',
            url: '/api/auth/login',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            data: {
              form,
            },
          }
        );

        if (response.status === 200) {
          commit('SUBMIT_FORM', response.data);
          commit('user/SET_TOKEN', response.data.jwt, { root: true });
          commit('SET_IS_LOGIN_LOADER_SHOWING', false);
        }

      } catch (e) {
        if (e.response.status === 400) {
          commit('SET_ERRORS', e.response.data);
          commit('SET_IS_LOGIN_LOADER_SHOWING', false);
        }
      }
    }
  }
}

export default login;