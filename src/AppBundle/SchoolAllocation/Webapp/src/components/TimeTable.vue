<template>
  <div>
    <h2>Timeplan</h2>
      <div v-for="school in schools">
        <school :school="timeTable[school]" :name="school"></school>
        <hr>
      </div>
  </div>
</template>

<script>

  import SchoolTimeTable from './SchoolTimeTable.vue';

  export default {
    components: {
      'school': SchoolTimeTable
    },
    props: ['timeTable'],
    data() {
      return {
        weekDays: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']
      }
    },
    created() {
      this.schools = Object.keys(this.timeTable);
    }
  }
</script>

<style lang="stylus" scoped>
  .status
    margin-top: 100px
    text-align: center

  hr
    border-color: rgba(240, 240, 240, 0.17)
</style>
