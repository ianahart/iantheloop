import axios from "axios";
import customize from "./customize";

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

          let rawData = {
              identity: rootGetters['identity/getIdentity'],
              workDetails: rootGetters['workDetails/getWorkDetails'],
              aboutDetails:   rootGetters['aboutDetails/getAboutDetails'],
              generalDetails: rootGetters['generalDetails/getGeneralDetails'],
          };

          let backgroundImageFile = rootGetters['customize/getBackgroundImageFile'];
          let profileImageFile = rootGetters['customize/getProfileImageFile'];


          rawData = JSON.stringify(rawData);

          const formData = new FormData();

          formData.append('data', rawData);
          formData.append('backgroundfile', backgroundImageFile.file ?? '');
          formData.append('profilefile', profileImageFile.file ?? '');


        let response;

        response = await axios(
          {
            url: '/api/auth/profile',
            method: 'POST',
            'contentType': false,
            'processData': false,
            headers: {

              'Accept' : 'application/json',
            },

            data: formData,
          }
        );

        console.log(response);

          let stringifiedUser = localStorage.getItem('user');

          const parsedUser = JSON.parse(stringifiedUser);

          parsedUser.profile_created = response.data.profileCreated;
          parsedUser.profile_pic = response.data.profile_pic;


          commit('user/SET_TOKEN', JSON.stringify(parsedUser), { root: true });

      } catch (e) {

        const forms = {
          identity: [],
          aboutDetails:  [],
          customize: [],
          generalDetails: [],
          workDetails: [],
        }

        const fields = e.response.data.errors;

        for (let field in fields) {

          if (!field.includes('.')) {

              forms.customize.push({[field]: fields[field]});
          }
          const form = field.split('.')[0];

          if (Object.keys(forms).includes(form)) {

            forms[form].push(
                {
                  [field.split('.')[1]]:fields[field]
                });
          }
        }
        // console.log('---------Create Profile Errors: ------', e.response);

          for (let form in forms) {

            commit(`${form}/SET_ERRORS`, forms[form], {root: true});
          }

      }
    }
  }
};

export default createProfile;
