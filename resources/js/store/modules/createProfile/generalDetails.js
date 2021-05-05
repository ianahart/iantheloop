
import axios from "axios";


import {
    getFormData,
    inputChange,
    pluckField,
    errorsPresent
  } from '../../../helpers/moduleHelpers.js';

const initialState = () => {

  return {

       form: [
          {field: 'displayname', errors: [], label: 'Display Name', value: '',size: 'md', type: 'text', nameAttr: 'displayname'},
          {field: 'town', errors: [], label: 'Town', value: '',size: 'md', type: 'text', nameAttr: 'town'},
          {field: 'state', errors: [], label: 'State', value: '',size: 'sm', type: 'text', nameAttr: 'state'},
          {field: 'country', errors: [], label: 'Country', value: '',size: 'md', type: 'text', nameAttr: 'country'},
          {field: 'phone', errors: [], label: 'Phone', value: '',size: 'md', type: 'text', nameAttr: 'phone'},
    ],
    generatedFieldsCounter: 0,
    formSubmitted: false,
    errorsPresent: false,
    formName: 'generalDetails',
  }
};

const generalDetails = {

  namespaced: true,

  state: initialState(),

  getters: {

    getGeneralDetails (state) {

      return getFormData(state);
    },
    getDisplayName(state) {

      return pluckField(state, 'displayname');
    },
    getTown(state) {

      return pluckField(state, 'town');
    },

    getState(state) {

      return pluckField(state, 'state');
    },

    getCountry(state) {

      return pluckField(state, 'country');
    },

    getPhone(state) {

      return pluckField(state, 'phone');
    },

    getLinks(state) {

      return state.form.filter((field) => {

        return field.field.includes('url-');
      });
    },
  },



  mutations: {

    UPDATE_FIELD: (state, payload) => {

      inputChange(state, payload);

    },

    ADD_FIELD: (state, payload) => {

      state.generatedFieldsCounter++;

      state.form.push({
        field: `url-${state.generatedFieldsCounter}`,
        errors: [],
        label: 'Link',
        value: '',
        size: 'lg',
        type: 'text',
        id: state.generatedFieldsCounter,
        nameAttr: `url-${state.generatedFieldsCounter}`
      });
    },

    REMOVE_LINK: (state, payload) => {

      state.form = state.form.filter((field) => field.id !== payload);
    },

    RESET_MODULE: (state) => {

      Object.assign(state, initialState());
    },

  CLEAR_ERROR_MSGS: (state) => {

      state.errorsPresent = false;

      state.form.forEach((field) => {

        field.errors = [];
       }
      );
  },
    SET_ERRORS: (state, payload) => {

      state.form.forEach((input, fIdx) => {

        payload.forEach((error, pIdx) => {

                const key = Object.keys(error);

                if (input.field === key.toString()) {

                    state.form[fIdx].errors.push(...payload[pIdx][input.field]);
                }
            });
        });

      if(errorsPresent(state.form, state.formName)) {

        state.errorsPresent = true;
      }
    },
  },

  actions: {


  }
};

export default generalDetails;
