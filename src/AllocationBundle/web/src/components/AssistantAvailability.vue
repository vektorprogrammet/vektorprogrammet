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
     -
    <span>{{ getAvailableGroup() }}</span>
  </span>
</template>

<script>
  export default {
    props: ['availability', 'preferredGroup', 'doublePosition'],
    methods: {
      getAvailableGroup() {
        if (this.doublePosition) {
          return 'Dobbel';
        } else if (this.preferredGroup !== null) {
          return 'Bolk ' + this.preferredGroup;
        } else {
          return 'Bolk 1 eller Bolk 2';
        }
      }
    }
  }
</script>
