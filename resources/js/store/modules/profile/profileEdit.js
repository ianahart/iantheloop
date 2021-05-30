import axios from 'axios';
import { flatten } from 'lodash';

import {
    inputChange,
    clearFields,
    filterEditGroup,
    getCurrentRadioValue,
    prepareEditFile,
    getObjPos,
  } from '../../../helpers/moduleHelpers.js';

const initialState = () => {

  return {

    editData: [],
    fetchError: '',
    userId: null,
    formErrors: false,
    dataLoaded: false,
    profileId: null,
    isUpdated: false,
    isCurrentlyChecked: false,
    initialBackgroundPic: null,
    initialProfilePic: null,
    generatedFieldsCounter: 0,
    interestsIndexer: 0,
    currentWindow: 'General',
    windows: ['General', 'Identity', 'Work', 'About', 'Pictures'],
    files: [
      {file: null, input: 'background_picture', src: ''},
      {file: null, input: 'profile_picture', src: ''},
    ],
    days: [{name: '1', abbrv: '1', id: 1}, {name: '2', abbrv: '2', id: 2}, {name: '3', abbrv: '3', id: 3},{name: '4', abbrv: '4', id: 4},{name: '5', abbrv: '5', id: 5},{name: '6', abbrv: '6', id: 6},{name: '7', abbrv: '7', id: 7},{name: '8', abbrv: '8', id: 8},{name: '9', abbrv: '9', id: 9},{name: '10', abbrv: '10', id: 10},{name: '11', abbrv: '11', id: 11},{name: '12', abbrv: '12', id: 12},{name: '13', abbrv: '13', id: 13},{name: '14', abbrv: '14', id: 14}, {name: '15', abbrv: '15', id: 15},{name: '16', abbrv: '16', id: 16},{name: '17', abbrv: '17', id: 17},{name: '18', abbrv: '18', id: 18},{name: '19', abbrv: '19', id: 19},{name: '20', abbrv: '20', id: 20},{name: '21', abbrv: '21', id: 21},{name: '22', abbrv: '22', id: 22},{name: '23', abbrv: '23', id: 23},{name: '24', abbrv: '24', id: 24},{name: '25', abbrv: '25', id: 25},{name: '26', abbrv: '26', id: 26},{name: '27', abbrv: '27', id: 27},{name: '28', abbrv: '28', id: 28}, {name: '29', abbrv: '29', id: 29},{name: '30', abbrv: '30', id: 30}, {name: '31', abbrv: '31', id: 31}],
    months: [{name: 'Jan', abbrv: 'Jan', id: 1},{name: 'Feb', abbrv: 'Feb', id: 2}, {name: 'Mar', abbrv: 'Mar', id: 3},{name: 'Apr', abbrv: 'Apr', id: 4}, {name: 'May', abbrv: 'May', id: 5}, {name: 'Jun', abbrv: 'Jun', id: 6}, {name: 'Jul', abbrv: 'Jul', id: 7}, {name: 'Aug', abbrv: 'Aug', id: 8},{name: 'Sep', abbrv: 'Sep', id: 9}, {name: 'Oct', abbrv: 'Oct', id: 10}, {name: 'Nov', abbrv: 'Nov', id: 11}, {name: 'Dec', abbrv: 'Dec', id: 12}],
    form: [
          {field: 'display_name', errors: [], label: 'Display Name', value: '',size: 'md', type: 'text', nameAttr: 'display_name', group: 'General'},
          {field: 'town', errors: [], label: 'Town', value: '',size: 'md', type: 'text', nameAttr: 'town',group: 'General'},
          {field: 'state', errors: [], label: 'State', value: 'State',size: 'sm', type: 'text', nameAttr: 'state' ,defaultValue: 'State',  group: 'General'},
          {field: 'country', errors: [], label: 'Country', value: 'Country',size: 'md', type: 'text', nameAttr: 'country', defaultValue: 'Country',  group: 'General'},
          {field: 'phone', errors: [], label: 'Phone', value: '',size: 'md', type: 'text', nameAttr: 'phone',  group: 'General'},

          {field: 'gender', errors: [], label: 'Gender', value: '',size: '', type: 'radio', nameAttr: 'gender', options: ['Male', 'Female', 'Trans', 'N/A'], group: 'Identity'},
          {field: 'birth_day', errors: [], label: 'Birth Day', value: 'Day',size: 'md', type: 'text', nameAttr: 'birth_day', defaultValue: 'Day', group: 'Identity'},
          {field: 'birth_month', errors: [], label: 'Birth Month', value: 'Month',size: 'md', type: 'text', nameAttr: 'birth_month', defaultValue: 'Month', group: 'Identity'},
          {field: 'birth_year', errors: [], label: 'Birth Year', value: 'Year',size: 'md', type: 'text', nameAttr: 'birth_year', defaultValue: 'Year', group: 'Identity'},

          {field: 'company', errors: [], label: 'Company', value: '',size: 'lg', type: 'text', nameAttr: 'company', group: 'Work'},
          {field: 'position', errors: [], label: 'Position', value: '',size: 'lg', type: 'text', nameAttr: 'position' ,group: 'Work'},
          {field: 'work_city', errors: [], label: 'City/Town', value: '',size: 'lg', type: 'text', nameAttr: 'work_city'  ,group: 'Work'},
          {field: 'description', errors: [], label: 'Description', value: '',size: 'md', type: 'text', nameAttr: 'birth_year'  ,group: 'Work'},
          {field: 'month_from', errors: [], label: 'Month', value: 'Month',size: '', type: 'text', nameAttr: 'month_from', defaultValue: 'Month'  ,group: 'Work'},
          {field: 'year_from', errors: [], label: 'Year', value: 'Year',size: '', type: 'text', nameAttr: 'year_from', defaultValue: 'Year'  ,group: 'Work'},
          {field: 'month_to', errors: [], label: 'Month', value: 'Month',size: '', type: 'text', nameAttr: 'month_to', defaultValue: 'Month'  ,group: 'Work'},
          {field: 'year_to', errors: [], label: 'Year', value: 'Year',size: '', type: 'text', nameAttr: 'year_to', defaultValue: 'Year'  ,group: 'Work'},

          {field: "bio",errors: [],label: "Bio",value: "",size: "md",type: "text",nameAttr: "bio"  ,group: 'About'},
          {field: "relationship",errors: [],label: "Relationship Status",value: "",size: "md",type: "radio",nameAttr: "relationship",statuses: ["Single","In a relationship","Married","Divorced","N/A"] ,group: 'About'},
          {field: "interests",errors: [],label: "Interests",value: "",size: "sm",type: "text",nameAttr: "interests",interests: [] ,group: 'About'},

          {file: 'background_picture', field: 'background_picture', errors: [], label: 'Background Image', value: '',size: '', type: 'file', nameAttr: 'background_picture' ,group: 'Pictures'},
          {file: 'profile_picture', field: 'profile_picture' ,errors: [], label: 'Profile Picture', value: '',size: '', type: 'file', nameAttr: 'profile_picture' ,group: 'Pictures'},
    ],
  }
};

