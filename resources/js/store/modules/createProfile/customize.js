
import axios from 'axios';

import {
  getFormData,
  pluckField,
  clearFields,
  retrieveFile,
  setCustomizeSrc,
} from '../../../helpers/moduleHelpers.js';

const initialState = () => {

  return {
    form: [
          {file: 'backgroundfile', field: 'background_image', errors: [], label: 'Background Image', value: '',size: '', type: 'file', nameAttr: 'background_image'},
          {file: 'profilefile', field: 'profile_image' ,errors: [], label: 'Profile Picture', value: '',size: '', type: 'file', nameAttr: 'profile_image'},
    ],
    errorsPresent: false,
    formName: 'customize',
    files: [
      {file: null, input: 'background_image', src: ''},
      {file: null, input: 'profile_image', src: ''},
    ],
  };
};

const customize = {

  namespaced: true,

  state: initialState(),

  getters: {

    getBackgroundImageFile(state) {

      return retrieveFile(state.files, 'background_image');
    },

    getProfileImageFile(state) {

      return retrieveFile(state.files, 'profile_image');
    },

    getBackgroundImage (state) {

      return pluckField(state, 'background_image');
    },

    getProfileImage (state) {

      return pluckField(state, 'profile_image');
    }
  },

  mutations: {

    REMOVE_IMAGE: (state, payload) => {

      state.form.forEach((field) => {

        if (field.field === payload) {
          field.errors = [];
        }
      });

      const file = state.files.find((file) => file.input === payload);

      file.src = '';
      file.file = null;

      return state.form.map((input) => {

        if (input.field === payload) {

          input.value = '';

          return input;
        }
        return input;
      });
    },

    SET_IMAGE_FIELD:(state, { file, input, src }) => {

      state.files.forEach((fileObj) => {

          if (fileObj.input === input) {

            fileObj.file = file;
            fileObj.input = input;
            fileObj.src = src;

            setCustomizeSrc(state.form, input, src);
          }
      });
    },

      CLEAR_VALUES: (state) => {

        clearFields(state.form);

        state.files.forEach((file) => {

          file.src = '';
          file.file = null;
        });
      },

      CLEAR_ERROR_MSGS: (state) => {

        state.errorsPresent = false;

        state.form.forEach((field) => {

          field.errors = [];
        }
        );
    },

     SET_ERRORS: (state, payload) => {

      if (payload.length === 0) {

        return;
      }

      state.form.forEach((field, formIndex) => {

        payload.forEach((error, errorIndex) => {
          const key = Object.keys(error);

          if (key.toString() === field.file) {

            state.form[formIndex].value = '';
            state.errorsPresent = true;
            state.form[formIndex].errors.push(...payload[errorIndex][key]);
          }
        });
      });
    },
  },

  actions: {


  }
}

export default customize;