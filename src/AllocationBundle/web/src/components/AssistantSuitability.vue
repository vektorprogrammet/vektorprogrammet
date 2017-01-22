<template>
  <span>
    <span v-if="previousParticipation">Tidligere assistent</span>
    <span v-else>
      <span class="text-bold">{{ score }}</span> Poeng
       -
      Passer som assistent: <span class="text-bold">{{ suitable }}</span>
    </span>
  </span>
</template>

<script>
  export default {
    props: ['score', 'suitable', 'previousParticipation']
  }
</script>