const profileEdit = {

  namespaced: true,

  state: initialState(),

  getters: {

    getFormData (state) {

      let data = state.form.map((field) => {

        const val = field.field === 'interests' ? field.interests : field.value;

        return {[field.field]:val};
      });

      // remove these fields because they are packaged separately and not part of form
      // for validation
      const exclude = ['background_picture', 'profile_picture'];
      data = data.filter((item) => {

        if(!exclude.includes(...Object.keys(item))) {
          return item;
        }
      });

      return Object.assign({}, ...data);
    },

    getBackgroundPicture(state) {

      return prepareEditFile(state.form, state.files, 'background_picture');
    },

    getProfilePicture(state) {

      return prepareEditFile(state.form, state.files, 'profile_picture');
    },

    getLinks(state) {

      return state.form.filter((field) => {

        return field.field.includes('url-');
      });
    },

    getEditGroup (state) {

      return filterEditGroup(state.form, state.currentWindow);
    },

    selectedGenderRadio: (state) => {

      return getCurrentRadioValue(state.form, 'gender');
    },

    selectedRelationshipRadio: (state) => {

      return getCurrentRadioValue(state.form, 'relationship');

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

  },

  mutations: {

        RESET_MODULE: (state) => {

        Object.assign(state, initialState());
        },

        CLEAR_FORM: (state) => {

          clearFields(state.form);
        },


        CLEAR_ERRORS: (state) => {

          state.formErrors = false;

          state.form.forEach((field) => {

            field.errors = [];
          });

        },

        SET_PICTURE: (state, { input, file, src }) => {

          if(input === 'profile_picture') {

            state.initialProfilePic = false;
          }

          if (input === 'background_picture') {

            state.initialBackgroundPic = false;
          }

          const fieldIndex = getObjPos(state.form, input);
          const fileIndex = state.files.findIndex((file) => file.input === input);

          const obj = {file, input, src};

          state.form[fieldIndex].value = src;
          state.files[fileIndex] = obj;
        },

        REMOVE_PICTURE: (state, payload) => {

          const fieldIndex = getObjPos(state.form, payload);

          state.form[fieldIndex].value = '';

          state.files.forEach((fileObj) => {

            if (fileObj.input === payload) {

              fileObj.file = null;
              fileObj.src = '';
            }
          });
        },

        TOGGLE_CURRENTLY: (state, payload) => {

        state.isCurrentlyChecked = !state.isCurrentlyChecked;

        const monthToIndex = getObjPos(state.form, 'month_to');
        const yearToIndex = getObjPos(state.form, 'year_to');


      if (state.isCurrentlyChecked) {

        const d = new Date();
        const { abbrv } = state.months.find((month) => month.id === d.getMonth() + 1);

        state.form[monthToIndex].value = abbrv;
        state.form[yearToIndex].value =  d.getFullYear().toString();
      }

      if (!state.isCurrentlyChecked) {

        state.form[monthToIndex].value = 'Month';
        state.form[yearToIndex].value = 'Year';
      }
    },

    DELETE_INTEREST: (state, payload) => {

      const pos = getObjPos(state.form, 'interests');

      state.form[pos].interests = state.form[pos].interests.filter((interest) => interest.id !== payload);


    },

    INDEX_INTEREST: (state, payload) => {

      state.interestsIndexer++;

      const interestsPos = getObjPos(state.form, 'interests');

      state.form[interestsPos].interests.push({id: state.interestsIndexer, name: payload});
      state.form[interestsPos].value = '';
    },

    SET_INTEREST_VALUE: (state, payload) => {

      const formIndex = state.form.findIndex((field) => field.field === 'interests');

      state.form[formIndex].value = payload;
    },

    REMOVE_LINK: (state, payload) => {

      state.form = state.form.filter((field) => field.id !== payload);
    },


     UPDATE_FIELD: (state, payload) => {

      const stateFieldIndex = state.form.findIndex((field) => field.field === 'state');

      if (payload.field === 'country' && payload.value.toLowerCase() !== 'united states') {

        state.form[stateFieldIndex].value = 'State';
      }

       if (payload.field === 'birth_month') {

        const BirthDayField = state.form.find(({ field }) => field === 'birth_day' );

        BirthDayField.value = 'Day';
      }

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

    CHANGE_WINDOW: (state, payload) => {


      state.currentWindow = payload;
    },

    SET_FETCH_ERROR: (state, payload) => {

      state.fetchError = payload;

      state.dataLoaded = true;
    },
    SET_EDIT_DATA: (state, { data }) => {

      state.form.forEach((field, index) => {



          if (field.field === 'interests') {

            field.interests = data[field.field];
            field.value = '';
          } else {

              if (field.field === 'state') {


              if (!data[field.field]) {

                state.form[index].value = 'State';
              } else {

                state.form[index].value = data[field.field];
              }
            } else {

              field.value = data[field.field];
            }
          }
      });

      state.isCurrentlyChecked = data.work_currently;
      state.initialProfilePic = true;
      state.initialBackgroundPic = true;
      state.profileId = data.id;

      data.links.forEach((link, index) => {

        state.form.push({
          field: `url-${index}`,
          errors: [],
          label: 'Link',
          value: link,
          size: 'lg',
          type: 'text',
          id: index,
          nameAttr: `url-${index}`
        });
      });

      state.interestsIndexer = Math.max(...data.interests.map((val) => val.id));
      state.generatedFieldsCounter = data.links.length;

      state.dataLoaded = true;
    },

    SET_VALIDATION_ERRORS:(state, payload) => {

      const interestsIndex = getObjPos(state.form, 'interests');

      state.formErrors = true;

      state.form.forEach((field, index) => {

        for( let prop in payload) {

          if(prop.includes('interests') && field.field === 'interests') {
            state.form[interestsIndex].errors.push(...payload[prop]);
          }

          if (field.field === prop) {
              state.form[index].errors.push(...payload[prop]);
          }
        }
      });
      state.form[interestsIndex].errors = [...new Set(state.form[interestsIndex].errors)];
    },

    SET_IS_UPDATED: (state, payload) => {

      state.userId = payload.user_id;
      state.isUpdated = payload.isUpdated;
    },

    REPLACE_DEFAULT_VALUES: (state) => {

      const stateFieldIndex = getObjPos(state.form, 'state');
      const countryFieldIndex = getObjPos(state.form, 'country');

      if (state.form[stateFieldIndex].value.toLowerCase() === 'state') {

        state.form[stateFieldIndex].value = '';
      }

      if (state.form[countryFieldIndex].value.toLowerCase() === 'country') {

        state.form[stateFieldIndex].value = '';
      }
    }
  },

  actions: {

    async FETCH_EDIT_DATA ({commit }, payload) {

      try {

        const response = await axios(
            {
              method:'GET',
              url: `/api/auth/profile/${payload}/edit`,
              headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json',
              },
            }
          );

         if (response.status === 401) {

          commit('SET_FETCH_ERROR', response.data.error);
         } else {

          commit('SET_EDIT_DATA', response.data);
         }
      } catch (e) {

        console.log(e.response);
      }
    },

    async UPDATE_PROFILE ({commit, state, getters }) {

      try {

        commit('REPLACE_DEFAULT_VALUES');
        const formData = new FormData();

        const fields = getters.getFormData;
        const backgroundPicture = getters.getBackgroundPicture;
        const profilePicture = getters.getProfilePicture;

        formData.append('_method', 'PATCH');

        if (backgroundPicture.file) {

          formData.append('background_picture', backgroundPicture.file ?? '');
        } else {

          fields.background_pic_prev = backgroundPicture.src;
        }

        if (profilePicture.file) {

          formData.append('profile_picture', profilePicture.file ?? '');
        } else {

          fields.profile_pic_prev = profilePicture.src;
        }

        fields.work_currently = state.isCurrentlyChecked;

        formData.append('data', JSON.stringify(fields));

        const response = await axios(
            {
              method:'POST',
              url: `/api/auth/profile/${state.profileId}/update`,
              'contentType': false,
              'processData': false,
              headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json',
              },
              data: formData,
            }
          );

        let stringifiedUser = localStorage.getItem('user');
        const parsedUser = JSON.parse(stringifiedUser);
        parsedUser.profile_pic = response.data.profile_pic;
        commit('user/SET_TOKEN', JSON.stringify(parsedUser), { root: true });

        commit('SET_IS_UPDATED', {isUpdated:response.data.isUpdated, user_id: parsedUser.user_id});
      } catch (e) {
        if (e.response.status === 422) {

          const { errors } = e.response.data;

          commit('SET_VALIDATION_ERRORS', errors);
        }
      }
    }

  }
};

export default profileEdit;
