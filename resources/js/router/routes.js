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
    name: 'AboutLooped',
    component: () => import('../pages/AboutLooped.vue'),
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

    {
      path: '/profile/:profileId/edit',
      name: 'ProfileEdit',
      component: () => import('../pages/ProfileEdit.vue'),
      meta: {
        requiresAuth: true,
      }
    },

    {
    path: '/profile/:profileId/about',
    name: 'ProfileAbout',
    component: () => import('../pages/ProfileAbout.vue'),
    meta: {
      requiresAuth: true,
    }
  },
  {
    path: '/profile/:id',
    name: 'Profile',
    component: () => import('../pages/Profile.vue'),
    meta: {
      requiresAuth: true,
    }
  },

  // Always have NotFound as bottom route
  {
    path: '/not-found',
    alias: '*',
    name: 'NotFound',
    component: () => import ('../pages/NotFound.vue'),
  },

];

export default routes;