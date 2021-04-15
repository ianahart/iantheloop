import VueRouter from 'vue-router';
import Vue from 'vue';
import routes from './routes';

const router = new VueRouter(
  {
  mode: 'history',
  routes,
  }
);

export default router;