
import axios from 'axios';

import {
  inputChange,
  getFormData,
  pluckField,
  clearFields,
  errorsPresent
} from '../../../helpers/moduleHelpers.js';

const initialState = () => {

  return {
    form: [
          {field: 'gender', errors: [], label: 'Gender', value: '',size: '', type: 'radio', nameAttr: 'gender', options: ['Male', 'Female', 'Trans', 'N/A']},
          {field: 'birth_day', errors: [], label: 'Birth Day', value: 'Day',size: 'md', type: 'text', nameAttr: 'birth_day', defaultValue: 'Day'},
          {field: 'birth_month', errors: [], label: 'Birth Month', value: 'Month',size: 'md', type: 'text', nameAttr: 'birth_month', defaultValue: 'Month'},
          {field: 'birth_year', errors: [], label: 'Birth Year', value: 'Year',size: 'md', type: 'text', nameAttr: 'birth_year', defaultValue: 'Year'},
    ],
    errorsPresent: false,
    days: [{name: '1', abbrv: '1', id: 1}, {name: '2', abbrv: '2', id: 2}, {name: '3', abbrv: '3', id: 3},{name: '4', abbrv: '4', id: 4},{name: '5', abbrv: '5', id: 5},{name: '6', abbrv: '6', id: 6},{name: '7', abbrv: '7', id: 7},{name: '8', abbrv: '8', id: 8},{name: '9', abbrv: '9', id: 9},{name: '10', abbrv: '10', id: 10},{name: '11', abbrv: '11', id: 11},{name: '12', abbrv: '12', id: 12},{name: '13', abbrv: '13', id: 13},{name: '14', abbrv: '14', id: 14}, {name: '15', abbrv: '15', id: 15},{name: '16', abbrv: '16', id: 16},{name: '17', abbrv: '17', id: 17},{name: '18', abbrv: '18', id: 18},{name: '19', abbrv: '19', id: 19},{name: '20', abbrv: '20', id: 20},{name: '21', abbrv: '21', id: 21},{name: '22', abbrv: '22', id: 22},{name: '23', abbrv: '23', id: 23},{name: '24', abbrv: '24', id: 24},{name: '25', abbrv: '25', id: 25},{name: '26', abbrv: '26', id: 26},{name: '27', abbrv: '27', id: 27},{name: '28', abbrv: '28', id: 28}, {name: '29', abbrv: '29', id: 29},{name: '30', abbrv: '30', id: 30}, {name: '31', abbrv: '31', id: 31}],
    months: [{name: 'Jan', abbrv: 'Jan', id: 1},{name: 'Feb', abbrv: 'Feb', id: 2}, {name: 'Mar', abbrv: 'Mar', id: 3},{name: 'Apr', abbrv: 'Apr', id: 4}, {name: 'May', abbrv: 'May', id: 5}, {name: 'Jun', abbrv: 'Jun', id: 6}, {name: 'Jul', abbrv: 'Jul', id: 7}, {name: 'Aug', abbrv: 'Aug', id: 8},{name: 'Sep', abbrv: 'Sep', id: 9}, {name: 'Oct', abbrv: 'Oct', id: 10}, {name: 'Nov', abbrv: 'Nov', id: 11}, {name: 'Dec', abbrv: 'Dec', id: 12}],
    formName: 'identity',
  };
};

const identity = {

  namespaced: true,

  state: initialState(),

  getters: {

    selectedRadio: (state) => {

      const radioField = state.form.find((field) => field.field === 'gender');

      return radioField.value;
    },

    getIdentity: (state) => {

      return getFormData(state);
    },

    getDaysInMonth (state) {

      let amount;

      const { value } = state.form.find(({ field }) => field === 'birth_month');

      const daysInMonth = {
        '31': ['Jan', 'Mar', 'May', 'Jul', 'Aug', 'Oct', 'Dec'],
        '30': ['Nov', 'Sep', 'Jun', 'Apr'],
        '28': ['Feb'],
      }

      for (let prop in daysInMonth) {

        if (daysInMonth[prop].includes(value)) {

           amount = parseInt(prop);
        }
      }

      return state.days.slice(0, amount);
    },

    getGender(state) {

      return pluckField(state, 'gender');
    },
    getBirthYear(state) {

      return pluckField(state, 'birth_year');
    },
    getBirthDay(state) {

      return pluckField(state, 'birth_day');
    },
    getBirthMonth(state) {

      return pluckField(state, 'birth_month');
    }
  },

  mutations: {

      CLEAR_VALUES: (state) => {

        clearFields(state.form);

      },

      CLEAR_ERROR_MSGS: (state) => {

        state.errorsPresent = false;

        state.form.forEach((field) => {

          field.errors = [];
        }
        );
    },


    RESET_MODULE: (state) => {

      Object.assign(state, initialState());
    },

    UPDATE_FIELD: (state, payload) => {

      if (payload.field === 'birth_month') {

        const BirthDayField = state.form.find(({ field }) => field === 'birth_day' );

        BirthDayField.value = 'Day';
      }

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
}

export default identity;