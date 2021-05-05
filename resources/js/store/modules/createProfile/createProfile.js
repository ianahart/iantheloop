import axios from "axios";

const initialState = () => {

  return {

    currentForm: 'identity',
  }
};

const createProfile = {

  namespaced: true,

  state: initialState(),

  getters: {

    getCurrentForm (state) {

     const currentForm = state.currentForm
     .split(' ').map((word) => {

      return word.slice(0, 1).toUpperCase() + word.slice(1);
     });

      return currentForm.join(' ');
    },
  },

  mutations: {

    SET_CURRENT_FORM: (state, payload) => {

      state.currentForm = payload;
    },

    RESET_MODULE: (state) => {

      Object.assign(state, initialState());
    }
  },

  actions: {

    async CREATE_PROFILE ({ commit, getters, rootGetters }) {

      try {

        let response;

        response = await axios(
          {
            url: '/api/auth/profile',
            method: 'POST',
            headers: {

              'Content-Type': 'application/json',
              'Accept' : 'application/json',
            },

            data: {
              identity: rootGetters['identity/getIdentity'],
              generalDetails: rootGetters['generalDetails/getGeneralDetails'],
              aboutDetails:   rootGetters['aboutDetails/getAboutDetails'],
              // customize: rootGetters['customize/getCustomize'],
              workDetails: rootGetters['workDetails/getWorkDetails'],
              },
            }
        );

        console.log(response);

          // let stringifiedUser = localStorage.getItem('user');

          // const parsedUser = JSON.parse(stringifiedUser);

          // parsedUser.profile_created = response.data.profileCreated;

          // commit('user/SET_TOKEN', JSON.stringify(parsedUser), { root: true });

      } catch (e) {

        const forms = {
          identity: [],        //
          aboutDetails:  [],
          // customize: [],
          generalDetails: [],
          workDetails: [],
        }

        const fields = e.response.data.errors;

        for (let field in fields) {

          const form = field.split('.')[0];

          if (Object.keys(forms).includes(form)) {

            forms[form].push(
                {
                  [field.split('.')[1]]:fields[field]
                });
          }
        }
        console.log('Create Profile Errors: ', e.response);

        /**Test a single form**/
        // commit('generalDetails/SET_ERRORS', forms.generalDetails, {root: true});

          for (let form in forms) {

            commit(`${form}/SET_ERRORS`, forms[form], {root: true});
          }

      }
    }
  }
};

export default createProfile;
