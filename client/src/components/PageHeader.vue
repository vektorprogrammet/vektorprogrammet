<template>
  <div class="page-header">
    <slot/>
  </div>
</template>

<script>
  export default {
    name: 'PageHeader',
  };
</script>

<style lang="scss" scoped>
  @import "../../../app/Resources/assets/scss/modules/page-header";

</style>
