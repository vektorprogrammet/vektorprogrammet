<template>
      <GmapMap
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
      </GmapMap>

</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import {accountService} from "../services";
import { Prop } from 'vue-property-decorator';
import * as VueGoogleMaps from 'vue2-google-maps'

Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyCfhjte_te7uOqtXmZkvtrhdZaNMaVIGso',
        libraries: 'places',
    },
});

@Component()

export default class Map extends Vue {
  @Prop() private school_name: string;
  @Prop() private school_location: JSON;
  private home_location: JSON;

  public async mounted() {
      this.school_location = await accountService.getLocationByName(this.school_name);
      this.school_location=this.school_location.results[0].geometry.location;
      this.home_location = await accountService.getLocationByName('Gløshaugen');
      this.home_location=this.home_location.results[0].geometry.location;
  }
}
</script>

<style scoped lang="scss">

</style>
