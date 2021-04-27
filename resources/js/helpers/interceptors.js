import axios from 'axios';
import router from '../router/index'


const UNAUTHORIZED_URLS = [

  '/api/auth/login',
  '/api/auth/token/refresh',
  '/api/auth/recovery',
  '/api/auth/reset-password/',
  '/api/auth/reset-password/create',
];

export default function setup(store) {

/**
 *
 * Add token to every request that needs one
*/

  axios.interceptors.request.use((config) => {

      if (!UNAUTHORIZED_URLS.includes(config.url)) {

          const token = store.getters['user/getToken'];

          if(token) {

             config.headers.common['Authorization'] = `Bearer ${token}`;
          } else {

            config.headers.common['Authorization'] = null;
          }
      }

        return config;

    }, (err) => {

        return Promise.reject(err);
    });

/**
 *
 * If a status of 401 comes back and the token is expired refresh token
 * and retry the previous request
 * if token is refresh token is expired then log the user out (403)
*/



  axios.interceptors.response.use(
    function(response) {

      return response;
    },
    async function(error) {

      const originalRequest = error.config;

      if (
        error.response.status === 403 &&
        originalRequest.url.includes("/api/auth/token/refresh")
      ) {

          store.commit('user/REMOVE_TOKEN');

          router.push('/login');

        return Promise.reject(error);

      } else if (error.response.status === 401 && !originalRequest._retry) {

        originalRequest._retry = true;

        if (!originalRequest.url.includes('/api/auth/reset-password')) {

          await store.dispatch("user/REFRESH_TOKEN");

          const token = store.getters['user/getToken'];

          originalRequest.headers.Authorization = `Bearer ${token}`;

            return axios(originalRequest);
        }

      }
      return Promise.reject(error);
    }
  );
}
