import axios from 'axios';
import { updateVisibility, configureFormData } from '../../helpers/moduleHelpers.js';

const initialState = () => {

  return {
    hasErrors: false,
    globalResetError: '',
    messageSent: '',
    formSubmitted: false,
    resetToken: '',
    forgotForm: [
      {field: 'email', errors: [], label: 'Your Email', value: '',size: 'lg', type: 'text', nameAttr: 'email'}
      ],
    resetForm: [
            {field: 'password', errors: [], label: 'New Password', value: '',size: 'lg', type: 'password', nameAttr: 'visiblepassword'},
            {field: 'password_confirmation', errors: [], label: 'Confirm Password', value: '',size: 'lg', type: 'password', nameAttr: 'confirm'}
    ],
  }
};

const passwordRecovery = {

  namespaced: true,

  state: initialState(),

  getters: {

      forgetFormData: (state) => {

        return configureFormData(state, 'forgotForm');
    },

    resetFormData: (state) => {

      return configureFormData(state, 'resetForm');
    },
  },
  mutations: {

    TOGGLE_PASSWORD_VISIBILITY: (state, payload) => {


      state.resetForm = updateVisibility(state, 'resetForm', payload.isPasswordShowing);

    },

    UPDATE_FIELD: (state, payload) => {

      state[payload.form].find((oldField) => {

          if (oldField.field === payload.field) {

              oldField.value = payload.value;

              oldField.errors.push(payload.error);

              state.hasErrors = oldField.errors.length ? true : false;
          }
        }
      );
    },


    CLEAR_ERROR_MSGS: (state) => {

      state.globalResetError = '';

      state.hasErrors = false;

      state.resetForm.forEach((field) => {

        field.errors = [];
       }
      );

      state.forgotForm.forEach((field) => {

         field.errors = [];
        }
      );
    },

    SUBMIT_FORM: (state, payload) => {

      state.formSubmitted = payload.formSubmitted;
    },

        SET_ERRORS: (state, payload) => {

      state.formSubmitted = payload.formSubmitted;

       state[payload.form].forEach((field) => {


        const keys = Object.keys(payload.errors);

        if (keys.includes(field.field)) {

          field.errors.push(...payload.errors[field.field]);
        }

       });

    },

    MESSAGE_SENT: (state) => {

      state.messageSent = 'Email Sent';
    },

    GET_RESET_TOKEN: (state, payload) => {

      state.resetToken = payload;
    },

    RESET_MODULE: (state) => {


     Object.assign(state, initialState());
    },

    SET_EXPIRED_MSG: (state, payload) => {
      state.globalResetError = payload;
    }
  },

  actions: {

      TOGGLE_PASSWORD_VISIBILITY ({ rootState, commit }) {

        commit('TOGGLE_PASSWORD_VISIBILITY', rootState);
      },

      async SEND_EMAIL({ commit, getters }) {

        try {

          let response;

          const formData = getters.forgetFormData;

          response = await axios(
            {
              method: 'POST',
              url: '/api/auth/recovery/',
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              data: {

                formData,
                formName: 'forgotForm',
              }
            }
          );



            if (response.status === 200) {

              commit('SUBMIT_FORM', response.data);

              commit('MESSAGE_SENT');

            }

        } catch (e) {

          if(e.response.status === 422 || e.response.status === 404) {

            commit('SET_ERRORS', e.response.data);
          }
        }
    },

    async RESET_PASSWORD({ state, commit, getters }) {

      try {

            let response;

            const formData = getters.resetFormData;

            response = await axios(
              {
                method: 'POST',
                url: '/api/auth/reset-password/',
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                },
                data: {

                  formData,
                  formName: 'resetForm',
                  resetToken: state.resetToken,
                }
              }
            );

          if (response.status === 200) {

            commit('SUBMIT_FORM', response.data);

          }

      } catch (e) {

        if(e.response.status === 422) {

          commit('SET_ERRORS', e.response.data);
        }

        if (e.response.status < 422) {

          commit('SET_EXPIRED_MSG', e.response.data.error);
        }
      }
    },
  }
};

export default passwordRecovery;
