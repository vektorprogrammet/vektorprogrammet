<template>
  <span>
    <span v-if="previousParticipation">Tidligere assistent</span>
    <span v-else>
      {{ score }} Poeng
       -
      Passer som assistent: {{ suitable }}
    </span>
  </span>
</template>

<script>
  export default {
    props: ['score', 'suitable', 'previousParticipation']
  }
</script>
