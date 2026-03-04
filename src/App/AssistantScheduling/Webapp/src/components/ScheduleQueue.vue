<template>
  <v-flex xs12>
    <h3>Assistenter som ikke ble fordelt</h3>
    <table>
      <caption>Tabell over assistenter</caption>
      <tr>
        <th scope="col">Navn</th>
        <th scope="col">Poeng</th>
        <th scope="col">Ønsket pulje</th>
        <th scope="col">Dobbel stilling</th>
        <th scope="col" v-for="day in weekDays">{{day}}</th>
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

<style lang="sass" scoped>
  .status
    margin-top: 100px
    text-align: center
</style>
