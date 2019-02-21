<template>
  <div class="admin-nav">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="/">Forsiden</a>
      </li>
      <li class="breadcrumb-item">
        Kontrollpanel
      </li>
      <li class="breadcrumb-menu"></li>
    </ol>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
@Component
export default class AdminNav extends Vue {}
</script>
