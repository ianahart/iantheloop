import axios from 'axios';

const initialState = () => {

  return {

    form: [
      {field: 'firstName', error: '', label: 'First Name', value: '',size: 'md', type: 'text'},
      {field: 'lastName', error: '', label: 'Last Name', value: '', size: 'md', type: 'text'},
      {field: 'email', error: '', label: 'Email', value: '', size: 'lg', type: 'text'},
      {field: 'createPassword', error: '', label: 'Create Password', value: '', size: 'lg', type: 'password'},
      {field: 'confirmPassword', error: '', label: 'Confirm Password', value: '', size: 'lg', type: 'password'},
    ],
    hasErrors: false,
    isSubmitted: false,
    isChecked: false,
    checkboxError: '',
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
    }

  },

  mutations: {

    UPDATE_FIELD: (state, payload) => {

      state
      .form
      .find(
        (oldField) => {

          if (oldField.field === payload.field) {

              oldField.value = payload.newValue;

              oldField.error = payload.error;

              state.hasErrors = oldField.error.length ? true : false;
          }
        }
      );
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
    },

    RESET_MODULE: (state) => {

      Object.assign(state, initialState());
    },

    SET_ERRORS: (state) => {

    },

    SUBMIT_FORM: (state) => {

    },
  },

  actions: {

    SUBMIT_FORM: (context) => {

      console.log(context);
    }

  }

};


export default createAccount;


// validate form on submit

  // if errors map them to inputs

  // set global error: true

// if no errors

// reset global error : false

  // if global error: false

   //dispatch axios request to server
//-----------------------------
    // get response back if 422

    // if errors map errors

    //reset global error: true,
