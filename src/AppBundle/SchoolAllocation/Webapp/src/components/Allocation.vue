<template>
  <v-layout row wrap>

    <v-flex xs12 sm6 v-show="schools.length > 0 && !scheduleData.hasOwnProperty('monday')">
      <h4>Skoler</h4>
      <v-list>
        <v-list-item v-for="school in schools">
          <v-list-tile avatar ripple>
            <v-list-tile-content>
              <v-list-tile-title>{{ school.name }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
          <v-divider></v-divider>
        </v-list-item>
      </v-list>
    </v-flex>

    <v-flex xs12 sm6 v-show="assistants.length > 0 && !scheduleData.hasOwnProperty('monday')">
      <h4>Assistenter</h4>
      <v-list>
        <v-list-item v-for="assistant in assistants">
          <v-list-tile avatar ripple>
            <v-list-tile-content>
              <v-list-tile-title>{{ assistant.name }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
          <v-divider></v-divider>
        </v-list-item>
      </v-list>
    </v-flex>

    <v-flex xs12 v-show="scheduleData.hasOwnProperty('monday')">
      <schedule :scheduleData="scheduleData"></schedule>
    </v-flex>

    <v-flex xs12 class="status">
      <v-btn v-show="!allocating" @click.native="allocate" primary light>Fordel</v-btn>
      <div class="text-center" v-show="allocating">Fordeler... <span v-show="showMessage">(Det skjer faktisk ingenting, jeg bare later som)</span>
      </div>
      <v-progress-linear v-show="allocating" v-model="progress"></v-progress-linear>
      <p v-show="allocating">{{progress}} %</p>
    </v-flex>

  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex";
  import {Schedule} from "../Schedule";
  import {scheduleGreedily} from "../GreedyScheduler";
  import {SAOptimize} from "../SAOptimizer";

  export default {
    methods: {
      allocate () {
        this.allocating = true;
        let schedule = new Schedule(this.schools, this.assistants);
        schedule = scheduleGreedily(schedule);
        SAOptimize(schedule, progress => {
          this.progress = progress;
        }, result => {
          setTimeout(() => {
            this.scheduleData = result;
            this.allocating = false;
          }, 750)
        });
      },
      goBack() {
        this.$emit('goBack');
      }
    },

    computed: mapGetters({
      assistants: 'selectedAssistants',
      schools: 'selectedSchools',
    }),
    data () {
      return {
        allocating: false,
        progress: 0,
        showMessage: false,
        scheduleData: {}
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .status
    margin-top: 100px
    text-align: center
</style>
