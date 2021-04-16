const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../pages/Home.vue'),
  },
  {
    path: '/create-account',
    name: 'CreateAccount',
    component: () => import('../pages/CreateAccount.vue')
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
  },
];

export default routes;