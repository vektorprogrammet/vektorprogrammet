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
  user(currentState) {
    return currentState.user;
  },
  department(currentState) {
    return currentState.department;
  }
};

const mutations = {
  loginRequest(currentState) {
    currentState.user.loaded = false;
    currentState.user.loading = true;
  },
  loginSuccessful(currentState, user) {
    if (!user.hasOwnProperty("username")) {
      currentState.user = {...defaultState.user};
      return;
    }

    currentState.user = {...currentState.user, ...user};
    currentState.user.loading = false;
    currentState.user.loaded = true;
  },
  loginFailure() {
    //TODO: Handle error
    // console.log('ERROR: ', e.message);
  },
  logoutSuccessful(currentState) {
    currentState.user = {...defaultState.user};
    currentState.department = {...defaultState.department};
  },
  logoutFailure() {
    //TODO: Handle error
    // console.log('ERROR: ', e);
  },
  departmentFetchSuccessful(currentState, department) {
    currentState.department = {...defaultState.department, ...department};
    currentState.department.loaded = true;
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
