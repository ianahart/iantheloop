const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../pages/Home.vue'),
  },
  {
    path: '/create-account',
    name: 'CreateAccount',
    component: () => import('../pages/CreateAccount.vue'),
    meta: {
      hideForAuth: true,
    },
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('../pages/About.vue'),
  },

  {
    path: '/login',
    name: 'Login',
    component: () => import('../pages/Login.vue'),
    meta: {
      hideForAuth: true,
    },
  },
  {
    path: '/newsfeed',
    name: 'NewsFeed',
    component: () => import('../pages/NewsFeed.vue'),
    meta: {
      requiresAuth: true,
    }
  },

];

export default routes;