import Vue from 'vue';
import Router from 'vue-router';
import store from '../store';

import MyPageView from '../views/assistant/MyPageView.vue';
import LoginView from '../views/LoginView.vue';
import Error404View from '../views/Error404View.vue';
import Error403View from '../views/Error403View.vue';
import StagingServerView from '../views/controlpanel/StagingServerView.vue';
import AdminBaseView from '../views/controlpanel/AdminBaseView.vue';
import AssistantBaseView from '../views/assistant/AssistantBaseView.vue';

Vue.use(Router);

const router = new Router({
  mode: 'history',
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
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
  ],
});

router.beforeEach(async (to, from, next) => {
  if (to.name === 'login' || to.name === '404') {
    next();
    return;
  }
  let loggedInUser = store.getters['account/user'];
  if (!loggedInUser.loaded) {
    await store.dispatch('account/getUser');
  }

  loggedInUser = store.getters['account/user'];
  if (!loggedInUser.loaded) {
    next({ name: 'login' });
    return;
  }

  if (to.path.indexOf('/kontrollpanel/staging') === 0 && !loggedInUser.isAdmin) {
    next({ name: '403' });
    return;
  }
  next();
});

export default router;
