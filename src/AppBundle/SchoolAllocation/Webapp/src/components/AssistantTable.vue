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
      <td class="text-xs-right">{{ props.item.double }}</td>
      <td class="text-xs-right">{{ props.item.monday }}</td>
      <td class="text-xs-right">{{ props.item.tuesday }}</td>
      <td class="text-xs-right">{{ props.item.wednesday }}</td>
      <td class="text-xs-right">{{ props.item.thursday }}</td>
      <td class="text-xs-right">{{ props.item.friday }}</td>
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
            text: 'Assistent',
            left: true,
            value: 'name'
          },
          {text: 'Dobbel stilling', value: 'double'},
          {text: 'Mandag', value: 'monday'},
          {text: 'Tirsdag', value: 'tuesday'},
          {text: 'Onsdag', value: 'wednesday'},
          {text: 'Torsdag', value: 'thursday'},
          {text: 'Fredag', value: 'friday'},
        ],
        schools: [
          {
            name: "Test assistent 1",
            double: true,
            monday: true,
            tuesday: false,
            wednesday: false,
            thursday: true,
            friday: false
          },
          {
            name: "Test assistent 2",
            double: false,
            monday: false,
            tuesday: true,
            wednesday: false,
            thursday: true,
            friday: false
          },
          {
            name: "Test assistent 3",
            double: false,
            monday: false,
            tuesday: false,
            wednesday: true,
            thursday: false,
            friday: true
          },
          {
            name: "Test assistent 4",
            double: true,
            monday: false,
            tuesday: false,
            wednesday: false,
            thursday: false,
            friday: true
          },
          {
            name: "Test assistent 5",
            double: false,
            monday: true,
            tuesday: true,
            wednesday: true,
            thursday: true,
            friday: true
          },
          {
            name: "Test assistent 6",
            double: false,
            monday: false,
            tuesday: true,
            wednesday: false,
            thursday: true,
            friday: true
          },
          {
            name: "Test assistent 7",
            double: false,
            monday: true,
            tuesday: true,
            wednesday: false,
            thursday: false,
            friday: false
          },
          {
            name: "Test assistent 8",
            double: false,
            monday: false,
            tuesday: true,
            wednesday: true,
            thursday: false,
            friday: false
          },
        ]
      }
    }
  }
</script>
