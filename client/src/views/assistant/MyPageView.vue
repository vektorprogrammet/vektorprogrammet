<template>
  <div class="content">
    <!--MyPageNav></MyPageNav-->
    <!-- PageHeader class="header-component"><h1>Min side</h1></PageHeader -->
    <ScheduleInfo
            :v-if="scheduleInfo.length > 0"
            :scheduleInfo="scheduleInfo"
            class="schedule-component">

    </ScheduleInfo>

    {{ this.scheduleInfo.length }}
    {{ this.scheduleInfo }}


    <UserInfo :user="user" :v-if="user"></UserInfo>

    <UpcomingEvents class="m-2" ></UpcomingEvents>


    <h3 v-if="scheduleInfo[0]">Kontaktinfo</h3>
    <div class="contact-info-container">
      <div v-for="scheduleInfo in this.scheduleInfo" class="contact-info">
        <h4>{{scheduleInfo.school.name}} ungdomsskole:</h4>
        <SchoolInfo :school="scheduleInfo.school"/>
      </div>
    </div>

    <div class="partner-info" v-if="scheduleInfo.length > 0">
      <div class="requires-partner" v-if="this.partners">
        <PartnerInfo :me="user" :partners="partners" class="partner-component"></PartnerInfo>
        <!--SchoolInfo :scheduleInfo="scheduleInfo" class="school-component"></SchoolInfo -->
      </div>

      <div v-else class="no-partner m-5">
        <h2> Du har ingen partner dette semesteret </h2>
      </div>
    </div>

    <div class="map" v-for="scheduleInfo in this.scheduleInfo" >
      <h3>Ruteforslag til {{scheduleInfo.school.name}} ungdomsskole</h3>
      <Map class="mb-5" :schoolName="scheduleInfo.school.name" :homeName="homeName"/>
      {{ homeName }}
    </div>

  </div>
</template>

<script lang="ts">
import PageHeader from '../../components/PageHeader.vue';
import MyPageNav from '../../components/MyPageNav.vue';
import PartnerInfo from '../../components/PartnerInfo.vue';
import ScheduleInfo from '../../components/ScheduleInfo.vue';
import SchoolInfo from '../../components/SchoolInfo.vue';
import UserInfo from '../../components/UserInfo.vue';
import Vue from 'vue';
import Component from 'vue-class-component';
import {accountService} from "@/services";
import {Prop} from "vue-property-decorator";
import UpcomingEvents from "../../components/UpcomingEvents";
import ContactInfo from "../../components/ContactInfo";
import Map from "../../components/Map";


@Component({
  components: {ContactInfo, UpcomingEvents, PageHeader, MyPageNav, PartnerInfo, ScheduleInfo, SchoolInfo, UserInfo, Map},
})

export default class MyPageView extends Vue {
    @Prop() private scheduleInfo: Array<object> = [];
    @Prop() private user: any;
    @Prop() private homeName!: string;
    @Prop() private partners: any;

    public async beforeCreate() {
        this.user = await accountService.getUser();
        if (this.user.address) {
            this.homeName = this.user.address.address + ', ' + this.user.address.city;
        } else {
          this.homeName = this.user.department.city + " Sentrum";
        }
        this.scheduleInfo = await accountService.getScheduleInfo();
        let partnerInfoResult = await accountService.getPartnerInfo();
        if (partnerInfoResult.length > 0) {
          this.partners = Object;
          for (let i = 0; i < partnerInfoResult.length; i++) {
            if (!this.partners[partnerInfoResult[i].bolk]) {
              this.partners[partnerInfoResult[i].bolk] = [];
            }
            this.partners[partnerInfoResult[i].bolk].push(partnerInfoResult[i].user);
          }
        }
    }

}
</script>

<style scoped lang="scss">
.partner-component {
  width: 100%;
  text-align: center;
  background-color: #0b58a2;
}

.content {
  text-align: center;
  align-items: center;
  width: 100%;
}

.header-component  {
  background-color: #0b58a2;
}

.partner-component {
  background-color: #00a6c7;
}

.contact-info-container{
  .contact-info{
    margin: 40px 0 40px 0;
  }

  margin-bottom: 50px;

}

</style>
