
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
          {field: 'company', errors: [], label: 'Company', value: '',size: 'lg', type: 'text', nameAttr: 'company'},
          {field: 'position', errors: [], label: 'Position', value: '',size: 'lg', type: 'text', nameAttr: 'position'},
          {field: 'work_city', errors: [], label: 'City/Town', value: '',size: 'lg', type: 'text', nameAttr: 'work_city'},
          {field: 'description', errors: [], label: 'Description', value: '',size: 'md', type: 'text', nameAttr: 'birth_year'},
          {field: 'month_from', errors: [], label: 'Month', value: '',size: '', type: 'text', nameAttr: 'month_from'},
          {field: 'year_from', errors: [], label: 'Year', value: '',size: '', type: 'text', nameAttr: 'year_from'},
          {field: 'month_to', errors: [], label: 'Month', value: '',size: '', type: 'text', nameAttr: 'month_to'},
          {field: 'year_to', errors: [], label: 'Year', value: '',size: '', type: 'text', nameAttr: 'year_to'},
    ],
    months: [{name: 'Jan', abbrv: 'Jan', id: 1},{name: 'Feb', abbrv: 'Feb', id: 2}, {name: 'Mar', abbrv: 'Mar', id: 3},{name: 'Apr', abbrv: 'Apr', id: 4}, {name: 'May', abbrv: 'May', id: 5}, {name: 'Jun', abbrv: 'Jun', id: 6}, {name: 'Jul', abbrv: 'Jul', id: 7}, {name: 'Aug', abbrv: 'Aug', id: 8},{name: 'Sep', abbrv: 'Sep', id: 9}, {name: 'Oct', abbrv: 'Oct', id: 10}, {name: 'Nov', abbrv: 'Nov', id: 11}, {name: 'Dec', abbrv: 'Dec', id: 12}],
    errorsPresent: false,
    formName: 'workDetails',
    timePeriodChecked: false,
  };
};

const workDetails = {

  namespaced: true,

  state: initialState(),

  getters: {

    getWorkDetails: (state) => {

      return getFormData(state);
    },


    getCompany(state) {

      return pluckField(state, 'company');
    },

    getPosition(state) {

      return pluckField(state, 'position');
    },

     getCity(state) {

      return pluckField(state, 'work_city');
    },

    getDescription(state) {

      return pluckField(state, 'description');
    },

    getMonthFrom(state) {

      return pluckField(state, 'month_from');
    },

    getYearFrom(state) {

      return pluckField(state, 'year_from');
    },
    getMonthTo(state) {

      return pluckField(state, 'month_to');
    },

    getYearTo(state) {

      return pluckField(state, 'year_to');
    }
  },

  mutations: {

    CLEAR_VALUES: (state) => {

      clearFields(state.form);

    },

    TOGGLE_CHECKBOX: (state, payload) => {

      state.timePeriodChecked = !state.timePeriodChecked;

      if (state.timePeriodChecked) {

        const d = new Date();

        const { abbrv } = state.months.find((month) => month.id === d.getMonth() + 1);

        state.form[state.form.length - 2].value = abbrv;
        state.form[state.form.length - 1].value =  d.getFullYear().toString();
      }

      if (!state.timePeriodChecked) {

        state.form[state.form.length - 2].value = '';
        state.form[state.form.length - 1].value = '';
      }
    },

    UPDATE_FIELD: (state, payload) => {


      inputChange(state, payload);

    },

      CLEAR_ERROR_MSGS: (state) => {

      state.form.forEach((field) => {

        field.errors = [];
        }
      );

      state.errorsPresent = false;

  },

     SET_ERRORS: (state, payload) => {

      console.log(payload);

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

export default workDetails;