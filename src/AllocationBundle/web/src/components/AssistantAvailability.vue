<template>
  <span>
    <span :class="{'text-success': availability.Monday, 'text-bold': availability.Monday}">M</span>
    &nbsp;
    <span :class="{'text-success': availability.Tuesday, 'text-bold': availability.Tuesday}">T</span>
    &nbsp;
    <span :class="{'text-success': availability.Wednesday, 'text-bold': availability.Wednesday}">O</span>
    &nbsp;
    <span :class="{'text-success': availability.Thursday, 'text-bold': availability.Thursday}">T</span>
    &nbsp;
    <span :class="{'text-success': availability.Friday, 'text-bold': availability.Friday}">F</span>
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
