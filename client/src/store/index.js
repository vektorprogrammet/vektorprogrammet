import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);
export default new Vuex.Store({
    state: {
        user: null,
    },
    mutations: {
        setUser(state, payload) {
            console.log(state)
            console.log(payload)
            state.user = payload.user
        }
    },
    actions: {
        setUser(context, payload) {
            context.commit('setUser', payload)
        }
    }
});
