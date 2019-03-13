<template>
  <div class="container">
    <div class="background"></div>
    <div class="content">
      <h2 class="pt-5 pb-5">Min Assistentstilling:</h2>
      <b-row>
        <b-col>
          <font-awesome-icon
            icon="map-marker-alt"
            id="marker"
            style="font-size: 5em; color: #FFFFFF;"
            class="mb-4"
          />
          <h4 class="label">{{scheduleInfo.school.name}}</h4>
          <!--h4>Ungdomsskole</h4-->
        </b-col>
        <b-col>
          <font-awesome-icon
                  icon="clock"
                  id="clock"
                  style="font-size: 5em; color: #FFFFFF;"
                  class="mb-4"
          />
          <h4 class="label">{{scheduleInfo.day}}</h4>
        </b-col>
        <b-col>
          <font-awesome-icon
            icon="calendar-alt"
            id="calendar"
            style="font-size: 5em; color: #FFFFFF;"
            class="mb-4"
          />
          <h4 class="label">{{scheduleInfo.bolk}}</h4>
        </b-col>
      </b-row>
    </div>
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

#marker #clock #calendar {
  color: white;
}

.row {
  size: auto;
  text-align: right;
}

.container {
  width: 100%;
  height: 20%;
}

.background {
  background: url("../../public/img/schools/gimse.jpg") center no-repeat;
  background-size: cover;
  filter: blur(5px);
  position: absolute;
  z-index: 0;
  width: 100%;
  height: 50%;
  top: 60px;
  left: 0;

}

.content {
  //background-image: url("../../public/img/schools/gimse.jpg");
  //background-color: lightgrey;
  padding: 0 0 100px 0;
  align-items: center;
  background-color: rgba(0,0,0,0);
  //position: absolute;
  top: 0;
  left: 0;
  /*-webkit-filter: blur(5px);
  -ms-filter: blur(5px);*/

}

.label {
  color: white;
}

</style>
