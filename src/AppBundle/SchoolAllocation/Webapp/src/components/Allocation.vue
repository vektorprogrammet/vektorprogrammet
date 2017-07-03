<template>
  <v-layout row wrap>

    <v-flex xs12 sm6 v-show="!allocating && schools.length > 0 && !scheduleData.hasOwnProperty('monday')">
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

    <v-flex xs12 sm6 v-show="!allocating && assistants.length > 0 && !scheduleData.hasOwnProperty('monday')">
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
      <div class="text-center" v-show="allocating">
        <p>Fordeling pågår. Dette kan ta noen minutter...</p>
        <br>
        <v-progress-circular
            v-bind:size="100"
            v-bind:width="15"
            v-bind:rotate="180"
            v-bind:value="progress"
            class="primary--text"
        >{{ progress }} %</v-progress-circular>
      </div>
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
      schedule (i) {
        let schedule = new Schedule(this.schools, this.assistants);
        schedule = scheduleGreedily(schedule);

        SAOptimize(schedule, () => {
        }, result => {
          const calculatedProgress = Math.round((i + 1) / this.numberOfRuns * 100);
          this.progress = Math.min(100, calculatedProgress);
          if (!this.bestSchedule || result.fitness() > this.bestSchedule.fitness()) {
            this.bestSchedule = result;
          }
          if (i < this.numberOfRuns) {
            this.schedule (i + 1);
          } else {

            this.scheduleData = this.bestSchedule;
            this.allocating = false;
          }
        })
      },
      allocate () {
        this.allocating = true;
        this.schedule (0);
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
        scheduleData: {},
        bestResult: {},
        numberOfRuns: 20,
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .status
    margin-top: 100px
    text-align: center
</style>
