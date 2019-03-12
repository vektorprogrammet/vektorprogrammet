<template>
      <!--GmapMap
              :center="{lat:home_location.lat, lng:home_location.lng}"
              :zoom="10"
              map-type-id="roadmap"
              style="width: 100%; height: 500px"
      >
            <GmapMarker
                    key=1
                    :position= "school_location"
                    :clickable="true"
                    :draggable="true"
                    @click="center=position"
            />
            <GmapMarker
                    key=2
                    :position= "home_location"
                    :clickable="true"
                    :draggable="true"
                    @click="center=position"
            />
      </GmapMap-->
      <div id="map" style="width: 100%; height: 500px;">

      </div>

</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import {accountService} from "../services";
import {mapService} from "../services";
import { Prop } from 'vue-property-decorator';
import * as VueGoogleMaps from 'vue2-google-maps';

/*Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyCfhjte_te7uOqtXmZkvtrhdZaNMaVIGso',
        libraries: 'places',
    },
});*/

@Component()

export default class Map extends Vue {
  @Prop() private school_name: string;
  @Prop() private home_name: string;
  @Prop() private school_location: JSON;
  private home_location: any = {lat:63, lng:10};
  private route: JSON;

  public async mounted() {
      /*this.school_location = await mapService.getLocationByName(this.school_name + ' ungdomsskole');
      this.school_location=this.school_location.results[0].geometry.location;
      this.home_location = await mapService.getLocationByName(this.home_name);
      this.home_location=this.home_location.results[0].geometry.location;
      this.route = await accountService.getRouteBetweenAddresses(this.home_name, this.school_name + ' ungdomsskole');*/
      try {
          const google = await mapService.init();
          const geocoder = new google.maps.Geocoder();
          //const map = new google.maps.Map(document.getElementById('Map'));
          console.log(document.getElementById('map'));
          let map = new google.maps.Map(document.getElementById('map'));
          console.log(map);
          geocoder.geocode({ address: 'NTNU' }, (results, status) => {
              if (status !== 'OK' || !results[0]) {
                  throw new Error(status);
              }

              map.setCenter(results[0].geometry.location);
              map.fitBounds(results[0].geometry.viewport);
          });
      } catch (error) {
          console.error(error);
      }
  }
}
</script>

<style scoped lang="scss">

</style>
