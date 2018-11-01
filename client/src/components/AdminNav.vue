<template>
  <div class="admin-nav">
    <router-link to="controlpanel">Kontrollpanel</router-link>
    <slot/>
  </div>
</template>

<script>
  export default {
    name: 'AdminNav',
  };
</script>
