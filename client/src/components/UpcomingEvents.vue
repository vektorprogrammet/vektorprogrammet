<template>
    <div class="events">
        <h3 class="mt-5">Kommende hendelser</h3>
        <div class="card accent-info text-left mt-3 m-lg-5"
             v-for="event in this.events">
            <div class="card-body ">
                <div class="card-text">
                    <div class="card-title">
                        <span class="title">{{event.title}}</span>
                        <span class="title-date"> - {{event.date}}</span>
                    </div>
                    <div class="content">
                        <p>{{event.content}}</p>
                    </div>
                    <span class="place-time mr-3">
                        <span>Sted: </span>
                        <span class="place-time-detail">{{event.location}}</span>
                    </span>
                    <span class="place-time mr-3">
                        <span>Tid: </span>
                        <span class="place-time-detail">{{event.time}}</span>
                    </span>
                    <!-- if link -->
                    <span v-if="event.link" class="link"> <a href="#">Link til arrangement</a></span>


                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import Vue from 'vue';
    import Component from 'vue-class-component';
    import { Prop } from 'vue-property-decorator';
    @Component({
    })
    export default class UpcomingEvents extends Vue {
    private events: object = [];

    public mounted() {
        this.events = [
            {
                'title': 'Assistent',
                'content': 'Du skal på Gimse på Mandag',
                'location' : 'Gløshaugen S4',
                'date': 'mandag 3 apr',
                'time' : '16:00'
            },
            {
                'title': 'Hyttetur',
                'content': 'Du skal på hyttetur med vektorprogrammet til Heinfjordstua. Det er et stykke å gå, så pass på at du har godt utstur. Vi møtes felles for å ta buss sammen utenfor samfundet klokken 15 på fredag. God tur!',
                'location' : 'Heinfjordstua',
                'date': 'fredag 20 mars til søn 22 mars',
                'time' : '15:00',
                'link' : 'Link til facebook arrangement'
            },
        ];


    }
    }
</script>

<style scoped lang="scss">
    .card {
        border-left-color: #0b3e6f;
        border-left-width: 5px;
    }

    .title {
        color: #023874;
        font-size: 25px;
        font-weight: normal;

    }
    .title-date {
        color: #023874;
        font-size: 25px;
        font-weight: lighter;
    }

    .place-time {
        color: #023874;
        font-size: 18px;
        font-weight: normal;
        .place-time-detail {
            font-weight: lighter;
        }
    }

    .link {
        color: #0b93d5;
        font-size: 18px;
    }

</style>
