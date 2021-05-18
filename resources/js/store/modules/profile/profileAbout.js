import axios from 'axios';


const initialState = () => {

  return {

    aboutData: [],
    fetchError: '',
    dataLoaded: false,
    numOfCompletedFields: 0,
    totalFields: '',
    completionBar: '',
  }
};

const profileAbout = {

  namespaced: true,

  state: initialState(),

  getters: {

    getAboutData: (state) => {

      return state.aboutData;
    },

    getUserFirstName (state) {

      return state.aboutData.full_name ? state.aboutData.full_name.split(' ')[0] : '';
    },
  },

  mutations: {


    RESET_MODULE: (state) => {

     Object.assign(state, initialState());
    },

    SET_ABOUT_DATA: (state, payload) => {

      state.aboutData = payload;

      state.dataLoaded = true;
    },

    SET_NUM_OF_COMPLETED_FIELDS: (state) => {

      const excluded = [
        'created_at',
        'id',
        'updated_at',
        'user_id',
        'full_name',
        'work_currently'
      ];

      state.totalFields = Object.keys(state.aboutData).length - excluded.length;

      const fields = []
      fields.push(...Object.entries(state.aboutData));

      state.numOfCompletedFields = fields.filter((set) => {

        if (!excluded.includes(set[0]) && set[1] !== ''){

          return set[0];
        }
      }).length;



      const exactPercent =  state.numOfCompletedFields / state.totalFields  * 100;
      const evenPercent = 2*Math.round(exactPercent/ 2);

      state.completionBar = evenPercent;

    },

    SET_FETCH_ERROR: (state, payload) => {

      state.fetchError = payload;

      state.dataLoaded = true;
    }
  },

  actions: {

    async FETCH_ABOUT_DATA ({commit }, payload) {

      try {

        const response = await axios(
            {
              method:'GET',
              url: `/api/auth/profile/${payload}/about`,
              headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json',
              },
            }
          );

         commit('SET_ABOUT_DATA', response.data.profile);

      } catch (e) {

        commit('SET_FETCH_ERROR',e.response.data.msg);
      }
    }

  }
};

export default profileAbout;
