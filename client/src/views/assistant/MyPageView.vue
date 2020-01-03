<template>
  <div class="content">
    <!--MyPageNav></MyPageNav-->
    <!-- PageHeader class="header-component"><h1>Min side</h1></PageHeader -->
    <ScheduleInfo
            v-if="scheduleInfo.length > 0"
            :scheduleInfo="scheduleInfo"
            class="schedule-component">

    </ScheduleInfo>
    <h1 v-else class="m-5 text-dark-blue fs-16">
      Min side
    </h1>


    <UserInfo v-if="user" :user="user"></UserInfo>

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
        <PartnerInfo :partners="partners" class="partner-component"></PartnerInfo>
        <!--SchoolInfo :scheduleInfo="scheduleInfo" class="school-component"></SchoolInfo -->
      </div>

      <div v-else class="no-partner m-5">
        <h2> Du har ingen partner dette semesteret </h2>
      </div>
    </div>

    <div class="map mt-5" v-for="scheduleInfo in this.scheduleInfo" >
      <h3>Ruteforslag til {{scheduleInfo.school.name}} ungdomsskole</h3>
      <Map class="mb-5" :schoolName="scheduleInfo.school.name" :user="user"/>
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
import {ProvideReactive} from "vue-property-decorator";
import UpcomingEvents from "../../components/UpcomingEvents";
import ContactInfo from "../../components/ContactInfo";
import Map from "../../components/Map";


@Component({
  components: {ContactInfo, UpcomingEvents, PageHeader, MyPageNav, PartnerInfo, ScheduleInfo, SchoolInfo, UserInfo, Map},
})

export default class MyPageView extends Vue {
    @ProvideReactive() private scheduleInfo: Array<object> = [];
    @ProvideReactive() private user: any = null;
    @ProvideReactive() private partners: any = null;

    public async beforeCreate() {
        this.user = await accountService.getUser();
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
