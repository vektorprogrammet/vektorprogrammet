<template>
  <div class="content" style="padding: 20px 0 150px 0">
    <h2>Din Assistentstilling:</h2>
    <b-row>
      <b-col>
        <font-awesome-icon
          icon="map-marker-alt"
          id="marker"
          style="font-size: 5em"
        />
        <h4>Gimse</h4>
        <h4>Ungdomsskole</h4>
      </b-col>
      <b-col>
        <font-awesome-icon icon="clock" id="clock" style="font-size: 5em" />
        <h4>Mandager</h4>
        <h4>12:00-14:00</h4>
      </b-col>
      <b-col>
        <font-awesome-icon
          icon="calendar-alt"
          id="calendar"
          style="font-size: 5em"
        />
        <h4>Uke 33-37</h4>
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
import { accountService } from '../services';
import Vue from 'vue';
import Component from 'vue-class-component';
import { accountService } from '../services';

library.add(faMapMarkerAlt, faClock, faCalendarAlt);
Vue.component('font-awesome-icon', FontAwesomeIcon);

@Component
export default class ScheduleInfo extends Vue {
  public myProfilePicture: string = '';
  public SchoolName: string = '';
  public Weekdays: string = '';
  public Weeknumbers: string = '';
  public mounted() {
    accountService.getSheduleInfo().then((result) => {
      console.log(result);
      console.log('sup')
    });
  }
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
</style>
