const routes = [
  {
    path: '/about',
    name: 'About',
    component: () => import('../pages/About.vue'),
  }
];

export default routes;