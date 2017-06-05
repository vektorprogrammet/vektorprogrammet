<template>
  <v-app>
    <v-toolbar>
      <v-toolbar-title v-text="title"></v-toolbar-title>
    </v-toolbar>
    <main>
      <v-container>
        <v-stepper non-linear v-model="stepper">
          <v-stepper-header>
            <v-stepper-step step="1" editable>Velg skoler</v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="2" editable>Velg assistenter</v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="3" editable>Fordel!</v-stepper-step>
          </v-stepper-header>
          <v-stepper-content step="1">
            <school-table></school-table>
            <v-btn @click.native="goToStep(2)" primary light>Neste &gt;</v-btn>
          </v-stepper-content>
          <v-stepper-content step="2">
            <assistant-table></assistant-table>
            <v-btn @click.native="goToStep(1)" dark>&lt; Tilbake</v-btn>
            <v-btn @click.native="goToStep(3)" primary light>Neste &gt;</v-btn>
          </v-stepper-content>
          <v-stepper-content step="3">
            <allocation></allocation>
            <v-btn @click.native="goToStep(2)" dark>&lt; Tilbake</v-btn>
          </v-stepper-content>
        </v-stepper>
      </v-container>
    </main>
  </v-app>
</template>

<script>
  import SchoolTable from './components/SchoolTable.vue'
  import AssistantTable from './components/AssistantTable.vue'
  import Allocation from './components/Allocation.vue'

  export default {
    components: {
      'school-table': SchoolTable,
      'assistant-table': AssistantTable,
      'allocation': Allocation
    },
    data() {
      return {
        title: 'Vektorprogrammet - Skolefordeling',
        stepper: 1
      }
    },
    created () {
      this.fetchSchools();
      this.fetchAssistants();
    },
    methods: {
      goToStep (step) {
        this.stepper = step;
      },
      fetchAssistants () {
        this.$http.get('/kontrollpanel/api/assistants')
            .then(response => {
              this.$store.state.assistants = JSON.parse(response.body).map(a => {
                return {
                  name: a.name,
                  double: a.doublePosition,
                  selected: false,
                  monday: a.availability.Monday === 1,
                  tuesday: a.availability.Tuesday === 1,
                  wednesday: a.availability.Wednesday === 1,
                  thursday: a.availability.Thursday === 1,
                  friday: a.availability.Friday === 1
                }
              });
            });
      },
      fetchSchools () {
        this.$http.get('/kontrollpanel/api/schools')
            .then(response => {
              this.$store.state.schools = JSON.parse(response.body).map(s => {
                return {
                  name: s.name,
                  selected: false,
                  monday: s.capacity['1'].Monday,
                  tuesday: s.capacity['1'].Tuesday,
                  wednesday: s.capacity['1'].Wednesday,
                  thursday: s.capacity['1'].Thursday,
                  friday: s.capacity['1'].Friday,
                }
              });
            });
      }
    }
  }
</script>

<style lang="stylus">
  @import './stylus/main'
</style>
