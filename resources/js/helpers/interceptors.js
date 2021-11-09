import axios from 'axios';
import router from '../router/index'


let isRefreshing = false;
let failedQueue = [];
let userLoggedOut = false;

const processQueue = (error, token = null) => {
  failedQueue.forEach(prom => {
    if (error) {

      prom.reject(error);
    } else {
      prom.resolve(token);
    }
  })

  failedQueue = [];
}

const cleanUpAndLogout = (store) => {
       failedQueue = [];
      store.commit('notifications/RESET_NOTIFICATIONS_MODULE');
      store.commit('user/RESET_USER_MODULE');
      store.commit('user/REMOVE_TOKEN');
      if (router.history.current.name !== 'Login') {
         router.push('/login');
      }
}




const UNAUTHORIZED_URLS = [
  '/api/auth/login',
  '/api/auth/token/refresh',
  '/api/auth/recovery',
  '/api/auth/reset-password/',
  '/api/auth/reset-password/create',
  '/api/auth/users/count',
  '/api/auth/reviews'
];

export default function setup(store) {
 axios.interceptors.request.use(
    (config) => {

      if (!UNAUTHORIZED_URLS.includes(config.url)) {
          const token = store.getters['user/getToken'];
          if(token) {

            config.headers.common['Authorization'] = `Bearer ${token}`;

          }
      }
      return config;
    },
    (error) => Promise.reject(error)
  );


  axios.interceptors.response.use(function (response) {
  return response;
}, async function (error) {


  const originalRequest = error.config;

  if (error.response.status === 403) {
    cleanUpAndLogout(store);
  }

  if (error.response.status === 401 || error.response.status === 403) {
      if (!originalRequest.url.includes('/api/auth/reset-password')) {

        if (isRefreshing) {
          return new Promise(function(resolve, reject) {
            failedQueue.push({resolve, reject})
          }).then(token => {

            originalRequest.headers['Authorization'] = 'Bearer ' + token;

            return axios(originalRequest);
          }).catch(err => {
            return Promise.reject(err);
          })
        }
      }

      if(originalRequest.headers.Authorization !== 'Bearer ' + store.getters['user/getToken']) {
        originalRequest.headers['Authorization'] ='Bearer ' + store.getters['user/getToken'];
        return Promise.resolve(axios(originalRequest));
      }

    originalRequest._retry = true;
    isRefreshing = true;


    return new Promise(function (resolve, reject) {

      store.dispatch('user/REFRESH_TOKEN').then((response) => {


          store.commit('user/SET_TOKEN', response.data.access_token);
          const newToken = store.getters['user/getToken'];

          axios.defaults.headers.common['Authorization'] = 'Bearer ' + newToken
          originalRequest.headers['Authorization'] = 'Bearer ' + newToken;

          processQueue(null, newToken);
          resolve(axios(originalRequest));

      })
        .catch((err) => {

          if (err.response.status === 403) {
            cleanUpAndLogout(store);
            reject(err);
          } else {
            processQueue(err, null);
            reject(err);
          }
        })
        .then(() => {
           isRefreshing = false


          })
    })
  }

  return Promise.reject(error);
});

}




















