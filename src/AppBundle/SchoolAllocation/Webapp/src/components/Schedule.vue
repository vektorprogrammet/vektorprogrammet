<template>
  <div>
    <h2>Timeplan</h2>
    <v-layout row>
      <v-flex xs6>
        <v-card>
          <h3>Pulje 1</h3>
          <h5>Mandag</h5>
          <ul>
            <li v-for="assistant in scheduleData.mondayAssistantsGroup1">
              {{assistant.name}}
            </li>
          </ul>
          <h5>Tirsdag</h5>
          <ul>
            <li v-for="assistant in scheduleData.tuesdayAssistantsGroup1">
              {{assistant.name}}
            </li>
          </ul>
          <h5>Onsdag</h5>
          <ul>
            <li v-for="assistant in scheduleData.wednesdayAssistantsGroup1">
              {{assistant.name}}
            </li>
          </ul>
          <h5>Torsdag</h5>
          <ul>
            <li v-for="assistant in scheduleData.thursdayAssistantsGroup1">
              {{assistant.name}}
            </li>
          </ul>
          <h5>Fredag</h5>
          <ul>
            <li v-for="assistant in scheduleData.fridayAssistantsGroup1">
              {{assistant.name}}
            </li>
          </ul>
        </v-card>
      </v-flex>
      <v-flex xs6>
        <v-card>
          <h3>Pulje 2</h3>
        </v-card>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
  export default {
    props: ['scheduleData'],
    created() {
      console.log(this.scheduleData)
    },
  }
</script>

<style lang="stylus" scoped>
  .status
    margin-top: 100px
    text-align: center
</style>
