<template>
    <div class="events">
        <h3>Kommende hendelser</h3>
        <div class="card accent-info text-left mt-3 m-lg-5"
             v-for="event in this.events">
            <div class="card-body ">
                <div class="card-text">
                    <h3 class="card-title text-dark-blue">{{event.title}}</h3>
                    <div class="content">
                        <p>{{event.content}}</p>
                    </div>
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
            'content': 'Du skal på Gimse på Mandag'
            },
            {
                'title': 'Hyttetur',
                'content': 'Du skal på hyttetur i morgen'
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
</style>
