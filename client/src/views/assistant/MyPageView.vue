<template>
  <div class="content">
    <!--MyPageNav></MyPageNav-->
    <!-- PageHeader class="header-component"><h1>Min side</h1></PageHeader -->
    <ScheduleInfo
            :scheduleInfo="scheduleInfo"
            class="schedule-component">
    </ScheduleInfo>
    <UpcomingEvents ></UpcomingEvents>

    <div class="requires-partner" v-if="this.scheduleInfo !== ''">
      <ContactInfo :partner="scheduleInfo.user" :school="this.scheduleInfo.school"></ContactInfo>
      <PartnerInfo :user_me="user" :user_partner="scheduleInfo.user" class="partner-component"></PartnerInfo>
      <SchoolInfo :scheduleInfo="scheduleInfo" class="school-component"></SchoolInfo>
      <MyPartner :user_partner="scheduleInfo.user"></MyPartner>
    </div>

    <div v-else class="no-partner m-5">
      <h2> Du har ingen partner :( </h2>
    </div>
    <Map class="mb-5" school_name="Gimse" :home_name="this.home_name"></Map>
  </div>
</template>

<script lang="ts">
import PageHeader from '../../components/PageHeader.vue';
import MyPageNav from '../../components/MyPageNav.vue';
import PartnerInfo from '../../components/PartnerInfo.vue';
import ScheduleInfo from '../../components/ScheduleInfo.vue';
import SchoolInfo from '../../components/SchoolInfo.vue';
import MyPartner from '../../components/MyPartner.vue';
import Vue from 'vue';
import Component from 'vue-class-component';
import {accountService} from "../../services";
import {Prop} from "vue-property-decorator";
import UpcomingEvents from "../../components/UpcomingEvents";
import ContactInfo from "../../components/ContactInfo";
import Map from "../../components/Map";


@Component({
  components: {ContactInfo, UpcomingEvents, PageHeader, MyPageNav, PartnerInfo, ScheduleInfo, SchoolInfo, MyPartner, Map},
})
export default class MyPageView extends Vue {
    @Prop() private scheduleInfo: any = '';
    @Prop() private user: any;
    @Prop() private home_name: string;
    public async mounted() {
        this.user = await accountService.getUser();
        /*if (this.user.address) {
            this.home_name = this.user.address.address + ', ' + this.user.address.city;
        }*/
        console.log(this.user);

        let scheduleInfoResult = await accountService.getScheduleInfo();
        if (scheduleInfoResult.length > 1){
            this.scheduleInfo = scheduleInfoResult[0]
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

.no-partner {

}

</style>
