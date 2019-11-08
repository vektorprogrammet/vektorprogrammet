<template>
    <div class="content">
        <div id="map" style="width: 100%; height: 500px;"></div>
        <p>Reisen fra {{this.homeName}} til {{this.schoolName}} Ungdomsskole tar ca {{this.duration}} </p>
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
        private duration: string = ' X min';

        public async mounted() {
            try {
                const google = await mapService.init();
                const geocoder = new google.maps.Geocoder();
                const directionsService = new google.maps.DirectionsService();
                const directionsDisplay = new google.maps.DirectionsRenderer();
                const map = new google.maps.Map(document.getElementById('map'));
                directionsDisplay.setMap(map);


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
                    if (status !== 'OK') {
                        throw new Error(status);
                    } else {
                        directionsDisplay.setDirections(result);
                        this.duration = result.routes[0].legs[0].duration.text
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
