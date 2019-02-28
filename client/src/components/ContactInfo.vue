<template>
    <div class="contact-info">
        <h3>Kontaktinfo</h3>
        <b-row class="">
            <b-col>
                <div class="card accent-info text-left m-lg-5">
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
                <div class="card accent-info text-left  m-lg-5">
                    <div class="card-body">
                        <div class="card-text">
                            <h3 class="card-title text-dark-blue">Din vektorpartner</h3>
                            <b-row>
                                <b-col class="w-25">
                                    <ProfilePicture :picture_path="partner.picture_path" :picture_width="picture_width"/>
                                </b-col>
                                <b-col>
                                    <div class="content">
                                        <p>{{partner.fullName}} <br>{{partner.email}}</p>
                                    </div>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                </div>
            </b-col>
            <b-col>
                <div class="card accent-info text-left  m-lg-5">
                    <div class="card-body">
                        <div class="card-text">
                            <h3 class="card-title text-dark-blue">Kontakt {{school.name}}</h3>
                            <div class="content">
                                <p>{{school.contactPerson}}<br>
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
        private picture_width: string = '50%';
    }
</script>

<style scoped lang="scss">
</style>
