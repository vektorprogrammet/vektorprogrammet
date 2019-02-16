import { accountService } from '@/services';
import {ActionTree, GetterTree, Module, MutationTree} from 'vuex';
import {RootState} from '@/store/types';

export interface User {
  loaded: boolean;
  loading: boolean;
}

export interface AccountState {
  user: User;
}

export interface Credentials {
  username: string;
  password: string;
}

const defaultState: AccountState = {
  user: {
    loaded: false,
    loading: false,
  },
};

const state: AccountState = {...defaultState};

const actions: ActionTree<AccountState, RootState> = {
  async login({commit}: any, credentials: Credentials) {
    commit('loginRequest');

    try {
      const user = await accountService.login(credentials.username, credentials.password);
      commit('loginSuccessful', user);
    } catch (e) {
      commit('loginFailure', e);
    }
  },
  async logout({commit}: any) {
    try {
      await accountService.logout();
      commit('logoutSuccessful');
    } catch (e) {
      commit('logoutFailure');
    }
  },
  async getUser({commit}: any) {
    try {
      const user = await accountService.getUser();
      commit('loginSuccessful', user);
    } catch (e) {
      commit('loginFailure');
    }
  },
};

const getters: GetterTree<AccountState, RootState> = {
  user(state) {
    return state.user;
  },
};

const mutations: MutationTree<AccountState> = {
  loginRequest(state) {
    state.user.loaded = false;
    state.user.loading = true;
  },
  loginSuccessful(state, user) {
    if (!user.hasOwnProperty('username')) {
      state.user = {...defaultState.user};
      return;
    }

    state.user = {...state.user, ...user};
    state.user.loading = false;
    state.user.loaded = true;
  },
  loginFailure() {
    // TODO: Handle error
    // console.log('ERROR: ', e.message);
  },
  logoutSuccessful(state) {
    state.user = {...defaultState.user};
  },
  logoutFailure() {
    // TODO: Handle error
    // console.log('ERROR: ', e);
  },
};

export const account: Module<AccountState, RootState> = {
  namespaced: true,
  state,
  actions,
  getters,
  mutations,
};
