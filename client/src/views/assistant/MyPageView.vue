<template>
  <div class="content">
    <!--MyPageNav></MyPageNav-->
    <!-- PageHeader class="header-component"><h1>Min side</h1></PageHeader -->
    <ScheduleInfo :scheduleInfo="scheduleInfo" class="schedule-component"></ScheduleInfo>
    <UpcomingEvents ></UpcomingEvents>
    <ContactInfo :partner="scheduleInfo.user" :school="this.scheduleInfo.school"></ContactInfo>
    <PartnerInfo :user_me="user" :user_partner="scheduleInfo.user" class="partner-component"></PartnerInfo>
    <SchoolInfo :scheduleInfo="scheduleInfo" class="school-component"></SchoolInfo>
    <MyPartner :user_partner="scheduleInfo.user"></MyPartner>
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


@Component({
  components: {ContactInfo, UpcomingEvents, PageHeader, MyPageNav, PartnerInfo, ScheduleInfo, SchoolInfo, MyPartner},
})
export default class MyPageView extends Vue {
    @Prop() private scheduleInfo: any;
    @Prop() private user: any;
    public async mounted() {
        this.user = await accountService.getUser();
        this.scheduleInfo = await  accountService.getScheduleInfo();
        this.scheduleInfo = (this.scheduleInfo[0]);
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
</style>
