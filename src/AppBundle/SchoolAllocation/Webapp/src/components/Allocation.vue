<template>
  <v-layout row wrap>

    <v-flex xs12 sm6 v-show="!allocating && schools.length > 0 && !bestSchedule.hasOwnProperty('monday')">
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

    <v-flex xs12 sm6 v-show="!allocating && assistants.length > 0 && !bestSchedule.hasOwnProperty('monday')">
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

    <v-flex xs12 v-show="!allocating && bestSchedule.hasOwnProperty('monday')">
      <schedule :scheduleData="bestSchedule"></schedule>
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
  import {SAOptimize, scheduleGreedily} from "../Schedulers";

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
            this.bestSchedule = result;
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
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .status
    margin-top: 100px
    text-align: center
</style>
