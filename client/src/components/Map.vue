<template>
    <div class="content">
        <div :id="'map_' + schoolName" style="width: 100%; height: 500px;"></div>
        <p>{{ this.helpText}}</p>
    </div>
</template>

<script lang="ts">
    import Vue from 'vue';
    import Component from 'vue-class-component';
    import {mapService} from "../services";
    import {Prop} from 'vue-property-decorator';

    @Component()

    export default class Map extends Vue {
        @Prop() private schoolName: string;
        @Prop() private homeName: string = 'Trondheim Sentrum';
        @Prop() private schoolLocation: JSON = JSON;
        private status: string = "";
        private helpText: string = "Venter på ruteforslag";

        public async mounted() {
            try {
                const google = await mapService.init();
                const geocoder = new google.maps.Geocoder();
                const directionsService = new google.maps.DirectionsService();
                let display: any;
                let map = new google.maps.Map(document.getElementById('map_' + this.schoolName), {
                    zoom: 12,
                    center: new google.maps.LatLng(63.42, 10.39)
                });



                /*geocoder.geocode({ address: this.school_name + ' ungdomsskole'}, (results, status) => {
                    if (status !== 'OK' || !results[0]) {
                        throw new Error(status);
                    }
                    map.setCenter(results[0].geometry.location);
                    map.setZoom(10);

                    this.locations.push({
                        position:
                            {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()}
                    });
                    let marker = new google.maps.Marker({
                        position: {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()},
                        map: map,
                        title: this.school_name + ' ungdomsskole',
                    });
                });

                geocoder.geocode({ address: this.home_name}, (results, status) => {
                    if (status !== 'OK' || !results[0]) {
                        throw new Error(status);
                    }

                    this.locations.push({
                        position:
                            {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()}
                    });
                    let marker = new google.maps.Marker({
                        position: {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()},
                        map: map,
                        title: this.home_name,
                    });
                });

                /*let markers = this.locations
                    .map(x => new google.maps.Marker({ ...x, map }));*/
                const request = {
                    origin: this.homeName,
                    destination: this.schoolName + ' ungdomsskole',
                    travelMode: 'TRANSIT'
                };
                directionsService.route(request, (result, status) => {
                    this.status = status;
                    if (status === 'OK') {
                        display = new google.maps.DirectionsRenderer();
                        display.setMap(map);
                        display.setDirections(result);
                        let duration = result.routes[0].legs[0].duration.text;
                        this.helpText = "Reisen fra " + this.homeName + " til " + this.schoolName + " ungdomsskole tar ca " + duration;
                    } else {
                        this.helpText = "Ingen rute funnet mellom " + this.homeName + " og " + this.schoolName + " Ungdomsskole";
                    }
                });

            } catch (error) {
                console.error(error);
            }
        }
    }
</script>

<style scoped lang="scss">

</style>
