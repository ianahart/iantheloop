import axios from 'axios';
import { updateVisibility, inputChange, getFormData } from '../../helpers/moduleHelpers';

const initialState = () => {

  return {

    form: [
          {field: 'firstName', errors: [], label: 'First Name', value: '',size: 'md', type: 'text', nameAttr: 'firstname'},
          {field: 'lastName', errors: [], label: 'Last Name', value: '', size: 'md', type: 'text', nameAttr: 'lastname'},
          {field: 'email', errors: [], label: 'Email', value: '', size: 'lg', type: 'text', nameAttr: 'email'},
          {field: 'password', errors: [], label: 'Create Password', value: '', size: 'lg', type: 'password', nameAttr: 'visiblepassword'},
          {field: 'password_confirmation', errors: [], label: 'Confirm Password', value: '', size: 'lg', type: 'password', nameAttr: 'confirmpassword'},
    ],
    hasErrors: false,
    isSubmitted: false,
    isChecked: false,
    checkboxError: '',
    userId: null,
    // isPasswordShowing: false,
    emailExistsError: '',
  }
}

const createAccount = {

  namespaced: true,



  state: initialState(),

  getters: {

    mediumFields: (state) => {

      return state
      .form
      .filter(
        (field) => {

        return field.size === 'md';
        }
      );
    },

    largeFields: (state) => {

      return state
      .form
      .filter(
        (field) => {

        return field.size === 'lg';
        }
      );
    },

    formData: (state) => {

     return getFormData(state);
    },

    getFieldNames: (state) => {

      return state.form
      .map(
          (field) => {

          return field.field;
        }
     );
    },
  },

  mutations: {
    SET_USER_ID(state, userId) {
      state.userId = userId;
    },

    TOGGLE_PASSWORD_VISIBILITY: (state, payload) => {

      state.resetForm = updateVisibility(state, 'form', payload.isPasswordShowing);
    },

    CHECKBOX_ERROR: (state, payload) => {

      state.checkboxError = payload;
    },

    TOGGLE_CHECKBOX: (state) => {

      state.isChecked = !state.isChecked;
    },

    RESET_ERRORS: (state) => {

      state.hasErrors =  false;

      state.checkboxError = '';

      state.form
      .forEach(
          (field) => {

          field.errors = [];
        }
      );
    },

    SET_VALIDATION_ERRORS: (state, payload) => {

      const newErrorFields = [];

      for (let obj in payload.errors) {

       const fieldName = obj.split('.')[1];

       const newErrorField = {[fieldName]:  payload.errors[obj]};

        newErrorFields.push(newErrorField);
      }

      newErrorFields.forEach((newErrorField, index) => {

        const errorKey = Object.keys(newErrorField)[0];

        state.form.forEach((oldField) => {

            if (errorKey === oldField.field) {

              oldField.errors.push(...newErrorFields[index][errorKey])
            }
        })
      });
    },

    UPDATE_FIELD: (state, payload) => {

      inputChange(state, payload);

    },


    SET_EMAIL_EXISTS_ERROR: (state, payload) => {

      state.form
      .forEach(
        (oldField) => {

        if (oldField.field === 'email') {

          oldField.errors.push(payload);
        }
      });
    },

    SUBMIT_FORM: (state, payload) => {

      state.isSubmitted = payload;
    },

    RESET_MODULE: (state) => {

      Object.assign(state, initialState());
    },
  },

  actions: {



      TOGGLE_PASSWORD_VISIBILITY ({ rootState, commit }) {

      commit('TOGGLE_PASSWORD_VISIBILITY', rootState);
    },

     async SUBMIT_FORM ({getters, state, commit }) {

      let response;

        try {

          const formData = getters.formData;

          response = await axios(
            {
              method: 'POST',
              url: '/api/auth/register',
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
              },
              data: {

                formData,
              }
          }
          );

          if (response.status === 201) {
            commit('SET_USER_ID', response.data.user_id);
            commit('SUBMIT_FORM', response.data.isSubmitted);
          }

        } catch(e) {

            const { errors } = e.response.data;

            if (e.response.status === 422) {

              // console.log(e.response.data.errors);

              commit('SET_VALIDATION_ERRORS',
                {
                  errors,
                  fields: getters.getFieldNames
                }
              );
            }

            if (e.response.status === 409) {

              commit('SET_EMAIL_EXISTS_ERROR', errors);
            }
        }
    }
  }
};


export default createAccount;


