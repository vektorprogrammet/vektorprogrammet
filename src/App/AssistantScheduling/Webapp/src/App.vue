<template>
  <v-app>
    <v-toolbar color="primary">
      <v-toolbar-title class="title" v-text="title"></v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-container>
          <a class="home-link" :href="'/kontrollpanel'" v-text="homeLinkText"/>
        </v-container>
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
            <v-card flat min-height="300"> 
              <school-table></school-table>
              <a class="add-school" href="/kontrollpanel/skole/capacity"><v-btn small color="success">Legg til skole</v-btn></a>
            </v-card>
            <v-container pb-2>
              <v-btn class="next" @click.native="goToStep(2)" color="primary">Neste &gt;</v-btn>
            </v-container>
          </v-stepper-content>
          <v-stepper-content step="2">
            <v-card flat min-height="300"> 
              <assistant-table></assistant-table>
            </v-card>
            <v-container pb-2>
              <v-btn @click.native="goToStep(1)">&lt; Tilbake</v-btn>
              <v-btn class="next" @click.native="goToStep(3)" color="primary">Neste &gt;</v-btn>
            </v-container>
          </v-stepper-content>
          <v-stepper-content step="3">
            <v-card flat min-height="300"> 
              <scheduling></scheduling>
            </v-card>
            <v-container pb-2>
              <v-btn @click.native="goToStep(2)" m5>&lt; Tilbake</v-btn>
            </v-container>
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
        stepper: 1,
        homeLinkText: "Tilbake til kontrollpanelet"
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
                  email: a.email,
                  double: a.doublePosition,
                  selected: false,
                  preferredGroup: a.preferredGroup,
                  suitable: a.suitable,
                  score: a.previousParticipation ? 20 : a.score,
                  monday: a.availability.Monday,
                  tuesday: a.availability.Tuesday,
                  wednesday: a.availability.Wednesday,
                  thursday: a.availability.Thursday,
                  friday: a.availability.Friday,
                  language: a.language,
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

<style lang="sass">
  @import '~vuetify/src/styles/main.sass'
  
  .home-link, .home-link:active, .home-link:visited
    color: #fff
    text-decoration: none
  
  .title
    color: #fff
    text-decoration: none

  .home-link:hover
    text-decoration: underline

  .add-school
    text-decoration: none
    margin-top: 25px
    display: block
    text-align: center
  
  .v-toolbar
    display: block
    flex: none

  .button
    margin: 100

  .next
    float: right
</style>
