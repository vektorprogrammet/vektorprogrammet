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
      <v-flex xs12 v-if="scheduleData.hasOwnProperty('queuedAssistants')">
        <h3>Assistenter som ikke ble fordelt</h3>
        <table>
          <tr>
            <th>Navn</th>
            <th>Poeng</th>
            <th>Ønsket pulje</th>
            <th>Dobbel stilling</th>
            <th v-for="day in weekDays">{{day}}</th>
          </tr>
          <tr v-for="assistant in scheduleData.queuedAssistants">
            <td>{{assistant.name}}</td>
            <td>{{assistant.score}}</td>
            <td>{{assistant.preferredGroup}}</td>
            <td>{{assistant.double}}</td>
            <td v-for="day in weekDays">{{assistant[day]}}</td>
          </tr>
        </table>
        <p>{{scheduleData.queuedAssistants.length}}</p>
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
