import Vue from 'vue';
import Router from 'vue-router';
import store from '../store';

import MyPageView from '../views/assistant/MyPageView';
import LoginView from '../views/LoginView';
import LogoutView from '../views/LogoutView'
import Error404View from '../views/Error404View';
import Error403View from '../views/Error403View';
import StagingServerView from '../views/controlpanel/StagingServerView';
import AdminBaseView from '../views/controlpanel/AdminBaseView';
import AssistantBaseView from '../views/assistant/AssistantBaseView';
import PartyPageView from '../views/partypage/PartyPageView';

Vue.use(Router);

const router = new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'root'
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/logout',
      name: 'logout',
      component: LogoutView
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
    {
      path: '/kontrollpanel',
      name: 'controlpanel',
      component: AdminBaseView,
      children: [
        {
          path: 'staging',
          name: 'staging',
          component: StagingServerView,
        },
      ],
    },
    {
      path: '/assistent',
      name: 'assistant',
      component: AssistantBaseView,
      children: [
        {
          path: 'min-side',
          name: 'my_page',
          component: MyPageView,
        },
      ],
    },
    {
      path: '/party',
      name: 'party',
      component: PartyPageView,
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  
  if (to.name === '404'|| to.name === 'logout') {
    next();
    return;
  }

  if (to.name === 'root') {
    next({name: 'login'})
    return;
  }

  let loggedInUser = store.getters['account/user'];
  if (!loggedInUser.loaded) {
    await store.dispatch('account/getUser');
  }
  loggedInUser = store.getters['account/user'];

  if (loggedInUser.loaded) {
    await store.dispatch('account/getDepartment');
  }

  if (to.name === 'login') {
    if (!loggedInUser.loaded){
      next();
    } else {
      next({name: 'my_page'});
    }
    return;
  }

  if (to.path.indexOf('/kontrollpanel/staging') === 0 && !loggedInUser.isAdmin) {
    next({name: '403'});
    return
  }
  next();
});

export default router;
