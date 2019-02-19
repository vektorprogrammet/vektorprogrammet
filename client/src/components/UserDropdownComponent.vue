<template>
  <div class="user-dropdown">
    <div>
      <div style="background-image: url('../assets/logo.png')" class="profile-picture profile-photo"></div>
    </div>
    <div class="dropdown">
      <span class="user-link dropdown-toggle" id="user-menu" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">User</span>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu">
        <a class="dropdown-item active" href="#">
          <i class="fa fa-icon"></i>
          Min side
        </a>
        <a class="dropdown-item active" href="#">
          <i class="fa fa-icon"></i>
          Profil
        </a>
        <a class="dropdown-item active" href="#">
          <i class="fa fa-icon"></i>
          Mine utlegg
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item active" href="#">
          <i class="fa fa-icon"></i>
          Mine vektorpartnere
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item active" href="#">
          <i class="fa fa-icon"></i>
          Logg ut
        </a>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';
  import Component from 'vue-class-component'
  @Component
  export default class UserDropdownComponent extends Vue {}
</script>

<style scoped>

</style>
