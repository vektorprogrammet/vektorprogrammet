<template>
  <div class="adminbaseview">
    <AdminNav></AdminNav>
    <router-view></router-view>
  </div>
</template>

<script lang="ts">
import AdminNav from '../../components/AdminNav.vue';
import { Component, Vue } from 'vue-property-decorator';
@Component({
  components: { AdminNav },
})
export default class AdminBaseView extends Vue {}
</script>
