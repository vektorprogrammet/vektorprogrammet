<template>
  <span>
    <span :class="{'text-success': availability.Monday}">M</span>
    &nbsp;
    <span :class="{'text-success': availability.Tuesday}">T</span>
    &nbsp;
    <span :class="{'text-success': availability.Wednesday}">O</span>
    &nbsp;
    <span :class="{'text-success': availability.Thursday}">T</span>
    &nbsp;
    <span :class="{'text-success': availability.Friday}">F</span>
  </span>
</template>

<script>
  export default {
    props: ['availability']
  }
</script>
