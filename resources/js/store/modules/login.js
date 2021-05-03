
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
          console.log(field.errors);
          field.errors.push(payload[field.field]['errors']);

        }
      });
    },
  },

  actions: {

    async SUBMIT_FORM ({ getters, state, commit }) {

      try {
        let response;
        const form = getters.formData;

        response = await axios(
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
        }

      } catch (e) {
        console.log(e.response);
        if (e.response.status === 400) {

          commit('SET_ERRORS', e.response.data);
        }
      }
    }
  }
}

export default login;