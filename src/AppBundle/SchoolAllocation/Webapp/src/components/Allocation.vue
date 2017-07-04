<template>
  <v-layout row wrap>

    <v-flex xs12 sm6 offset-sm3 v-show="!allocating && !bestSchedule.hasOwnProperty('monday')">
      <h4>{{schools.length}} skoler valgt. {{assistants.length}} assistenter valgt.</h4>
    </v-flex>

    <v-flex xs12 v-if="!allocating && Object.keys(timeTable).length > 0">
      <time-table :timeTable="timeTable"></time-table>
    </v-flex>

    <v-flex xs12 class="status">

      <v-flex v-show="!allocating" xs12 sm6 offset-sm3>
        <label> Maximum number of runs</label>
        <v-slider v-model="numberOfRuns" dark thumb-label step="2"></v-slider>
        <p>{{numberOfRuns}}</p>
      </v-flex>

      <br>
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
        >{{ progress }} %
        </v-progress-circular>
        <br>
        <br>
        <div v-if="bestSchedule.hasOwnProperty('monday')">
          <p>Beste fordeling:</p>
          <p>{{bestSchedule.scheduledAssistantsCount()}} / {{bestSchedule.totalCapacity()}}</p>
        </div>
      </div>
    </v-flex>

  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex";
  import {Schedule} from "../Schedule";
  import {SAOptimize, scheduleGreedily, optimizeScore, mapScheduledAssistantsToSchools} from "../Schedulers";

  export default {
    methods: {
      schedule (i) {
        let schedule = new Schedule(this.schools, this.assistants);
        schedule = scheduleGreedily(schedule);

        SAOptimize(schedule, result => {
          const calculatedProgress = Math.round((i + 1) / this.numberOfRuns * 100);
          this.progress = Math.min(100, calculatedProgress);
          if (result.isOptimal()) {
            this.progress = 100;
            this.bestSchedule = optimizeScore(result);
            if (!this.bestSchedule.isValid()) {
              console.error('Invalid schedule', this.bestSchedule)
            }
            this.timeTable = mapScheduledAssistantsToSchools(this.schools, this.bestSchedule);
            setTimeout(() => {
              this.allocating = false;
            }, 750)

            return;
          }

          if (!this.bestSchedule.hasOwnProperty('monday') || result.fitness() > this.bestSchedule.fitness()) {
            this.bestSchedule = result;
          }

          if (i < this.numberOfRuns) {
            this.schedule(i + 1);
          } else {
            this.bestSchedule = optimizeScore(this.bestSchedule);
            if (!this.bestSchedule.isValid()) {
              console.error('Invalid schedule', this.bestSchedule)
            }
            this.timeTable = mapScheduledAssistantsToSchools(this.schools, this.bestSchedule);

            this.allocating = false;
          }

        })
      },
      allocate () {
        this.allocating = true;
        this.schedule(0);
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
        bestSchedule: {},
        numberOfRuns: 50,
        timeTable: {}
      }
    }
  }
</script>

<style lang="stylus" scoped>
  h4
    text-align: center;
  .status
    margin-top: 100px
    text-align: center
</style>
