require('./bootstrap');


import Vue from 'vue';
import VueRouter from 'vue-router';

import router from './router/index';
import store from './store/index';
import App from './App.vue';

import interceptorSetup from './helpers/interceptors.js';

interceptorSetup(store);

Vue.use(VueRouter);

const app = new Vue(
  {
    el: '#app',
    router,
    store,
    components: { App },
  }
);