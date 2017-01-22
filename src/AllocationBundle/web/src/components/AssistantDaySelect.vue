<template>
  <label>
    Dag
    <select @change="changeDay" :value="selectedDay">
      <option value="">Velg dag</option>
      <option :value="day" v-for="day in days">{{ getNorwegianDay(day) }}</option>
    </select>
  </label>
</template>

<script>
  export default {
    props: ['days', 'selectedDay'],
    methods: {
      changeDay(event) {
        this.$emit('dayChanged', event.target.value)
      },
      getNorwegianDay(idx) {
        if (typeof idx === 'string') {
          idx = parseInt(idx);
        }
        const week = ['Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag'];

        return week[idx]
      }
    }
  }
</script>
