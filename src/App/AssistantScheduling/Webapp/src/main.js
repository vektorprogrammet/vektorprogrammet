import Vue from 'vue'
import VueResource from 'vue-resource'
import App from './App.vue'
import vuetify from './plugins/vuetify' // path to vuetify export

import store from './store';

import TimeTable from './components/TimeTable.vue';
Vue.component('time-table', TimeTable)

// Vue.use(vuetify)
Vue.use(VueResource)

new Vue({
  vuetify,
  el: '#app',
  store,
  render: h => h(App)
})
