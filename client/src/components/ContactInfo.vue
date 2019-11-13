<template>
    <div class="contact-info">
        <h3 class="mt-5 mb-5">Kontaktinfo</h3>
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
            <!--b-col>
                <div class="card accent-info text-left  ml-3 mr-3">
                    <div class="card-body">
                        <div class="card-text">
                            <h3 class="card-title text-dark-blue">Din vektorpartner</h3>
                            <b-row>
                                <b-col cols="5">
                                    <ProfilePicture :picturePath="partner.picture_path" :pictureWidth="'100%'"/>
                                </b-col>
                                <b-col>
                                    <div class="content text-md-left">
                                        <p class="text-dark-blue text-bold">{{partner.fullName}}</p>
                                        <p class="link"><a :href="'mailto:' + partner.email">{{partner.email}}</a></p>
                                        <p>
                                            <a class="text-default" :href="'tel: ' + partner.phone">
                                                <i class="fa fa-phone"></i> {{ partner.phone }}
                                            </a>
                                        </p>
                                    </div>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                </div>
            </b-col-->
            <b-col>
                <div class="card accent-info text-left ml-3 mr-5">
                    <div class="card-body">
                        <div class="card-text">
                            <h3 class="card-title text-dark-blue">Kontakt {{school.name}}</h3>
                            <div class="content">
                                <p>{{school.contactPerson}}</p>
                                <p class="m-0">E-post:   <a :href="'mailto:' + school.email">{{school.email}}</a></p>
                                <p class="m-0">Tlf:   <a :href="'tel:' + school.phone">{{school.phone}}</a></p>
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
    //import Component from 'vue-class-component';
    import { Prop } from 'vue-property-decorator';
    /* import ProfilePicture from "./ProfilePicture";
    @Component({
        components: {ProfilePicture}
    })*/
    export default class ContactInfo extends Vue {
        // @Prop() private coordinator: any;
        //@Prop() private partner: any;
        @Prop() private school: object = Object;
        //contact_info_pictureWidth: string = '20%';
    }
</script>

<style scoped lang="scss">
    .card {
        height: 100%;
    }
</style>
