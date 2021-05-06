
import axios from 'axios';

import {
  inputChange,
  getFormData,
  pluckField,
  errorsPresent
} from '../../../helpers/moduleHelpers.js';

const initialState = () => {

  return {
    form: [
          {field: 'background_image', errors: [], label: 'Background Image', value: '',size: '', type: 'file', nameAttr: 'background_image'},
          {field: 'profile_image', errors: [], label: 'Profile Picture', value: '',size: '', type: 'file', nameAttr: 'profile_image'},
    ],
    errorsPresent: false,
    formName: 'customize',
  };
};

const customize = {

  namespaced: true,

  state: initialState(),

  getters: {

    getCustomize: (state) => {

      return getFormData(state);
    },

    getBackgroundImage (state) {

      return pluckField(state, 'background_image');
    },

    getProfileImage (state) {

      return pluckField(state, 'profile_image');
    }
  },

  mutations: {

    UPDATE_FIELD: (state, payload) => {

      inputChange(state, payload);

    },

     SET_ERRORS: (state, payload) => {

      if (payload.length === 0) {

        return;
      }

      state.form.forEach((input, fIdx) => {

        payload.forEach((error, pIdx) => {

          const key = Object.keys(error);

          if (input.field === key.toString()) {

               state.errorsPresent = true;

               state.form[fIdx].errors.push(...payload[pIdx][input.field]);
          }
        });
      });
    },
  },

  actions: {


  }
}

export default customize;