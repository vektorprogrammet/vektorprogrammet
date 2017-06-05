<template>
  <div>
    <v-layout row wrap>

        <v-flex xs12 sm6 v-show="schools.length > 0">
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

        <v-flex xs12 sm6 v-show="assistants.length > 0">
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

        <v-flex xs12 class="status">
          <v-btn v-show="!allocating" @click.native="allocate" primary light>Fordel</v-btn>
          <div class="text-center" v-show="allocating">Fordeler... <span v-show="showMessage">(Det skjer faktisk ingenting, jeg bare later som)</span>
          </div>
          <v-progress-linear v-show="allocating" v-bind:indeterminate="true"></v-progress-linear>
        </v-flex>

    </v-layout>
  </div>
</template>

<script>
  import {mapGetters} from "vuex";
  export default {
    methods: {
      allocate: function () {
        console.log(this.$store.state);
        this.allocating = true;
        setInterval(() => {
          this.showMessage = true
        }, 5000);
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
        showMessage: false
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .status
    margin-top: 100px
    text-align: center
</style>
