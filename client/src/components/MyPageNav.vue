<template>
  <div class="assistant-nav">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="/">Forsiden</a>
      </li>
      <li class="breadcrumb-item">
        Min side
      </li>
      <li class="breadcrumb-menu"></li>
    </ol>
    <user-dropdown-component></user-dropdown-component>
  </div>
</template>

<script lang="ts">
import UserDropdownComponent from './UserDropdownComponent.vue';
import Vue from 'vue';
import Component from 'vue-class-component';
@Component({
  components: { UserDropdownComponent },
})
export default class MyPageNav extends Vue {}
</script>
