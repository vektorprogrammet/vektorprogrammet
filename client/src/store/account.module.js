import { accountService } from '../services';

const defaultState = {
  user: {
    loaded: false,
    loading: false,
  },
  department: {
    loaded: false
  }
};

const state = {...defaultState};

const actions = {
  async login({commit}, credentials) {
    commit('loginRequest');

    try {
      const user = await accountService.login(credentials.username, credentials.password);
      commit('loginSuccessful', user);
    } catch (e) {
      commit('loginFailure', e);
    }
  },
  async logout({commit}) {
    try {
      await accountService.logout();
      commit('logoutSuccessful');
    } catch (e) {
      commit('logoutFailure', e);
    }
  },
  async getUser({commit}) {
    try {
      const user = await accountService.getUser();
      commit('loginSuccessful', user);
    } catch (e) {
      commit('loginFailure');
    }
  },
  async getDepartment({commit}) {
    try {
      const department = await accountService.getDepartment();
      commit('departmentFetchSuccessful', department)
    }catch (e) {
      console.log(e)
      commit('departmentFetchFailure', e);
    }
  }
};

const getters = {
  user(state) {
    return state.user;
  },
  department(state) {
    return state.department;
  }
};

const mutations = {
  loginRequest(state) {
    state.user.loaded = false;
    state.user.loading = true;
  },
  loginSuccessful(state, user) {
    if (!user.hasOwnProperty("username")) {
      state.user = {...defaultState.user};
      return;
    }

    state.user = {...state.user, ...user};
    state.user.loading = false;
    state.user.loaded = true;
  },
  loginFailure() {
    //TODO: Handle error
    // console.log('ERROR: ', e.message);
  },
  logoutSuccessful(state) {
    state.user = {...defaultState.user};
    state.department = {...defaultState.department};
  },
  logoutFailure() {
    //TODO: Handle error
    // console.log('ERROR: ', e);
  },
  departmentFetchSuccessful(state, department) {
    state.department = {...defaultState.department, ...department};
    state.department.loaded = true;
  },
  departmentFetchFailure() {
    //TODO: Handle error
    // console.log('ERROR: ', e);
  },
};

export const account = {
  namespaced: true,
  state,
  actions,
  getters,
  mutations,
};
