<template>
    <div class="container mt-4 text-left">
        <b-row>
            <b-col class="m-4">
                <div class="card accent-info text-left">
                    <div class="card-header">Min skolekoordinator</div>

                    <b-row class="card-body">
                        <b-col>
                            <ProfilePicture pictureWidth="50%"></ProfilePicture>
                        </b-col>
                        <b-col>
                            <p>We have no skolekoordinator
                            Kanskje best å finne ut av dette?</p>
                        </b-col>
                    </b-row>
                </div>
            </b-col>
            <b-col class="m-4">
                <div class="card accent-info text-left">
                   <div class="card-header">Kontaktperson {{this.school.name}}</div>
                    <div class="card-body">
                    <p>{{this.school.contactPerson}} <br>
                    E-post: {{this.school.email}}<br>
                    Telefon: {{this.school.phone}}</p>
                    </div>
                </div>
            </b-col>
        </b-row>
    </div>
</template>

<script lang="ts">
    import Vue from 'vue';
    import Component from 'vue-class-component';
    import { Prop } from 'vue-property-decorator';
    import ProfilePicture from "./ProfilePicture";
    @Component({
        components: {ProfilePicture}
    })
    export default class SchoolInfo extends Vue {
        @Prop() school: any;
    }
</script>

<style scoped lang="scss">


</style>