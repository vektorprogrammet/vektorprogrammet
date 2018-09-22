import Vue from 'vue';
import Router from 'vue-router';
import store from '../store';

import MyPageView from '../views/MyPageView';
import LoginView from '../views/LoginView';
import Error404View from '../views/Error404View';
import Error403View from '../views/Error403View';
import StagingServerView from '../views/controlpanel/StagingServerView';

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
      path: '/kontrollpanel/staging',
      name: 'staging',
      component: StagingServerView,
    },
    {
      path: '*',
      name: '404',
      component: Error404View,
    },
    {
      path: '/403',
      name: '403',
      component: Error403View,
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  if (to.name === 'login' || to.name === '404') {
    next();
    return;
  }
  let loggedInUser = store.getters['account/user'];
  if (!loggedInUser.loaded) {
    next({name: 'login'});
    return;
  }
  if (to.path.indexOf('/kontrollpanel/staging') === 0 && !loggedInUser.isAdmin) {
    next({name: '403'});
    return
  }
  next();
});

export default router;
