<template>
    <div class="contact-info">
        <h3>Kontaktinfo</h3>
        <b-row class="mt-4 mb-4">
            <b-col>
                <div class="card accent-info text-left ml-5 mr-3">
                    <div class="card-body">
                        <div class="card-text">
                            <h3 class="card-title text-dark-blue">Din skolekoordinator</h3>
                            <div class="content">
                                <p>Umulig å si for øyeblikket</p>
                            </div>
                        </div>
                    </div>
                </div>
            </b-col>
            <b-col>
                <div class="card accent-info text-left  ml-3 mr-3">
                    <div class="card-body">
                        <div class="card-text">
                            <h3 class="card-title text-dark-blue">Din vektorpartner</h3>
                            <b-row>
                                <b-col cols="5">
                                    <ProfilePicture :picture_path="full_path" :picture_width="'100%'"/>
                                </b-col>
                                <b-col>
                                    <div class="content text-md-left">
                                        <p class="text-dark-blue text-bold">{{partner.fullName}}</p>
                                        <p class="text-info">{{partner.email}}</p>
                                    </div>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                </div>
            </b-col>
            <b-col>
                <div class="card accent-info text-left ml-3 mr-5">
                    <div class="card-body">
                        <div class="card-text">
                            <h3 class="card-title text-dark-blue">Kontakt {{school.name}}</h3>
                            <div class="content">
                                <p class="text-info">{{school.contactPerson}}<br>
                                    {{school.email}}<br>
                                    {{school.phone}}
                                </p>
                            </div>
                        </div>
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
    export default class ContactInfo extends Vue {
        @Prop() private coordinator: any;
        @Prop() private partner: any;
        @Prop() private school: object;
        private full_path: string = 'http://localhost:8000/' +this.partner.picture_path;
        contact_info_picture_width: string = '20%';
    }
</script>

<style scoped lang="scss">
    .card {
        height: 100%;
    }
</style>
