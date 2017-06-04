<template>
  <v-data-table
    v-bind:headers="headers"
    v-bind:items="schools"
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
          v-model="props.selected"
        ></v-checkbox>
      </td>
      <td>{{ props.item.name }}</td>
      <td  class="text-xs-right">{{ props.item.monday }}</td>
      <td  class="text-xs-right">{{ props.item.tuesday }}</td>
      <td  class="text-xs-right">{{ props.item.wednesday }}</td>
      <td  class="text-xs-right">{{ props.item.thursday }}</td>
      <td  class="text-xs-right">{{ props.item.friday }}</td>
    </template>
  </v-data-table>
</template>

<script>
  export default {
    data () {
      return {
        search: '',
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
        ],
        schools: [
          {
            name: "Test skole 1",
            monday: 2,
            tuesday: 0,
            wednesday: 0,
            thursday: 2,
            friday: 0
          },
          {
            name: "Test skole 2",
            monday: 0,
            tuesday: 0,
            wednesday: 0,
            thursday: 2,
            friday: 0
          },
          {
            name: "Test skole 3",
            monday: 0,
            tuesday: 2,
            wednesday: 2,
            thursday: 0,
            friday: 2
          },
          {
            name: "Test skole 4",
            monday: 2,
            tuesday: 0,
            wednesday: 2,
            thursday: 0,
            friday: 0
          },
          {
            name: "Test skole 5",
            monday: 0,
            tuesday: 2,
            wednesday: 0,
            thursday: 0,
            friday: 0
          },
        ]
      }
    }
  }
</script>
