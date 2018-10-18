import { stagingService } from '../services';

const state = {
  servers: [],
  diskSpace: {
    size: 1, 
    used: 1
  },
};

const actions = {
  async getServers({commit}) {
    commit('getServersRequest');

    try {
      const servers = await stagingService.getServers();
      commit('getServersSuccessful', servers);
    } catch (e) {
      commit('getServersFailure', e);
    }
  },
  async getDiskSpace({commit}) {
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
      {...s, repo: s.repo.replace('https://github.com/', '')}
    ));
  },
  diskSpace(state) {
    return state.diskSpace
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
    //TODO: Handle error
    // console.log('ERROR: ', e.message);
  },
  getDiskSpaceRequest(state) {
    state.loaded = false;
    state.loading = true;
  },
  getDiskSpaceSuccessful(state, diskSpace) {
    state.diskSpace= diskSpace;
    state.loading = false;
    state.loaded = true;
  },
  getDiskSpaceFailure() {
    //TODO: Handle error
    // console.log('ERROR: ', e.message);
  }
};

export const staging = {
  namespaced: true,
  state,
  actions,
  getters,
  mutations,
};
