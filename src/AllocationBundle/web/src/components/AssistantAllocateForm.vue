<template>
  <div>
    <group-select
      @groupChanged="changeGroup"
      :selectedGroup="group"
      :groups="getAvailableGroups()"></group-select>

    <day-select
      v-if="isSet(group)"
      @dayChanged="changeDay"
      :selectedDay="day"
      :days="getAvailableDays()"></day-select>

    <school-select
      v-if="isSet(group) && isSet(day)"
      @schoolChanged="changeSchool"
      :selectedSchool="school"
      :schools="getAvailableSchools()"></school-select>

    <div class="text-center" v-if="isSet(group) && isSet(day) && isSet(school)">
      <button class="button small">Fordel til skole</button>
    </div>
  </div>
</template>

<script>
  import GroupSelect from './AssistantGroupSelect.vue'
  import DaySelect from './AssistantDaySelect.vue'
  import SchoolSelect from './AssistantSchoolSelect.vue'

  export default {
    props: ['assistant'],
    data () {
      return {
        group: null,
        day: null,
        school: null
      }
    },
    components: {
      'group-select': GroupSelect,
      'day-select': DaySelect,
      'school-select': SchoolSelect
    },
    methods: {
      isSet(property) {
        return property !== null && property.length > 0;
      },
      changeGroup(event) {
        this.group = event;
        if (event === null || event === "") {
          this.day = null;
          this.school = null;
        }
      },
      changeDay(event) {
        this.day = event;
        if (event === null || event === "") {
          this.school = null;
        }
      },
      changeSchool(event) {
        this.school = event
      },
      getAvailableGroups() {
        var availableGroups = [];
        if (this.assistant.doublePosition) {
          availableGroups.push('Begge');
          availableGroups.push('Bolk 1');
          availableGroups.push('Bolk 2');
        } else {
          if (this.assistant.preferredGroup === null) {
            availableGroups.push('Bolk 1');
            availableGroups.push('Bolk 2');
          } else {
            availableGroups.push('Bolk ' + this.assistant.preferredGroup);
          }
        }

        return availableGroups;
      },
      getAvailableDays() {
        const availability = this.assistant.availability;
        return Object.keys(availability).reduce((prev, curr, idx) => {
          if (availability[curr]) {
            return [...prev, idx];
          }
          return prev;
        }, [])
      },
      getAvailableSchools() {
        // TODO: Get actual schools
        return [
          {id: 1, name: 'Åsheim'},
          {id: 2, name: 'Huseby'},
          {id: 3, name: 'Rosenborg'},
          {id: 4, name: 'Markaplassen'}
        ]
      }
    }
  }
</script>
