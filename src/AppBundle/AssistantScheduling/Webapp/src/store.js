import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    assistants: [],
    schools: []
  },
  getters: {
    assistants(state) {
      return state.assistants
    },
    schools(state) {
      return state.schools
    },
    selectedSchools(state) {
      return state.schools.filter(school => school.selected);
    },
    selectedAssistants(state) {
      return state.assistants.filter(assistant => assistant.selected);
    }
  }
});
