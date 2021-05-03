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
    path: '/recovery/create',
    name: 'ForgotPassword',
    component: () => import('../pages/ForgotPassword.vue'),
    meta: {
      hideForAuth: true,
    },
  },
  {
    path: '/reset-password/create',
    name: 'ResetPassword',
    component: () => import ('../pages/ResetPassword.vue'),
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
  {
    path: '/profile/create',
    name: 'CreateProfile',
    component: () => import('../pages/CreateProfile.vue'),
    meta: {
      requiresAuth: true,
    }
  },
  {
    path: '/friends',
    name: 'Friends',
    component: () => import('../pages/Friends.vue'),
    meta: {
      requiresAuth: true,
    }
  },
  {
    path: '/find-friends',
    name: 'FindFriends',
    component: () => import('../pages/FindFriends.vue'),
    meta: {
      requiresAuth: true,
    }
  },

  // Always have NotFound as bottom route
  {
    path: '*',
    name: 'NotFound',
    component: () => import ('../pages/NotFound.vue'),
  },

];

export default routes;