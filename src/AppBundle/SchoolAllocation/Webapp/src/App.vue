<template>
  <v-app>
    <v-toolbar>
      <v-toolbar-title v-text="title"></v-toolbar-title>
    </v-toolbar>
    <main>
      <v-container>
        <v-stepper non-linear>
          <v-stepper-header>
            <v-stepper-step step="1" editable>Velg skoler</v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="2" editable>Velg assistenter</v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="3" editable>Fordel!</v-stepper-step>
          </v-stepper-header>
          <v-stepper-content step="1">
            <school-table></school-table>
          </v-stepper-content>
          <v-stepper-content step="2">
            <assistant-table></assistant-table>
          </v-stepper-content>
          <v-stepper-content step="3">
            <v-btn v-show="!allocating" v-on:click.native="allocate" primary light>Fordel</v-btn>
            <div class="text-center" v-show="allocating">Fordeler... <span v-show="showMessage">(Det skjer faktisk ingenting, jeg bare later som)</span></div>
            <v-progress-linear v-show="allocating" v-bind:indeterminate="true"></v-progress-linear>
          </v-stepper-content>
        </v-stepper>
      </v-container>
    </main>
  </v-app>
</template>

<script>
    import SchoolTable from './components/SchoolTable.vue'
    import AssistantTable from './components/AssistantTable.vue'

    export default {
        components: {
            'school-table': SchoolTable,
            'assistant-table': AssistantTable
        },

        methods: {
            allocate: function(event) {
                this.allocating = true;
                setInterval(() => {this.showMessage = true}, 5000);
            }
        },

        data() {
            return {
                title: 'Vektorprogrammet - Skolefordeling',
                allocating: false,
                showMessage: false
            }
        }
    }
</script>

<style lang="stylus">
  @import './stylus/main'
</style>
