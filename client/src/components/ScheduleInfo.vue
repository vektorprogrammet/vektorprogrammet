<template>
  <div class="content" style="padding: 20px 0 100px 0; align-items: center">
    <h2>Din Assistentstilling:</h2>
    <br><br><br>
    <b-row>
      <b-col>
        <font-awesome-icon
          icon="map-marker-alt"
          id="marker"
          style="font-size: 5em"
        />
        <h4>{{scheduleInfo.school.name}}</h4>
        <!--h4>Ungdomsskole</h4-->
      </b-col>
      <b-col>
        <font-awesome-icon icon="clock" id="clock" style="font-size: 5em" />
        <h4>{{scheduleInfo.day}}</h4>
      </b-col>
      <b-col>
        <font-awesome-icon
          icon="calendar-alt"
          id="calendar"
          style="font-size: 5em"
        />
        <h4>{{scheduleInfo.bolk}}</h4>
      </b-col>
    </b-row>
  </div>
</template>

<script lang="ts">
import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faMapMarkerAlt,
  faClock,
  faCalendarAlt,
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import Vue from 'vue';
import Component from 'vue-class-component';
import { Prop, Watch } from 'vue-property-decorator';

library.add(faMapMarkerAlt, faClock, faCalendarAlt);
Vue.component('font-awesome-icon', FontAwesomeIcon);

@Component
export default class ScheduleInfo extends Vue {
  @Prop() scheduleInfo: any;
}
</script>

<style scoped lang="scss">
.profile-photo {
  max-width: 50%;
  height: auto;
  border-radius: 50em;
}

//fix asap: find id / class to set style for fa-icons
#clock {
  font-size: 5em;
}

.row {
  size: auto;
  text-align: right;
  background-color: #0b2e13;
}

</style>
