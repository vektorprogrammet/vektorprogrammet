<template>
  <v-flex xs12>
    <h3>Assistenter som ikke ble fordelt</h3>
    <table>
      <tr>
        <th>Navn</th>
        <th>Poeng</th>
        <th>Ønsket pulje</th>
        <th>Dobbel stilling</th>
        <th v-for="day in weekDays">{{day}}</th>
      </tr>
      <tr v-for="assistant in queue">
        <td>{{assistant.name}}</td>
        <td>{{assistant.score}}</td>
        <td>{{assistant.preferredGroup}}</td>
        <td>{{assistant.double}}</td>
        <td v-for="day in weekDays">{{assistant[day]}}</td>
      </tr>
    </table>
    <p>Antall: {{queue.length}}</p>
  </v-flex>
</template>

<script>
  import AssistantList from './AssistantList.vue';

  export default {
    props: ['queue'],
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
