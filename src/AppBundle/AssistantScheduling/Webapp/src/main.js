import Vue from 'vue'
import VueResource from 'vue-resource'
import App from './App.vue'
import Vuetify from 'vuetify'

import store from './store';

import TimeTable from './components/TimeTable.vue';
Vue.component('time-table', TimeTable)

Vue.use(Vuetify)
Vue.use(VueResource)

new Vue({
  el: '#app',
  store,
  render: h => h(App)
})
