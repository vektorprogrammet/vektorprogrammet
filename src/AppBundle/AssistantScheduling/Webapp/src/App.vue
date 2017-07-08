<template>
  <v-app>
    <v-toolbar>
      <v-toolbar-title v-text="title"></v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <a class="home-link" :href="'/kontrollpanel'">Tilbake til kontrollpanelet</a>
      </v-toolbar-items>
    </v-toolbar>
    <main>
      <v-container>
        <v-stepper non-linear v-model="stepper">
          <v-stepper-header>
            <v-stepper-step step="1" editable>Velg skoler</v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="2" editable>Velg assistenter</v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="3" editable>Generer!</v-stepper-step>
          </v-stepper-header>
          <v-stepper-content step="1">
            <school-table></school-table>
            <a class="add-school" href="/kontrollpanel/skole/capacity"><v-btn small success light>Legg til skole</v-btn></a>
            <br>
            <v-btn class="next" @click.native="goToStep(2)" primary light>Neste &gt;</v-btn>
          </v-stepper-content>
          <v-stepper-content step="2">
            <assistant-table></assistant-table>
            <v-btn @click.native="goToStep(1)" dark>&lt; Tilbake</v-btn>
            <v-btn class="next" @click.native="goToStep(3)" primary light>Neste &gt;</v-btn>
          </v-stepper-content>
          <v-stepper-content step="3">
            <scheduling></scheduling>
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
  import Scheduling from './components/Scheduling.vue'

  export default {
    components: {
      'school-table': SchoolTable,
      'assistant-table': AssistantTable,
      'scheduling': Scheduling,
    },
    data() {
      return {
        title: 'Vektorprogrammet - Timeplangenerator',
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
                  id: a.id,
                  name: a.name,
                  double: a.doublePosition,
                  selected: false,
                  preferredGroup: a.preferredGroup,
                  suitable: a.suitable,
                  score: a.previousParticipation ? 20 : a.score,
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
                  id: s.id,
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

  .home-link, .home-link:active, .home-link:visited
    color: #fff
    text-decoration none

  .home-link:hover
    text-decoration: underline

  .add-school
    text-decoration: none
    margin-top: 25px
    display: block
    text-align: center;

  .next
    float: right;
</style>
