import Vue from 'vue';
import Router from 'vue-router';
import store from '../store';

import MyPageView from '../views/MyPageView'
import LoginView from '../views/LoginView'
import Error404View from '../views/Error404View'

Vue.use(Router);

const router = new Router({
  mode: 'history',
  routes: [
    {
      path: '/min-side',
      name: 'my_page',
      component: MyPageView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '*',
      name: '404',
      component: Error404View
    }
  ],
});

router.beforeEach(async (to, from, next) => {
  if (to.name === 'login' || to.name === '404') {
    next();
    return;
  }
  let loggedInUser = store.getters['account/user'];
  if (loggedInUser.loaded) {
    next();
    return;
  }

  next({name: 'login'});
});

export default router;
