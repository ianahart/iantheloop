import VueRouter from 'vue-router';
import Vue from 'vue';
import routes from './routes';
import store from '../store/index';

const router = new VueRouter(
  {
  mode: 'history',
  routes,
  }
);

const authenticatedNotAuthorized = [
  'Profile',
  'ProfileAbout',
  'ProfileEdit',
  'Explore',
  'Following',
  'Followers',
  'NewsFeed',
  'CreateStory',
   'StoriesDashboard',
];

router.beforeEach((to, from, next) => {
  /*
  *
  * Only allow the user logged in to view only their newsfeed/stories/settings
  **/
    const slugRoutes = ['NewsFeed', 'Settings'];
    if (slugRoutes.includes(to.name) && to.params.slug !== store.getters['user/getUserSlug'] || to.name === 'StoriesDashboard' && parseInt(to.params.userId) !== store.getters['user/getUserId']) {
      next({ path: '/' })
    } else {
      next();
    }

  /*
  *
  * If user is authenticated but does not have a profile disallow them to visit profile(s)
  **/

  if (authenticatedNotAuthorized.includes(to.name) && to.meta.requiresAuth) {

    if (!store.getters['user/getProfileStatus']) {

      next({path: '/'});
    }

    next();
  }


  /*
  *
  * If user is not authenticated redirect to login. else continue to route
  **/
  if (to.matched.some((route) => route.meta.requiresAuth && !store.getters['user/isLoggedIn'])) {

    next({path: '/login'});
  } else {

    next();
  }

  /*
  *
  * If user is authenticated redirect to home. else continue to route
  **/
  if (to.matched.some((route) => route.meta.hideForAuth && store.getters['user/isLoggedIn'])) {

    next({path: '/'});
  } else {

    next();
  }

});

export default router;