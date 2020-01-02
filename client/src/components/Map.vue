<template>
    <div class="content">
        <div class="map_section" :id="'map_' + schoolName"></div>
        <p>{{ this.helpText}}</p>
    </div>
</template>

<script lang="ts">
    import {mapService} from "../services";
    import {Vue, Component, Prop} from 'vue-property-decorator';

    @Component
    export default class Map extends Vue {
        @Prop() user!: any;
        private startLocation!: string;
        private helpText: string = "Venter på ruteforslag";
        @Prop() private schoolName!: string;

        public beforeMount() {
            if (this.user.address) {
                this.startLocation = this.user.address.address + ', ' + this.user.address.city;
            } else {
                this.startLocation = this.user.department.city + " Sentrum";
            }

            console.log('1')
            console.log(this.schoolName)
            console.log(this.startLocation)
        }
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

                const request = {
                    origin: this.startLocation,
                    destination: this.schoolName + ' ungdomsskole',
                    travelMode: 'TRANSIT'
                };
                directionsService.route(request, (result: JSON, status: string) => {
                    if (status === 'OK') {
                        display = new google.maps.DirectionsRenderer();
                        display.setMap(map);
                        display.setDirections(result);
                        let duration = result.routes[0].legs[0].duration.text;
                        this.helpText = "Reisen fra " + this.startLocation + " til " + this.schoolName + " ungdomsskole tar ca " + duration;
                    } else {
                        this.helpText = "Ingen rute funnet mellom " + this.startLocation + " og " + this.schoolName + " Ungdomsskole";
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
