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
    path: '/stories/create',
    name: 'CreateStory',
    component: () => import('../pages/CreateStory.vue'),
    meta: {
      requiresAuth: true,
    }
  },
  {
   path: '/stories/:userId',
   name: 'StoriesDashboard',
   component: () => import('../pages/StoriesDashboard.vue'),
   meta: {
     requiresAuth: true,
   }
  },
   {
    path: '/reviews',
    name: 'Reviews',
    component: () => import('../pages/Reviews.vue'),
  },
  {
    path: '/reviews/:userId',
    name: 'UserReview',
    component: () => import('../pages/UserReview.vue'),
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
    path: '/newsfeed/:slug',
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
    path: '/settings/:slug',
    name: 'Settings',
    component: () => import('../pages/Settings.vue'),
    meta: {
      requiresAuth: true,
    }
  },
  {
    path: '/network/following/:id',
    name: 'Following',
    component: () => import('../pages/Following.vue'),
    meta: {
      requiresAuth: true,
    }
  },
  {
    path: '/network/followers/:id',
    name: 'Followers',
    component: () => import('../pages/Followers.vue'),
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