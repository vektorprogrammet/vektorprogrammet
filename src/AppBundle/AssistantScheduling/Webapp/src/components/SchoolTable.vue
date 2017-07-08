<template>
  <v-data-table
      v-bind:headers="headers"
      v-bind:items="schools"
      @input="toggleSelected()"
      v-model="selected"
      selected-key="name"
      select-all
      hide-actions
      class="elevation-1"
  >
    <template slot="items" scope="props">
      <td>
        <v-checkbox
            primary
            hide-details
            v-model="props.item.selected"
        ></v-checkbox>
      </td>
      <td>{{ props.item.name }}</td>
      <td class="text-xs-right">{{ props.item.monday }}</td>
      <td class="text-xs-right">{{ props.item.tuesday }}</td>
      <td class="text-xs-right">{{ props.item.wednesday }}</td>
      <td class="text-xs-right">{{ props.item.thursday }}</td>
      <td class="text-xs-right">{{ props.item.friday }}</td>
      <td class="text-xs-right"><a :href="'/kontrollpanel/skole/capacity/' + props.item.id"><v-icon dark>edit</v-icon></a></td>
    </template>
  </v-data-table>
</template>

<script>
  import {mapGetters} from 'vuex';

  export default {
    methods: {
      toggleSelected: function() {
        if (this.selected.length > 0) {
          this.selected.forEach(school => school.selected = true);
        } else {
          this.schools.forEach(school => school.selected = false);
        }
      }
    },
    computed: mapGetters(['schools', 'selectedSchools']),
    data () {
      return {
        selected: [],
        headers: [
          {
            text: 'Skole',
            left: true,
            value: 'name'
          },
          {text: 'Mandag', value: 'monday'},
          {text: 'Tirsdag', value: 'tuesday'},
          {text: 'Onsdag', value: 'wednesday'},
          {text: 'Torsdag', value: 'thursday'},
          {text: 'Fredag', value: 'friday'},
          {text: '', value: ''},
        ]
      }
    }
  }
</script>

<style scoped>
  a {
    text-decoration: none;
  }
</style>
