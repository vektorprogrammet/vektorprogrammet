<template>
    <div class="container mt-4 text-left">
        <b-row>
            <b-col>
                <h4>Min skolekoordinator</h4>
                <b-row>
                    <b-col>
                        <ProfilePicture picture_path="https://via.placeholder.com/150" picture_width="50%"></ProfilePicture>
                    </b-col>
                    <b-col>
                        We have no skolekoordinator
                        Kanskje best å finne ut av dette?
                    </b-col>
                </b-row>
            </b-col>
            <b-col>
               <h4>Kontaktperson {{scheduleInfo.school.name}}</h4>
                <p>{{scheduleInfo.school.contactPerson}}</p>
                <p>E-post: {{scheduleInfo.school.email}}</p>
                <p>Telefon: {{scheduleInfo.school.phone}}</p>
                <p></p>
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
        @Prop() scheduleInfo: any;
    }
</script>

<style scoped lang="scss">


</style>