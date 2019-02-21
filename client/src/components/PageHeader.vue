<template>
  <div class="page-header">
    <slot/>
  </div>
</template>

<script lang="ts">
    import Vue from 'vue';
    import Component from 'vue-class-component';
    @Component
    export default class PageHeader extends Vue {
    }
</script>

<style lang="scss" scoped>
  .page-header {
    margin-top: 50px;
    margin-bottom: 100px;

    h1 {
      text-align: center;
    }
  }
</style>
