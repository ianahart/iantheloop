import axios from 'axios';

import { getObjPos } from '../../helpers/moduleHelpers';

const initialState = () => {

  return {
    form: [
        {field: 'posted_by', errors: [], label: '', value: 'Who', nameAttr: 'posted_by', defaultValue: 'Who'},
        {field: 'go_to_year', errors: [], label: '', value: 'Year', nameAttr: 'go_to_year', defaultValue: 'Year'},
        {field: 'go_to_month', errors: [], label: '', value: 'Month', nameAttr: 'go_to_month', defaultValue: 'Month'},
    ],
    filtersShowing: false,
    feedback: '',
  }
};

const profileWallSettings = {

  namespaced: true,

  state: initialState(),

  getters: {

    checkFiltersOn(state) {
      return state.form.every((selectEl) => selectEl.value.toLowerCase() !== selectEl.defaultValue.toLowerCase());
    },

    getPostedBy(state) {
      const index = getObjPos(state.form, 'posted_by');
      return state.form[index];
    },

     getGoToYear(state) {
       const index = getObjPos(state.form, 'go_to_year');
      return state.form[index];
    },

     getGoToMonth(state) {
       const index = getObjPos(state.form, 'go_to_month');
       return state.form[index];
    },

    getFilters(state) {
      return state.form
      .filter((selectEl) => selectEl.value.toLowerCase() !== selectEl.defaultValue.toLowerCase())
      .map(({field, value}) => ({ [field]: value } ));
    },
  },

  mutations: {

    SET_FEEDBACK: (state, payload) => {
      state.feedback = payload;
    },

    RESET_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    OPEN_FILTERS: (state) => {
      state.filtersShowing = true;
    },

    CLOSE_FILTERS: (state) => {
      state.filtersShowing = false;
    },

    UPDATE_FILTER: (state, selection) => {
      state.form.forEach((selectEl, index) => {
        if (selectEl.field === selection.field) {
          state.form[index].value = selection.value;
        }
      });
    },

    CLEAR_FILTERS: (state, payload) => {
        state.form.forEach((selectEl) => {
          payload.forEach((filterValue) => {
            if (selectEl.defaultValue === filterValue) {
              selectEl.value = filterValue;
            }
          });
        });
    },
  },

  actions: {

  }
};

export default profileWallSettings;
