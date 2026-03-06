<template>
  <div v-if="scheduleData.hasOwnProperty('monday')">
    <h2>Timeplan</h2>
    <v-layout row wrap>
      <v-flex xs6>
        <h3>Pulje 1</h3>
        <assistant-list
            v-for="d in weekDays"
            :assistants="scheduleData[d + 'AssistantsGroup1']"
            :day="d"
            :capacity="scheduleData[d]"
        ></assistant-list>
      </v-flex>
      <v-flex xs6>
          <h3>Pulje 2</h3>
          <assistant-list
              v-for="d in weekDays"
              :assistants="scheduleData[d + 'AssistantsGroup2']"
              :day="d"
              :capacity="scheduleData[d]"
          ></assistant-list>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
  import AssistantList from './AssistantList.vue';

  export default {
    props: ['scheduleData'],
    components: {
      'assistant-list': AssistantList
    },
    data() {
      return {
        weekDays: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .status
    margin-top: 100px
    text-align: center
</style>
