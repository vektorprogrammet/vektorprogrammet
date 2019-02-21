import { stagingService } from '@/services';
import { fileSize } from '@/util';

export interface DiskSpace {
  size: number;
  used: number;
}

export interface RootState {
  loading: boolean;
  loaded: boolean;
}

export interface StagingState extends RootState {
  servers: any[]; // TODO: Define Server interface
  diskSpace: DiskSpace;
}

const state: StagingState = {
  servers: [],
  diskSpace: {
    size: 1,
    used: 1,
  },
  loading: false,
  loaded: false,
};

const actions = {
  async getServers({ commit }: any) {
    commit('getServersRequest');

    try {
      const servers = await stagingService.getServers();
      commit('getServersSuccessful', servers);
    } catch (e) {
      commit('getServersFailure', e);
    }
  },
  async getDiskSpace({ commit }: any) {
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
  servers(state: StagingState) {
    return state.servers.map(s => ({
      ...s,
      repo: s.repo.replace('https://github.com/', ''),
    }));
  },
  diskSpaceSize(state: StagingState) {
    return fileSize.kbToGb(state.diskSpace.size).toFixed(1);
  },
  diskSpaceUsed(state: StagingState) {
    return fileSize.kbToGb(state.diskSpace.used).toFixed(1);
  },
  diskSpacePercent(state: StagingState) {
    return ((state.diskSpace.used / state.diskSpace.size) * 100).toFixed(1);
  },
};

const mutations = {
  getServersRequest(state: StagingState) {
    state.loaded = false;
    state.loading = true;
  },
  getServersSuccessful(state: StagingState, servers: any[]) {
    state.servers = servers;
    state.loading = false;
    state.loaded = true;
  },
  getServersFailure() {
    // TODO: Handle error
    // console.log('ERROR: ', e.message);
  },
  getDiskSpaceRequest(state: StagingState) {
    state.loaded = false;
    state.loading = true;
  },
  getDiskSpaceSuccessful(state: StagingState, diskSpace: DiskSpace) {
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
