<template>
  <label>
    Skole
    <select @change="changeSchool" :value="selectedSchool">
      <option value="">Velg skole</option>
      <option :value="school.id" v-for="school in schools">{{ school.name }}</option>
    </select>
  </label>
</template>

<script>
  export default {
    props: ['schools', 'selectedSchool'],
    methods: {
      changeSchool(event) {
        this.$emit('schoolChanged', event.target.value)
      }
    }
  }
</script>
