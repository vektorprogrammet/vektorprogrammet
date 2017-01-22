<template>
  <label>
    Bolk
    <select @change="changeGroup" :value="selectedGroup">
      <option value="">Velg bolk</option>
      <option :value="group" v-for="group in groups">{{ group }}</option>
    </select>
  </label>
</template>

<script>
  export default {
    props: ['groups', 'selectedGroup'],
    methods: {
      changeGroup(event) {
        console.log(event);
        this.$emit('groupChanged', event.target.value);
      }
    }
  }
</script>
