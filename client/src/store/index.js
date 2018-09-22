import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate';

import { account } from './account.module';

Vue.use(Vuex);
export default new Vuex.Store({
  plugins: [createPersistedState()],
  modules: {
    account,
  },
});
