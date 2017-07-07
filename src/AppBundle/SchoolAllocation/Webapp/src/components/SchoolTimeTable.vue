<template>
  <div>
    <h4>{{name}}</h4>
    <table>
      <thead>
      <tr>
        <th></th>
        <th v-for="day in weekDays">{{day}}</th>
      </tr>
      </thead>
      <tbody>

      <tr v-for="(i, index) in group1Max">
        <td class="group-header"><span v-if="index === 0">Gruppe 1</span></td>
        <td v-for="day in weekDays"><span
            v-if="group1.hasOwnProperty(day) && group1[day].length >= index+1">{{group1[day][index].name}} - {{group1[day][index].score}}</span>
        </td>
      </tr>

      <tr>
        <td colspan="6">&nbsp;</td>
      </tr>

      <tr v-for="(i, index) in group2Max">
        <td class="group-header"><span v-if="index === 0">Gruppe 2</span></td>
        <td v-for="day in weekDays"><span
            v-if="group2.hasOwnProperty(day) && group2[day].length >= index+1">{{group2[day][index].name}} - {{group2[day][index].score}}</span>
        </td>
      </tr>
      </tbody>

    </table>
  </div>
</template>

<script>

  export default {
    props: ['school', 'name'],
    data() {
      return {
        weekDays: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']
      }
    },
    created() {
      this.group1 = this.school[1];
      this.group2 = this.school[2];
    },
    computed: {
      group1Max: function () {
        return new Array(this.weekDays.reduce((acc, day) => {
          if (!this.group1.hasOwnProperty(day)) {
            return acc;
          }
          const assOnDay = this.group1[day].length;

          if (assOnDay > acc) {
            return assOnDay;
          }

          return acc;
        }, 0));
      },
      group2Max: function () {
        return new Array(this.weekDays.reduce((acc, day) => {
          if (!this.group2.hasOwnProperty(day)) {
            return acc;
          }
          const assOnDay = this.group2[day].length;

          if (assOnDay > acc) {
            return assOnDay;
          }

          return acc;
        }, 0));
      }
    }
  }
</script>

<style scoped>
  h4 {
    margin-top: 100px;
    font-size: 24px;
  }

  table {
    table-layout: fixed;
    width: 100%;
    padding-bottom: 50px;
  }

  .group-header {
    font-weight: bold;
    text-decoration: underline;
  }
</style>
