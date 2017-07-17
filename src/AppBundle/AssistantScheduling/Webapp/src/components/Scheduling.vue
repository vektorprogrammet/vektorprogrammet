<template>
  <v-layout row wrap>

    <v-flex xs12 sm6 offset-sm3 v-show="!allocating && !bestSchedule.hasOwnProperty('monday')">
      <h5>{{schools.length}} skoler valgt</h5>
      <h5>{{assistants.length}} assistenter valgt</h5>
    </v-flex>

    <v-flex xs12 v-if="!allocating && Object.keys(timeTable).length > 0">
      <h2>Timeplan</h2>
      <v-btn @click.native="downloadCSV" primary light>Last ned CSV<v-icon right light>file_download</v-icon></v-btn>
      <time-table :timeTable="timeTable"></time-table>
      <br>
      <queue :queue="bestSchedule.queuedAssistants"></queue>
    </v-flex>

    <v-flex xs12 class="status">

      <!--<v-flex v-show="!allocating" xs12 sm6 offset-sm3>
        <label> Maximum number of runs</label>
        <v-slider v-model="numberOfRuns" dark thumb-label step="2"></v-slider>
        <p>{{numberOfRuns}}</p>
      </v-flex>-->

      <br>
      <v-btn v-show="!allocating" @click.native="allocate" primary light>Generer timeplan</v-btn>
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
  import ScheduleQueue from './ScheduleQueue.vue';
  import {Schedule} from "../Schedule";
  import {SAOptimize, scheduleGreedily} from "../Schedulers";

  export default {
    components: {
      'queue': ScheduleQueue
    },
    methods: {
      allocate () {
        this.progress = 0;
        this.allocating = true;
        this.schedule(0);
      },
      schedule (i) {
        let schedule = new Schedule(this.schools, this.assistants);
        schedule = scheduleGreedily(schedule);

        SAOptimize(schedule, result => {
          if (result.isOptimal()) {
            this.progress = 100;

            this.updateSchedule(result);

            return;
          }

          const calculatedProgress = Math.round((i + 1) / this.numberOfRuns * 100);
          this.progress = Math.min(100, calculatedProgress);

          if (!this.bestSchedule.hasOwnProperty('monday') || result.fitness() > this.bestSchedule.fitness()) {
            this.bestSchedule = result;
          }

          if (i < this.numberOfRuns) {
            this.schedule(i + 1);
          } else {
            this.updateSchedule(this.bestSchedule);
          }

        })
      },
      updateSchedule(schedule) {
        schedule.optimizeScore();

        if (!schedule.isValid()) {
          console.error('Invalid schedule', schedule)
        }

        this.bestSchedule = schedule;
        this.timeTable = schedule.generateTimeTable();

        setTimeout(() => {
          this.allocating = false;
        }, 750)
      },
      downloadCSV() {
        const csv = this.bestSchedule.toCSV();
        const encodedUri = encodeURI(csv);
        const link = document.createElement("a");
        link.style.visibility = 'hidden';
        link.setAttribute("href", "data:text/csv;charset=utf-8,\uFEFF" + encodedUri);
        link.setAttribute("download", "timeplan.csv");
        document.body.appendChild(link);

        link.click();
      },
      goBack() {
        this.$emit('goBack');
      },
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
        numberOfRuns: 100,
        timeTable: {}
      }
    }
  }
</script>

<style lang="stylus" scoped>
  h5
    text-align: center;
  .status
    margin-top: 30px
    text-align: center
</style>
