import { stagingService } from '../services';
import { fileSize } from '../util';

const state = {
  servers: [],
  diskSpace: {
    size: 1,
    used: 1,
  },
};

const actions = {
  async getServers({ commit }) {
    commit('getServersRequest');

    try {
      const servers = await stagingService.getServers();
      commit('getServersSuccessful', servers);
    } catch (e) {
      commit('getServersFailure', e);
    }
  },
  async getDiskSpace({ commit }) {
    commit('getDiskSpaceRequest');

    try {
      const diskSpace = await stagingService.getDiskSpace();
      commit('getDiskSpaceSuccessful', diskSpace);
    } catch (e) {
      commit('getDiskSpaceFailure', e);
    }
  },
};

const getters = {
  servers(state) {
    return state.servers.map(s => (
      { ...s, repo: s.repo.replace('https://github.com/', '') }
    ));
  },
  diskSpace(state) {
    return state.diskSpace;
  },
  diskSpaceSize(state) {
    return fileSize.kbToGb(state.diskSpace.size).toFixed(1);
  },
  diskSpaceUsed(state) {
    return fileSize.kbToGb(state.diskSpace.used).toFixed(1);
  },
  diskSpacePercent(state) {
    return (state.diskSpace.used / state.diskSpace.size * 100).toFixed(1);
  },
};

const mutations = {
  getServersRequest(state) {
    state.loaded = false;
    state.loading = true;
  },
  getServersSuccessful(state, servers) {
    state.servers = servers;
    state.loading = false;
    state.loaded = true;
  },
  getServersFailure() {
    // TODO: Handle error
    // console.log('ERROR: ', e.message);
  },
  getDiskSpaceRequest(state) {
    state.loaded = false;
    state.loading = true;
  },
  getDiskSpaceSuccessful(state, diskSpace) {
    state.diskSpace = diskSpace;
    state.loading = false;
    state.loaded = true;
  },
  getDiskSpaceFailure() {
    // TODO: Handle error
    // console.log('ERROR: ', e.message);
  },
  getDiskSpaceRequest(state) {
    state.loaded = false;
    state.loading = true;
  },
  getDiskSpaceSuccessful(state, diskSpace) {
    state.diskSpace = diskSpace;
    state.loading = false;
    state.loaded = true;
  },
  getDiskSpaceFailure() {
    // TODO: Handle error
    // console.log('ERROR: ', e.message);
  },
};

export const staging = {
  namespaced: true,
  state,
  actions,
  getters,
  mutations,
};
