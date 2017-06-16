<template>
  <v-data-table
    v-bind:headers="headers"
    v-bind:items="assistants"
    @input="assistantSelected"
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
      <td class="text-xs-right">{{ props.item.score }}</td>
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
  import {mapGetters} from 'vuex';

  export default {
    computed: mapGetters(['assistants']),
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
          {text: 'Score', value: 'score'},
          {text: 'Dobbel stilling', value: 'double'},
          {text: 'Mandag', value: 'monday'},
          {text: 'Tirsdag', value: 'tuesday'},
          {text: 'Onsdag', value: 'wednesday'},
          {text: 'Torsdag', value: 'thursday'},
          {text: 'Fredag', value: 'friday'},
        ]
      }
    }
  }
</script>
