import { stagingService } from '../services';

const state = {
  servers: [],
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
};

const getters = {
  servers(state) {
    return state.servers.map(s => (
      {...s, repo: s.repo.replace('https://github.com/', '')}
    ));
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
  getServersFailure(e) {
    //TODO: Handle error
    console.log('ERROR: ', e.message);
  },
};

export const staging = {
  namespaced: true,
  state,
  actions,
  getters,
  mutations,
};
