<template>
  <div class="content">
    <!--MyPageNav></MyPageNav-->
    <PageHeader class="header-component"><h1>Min side</h1></PageHeader>
    <ScheduleInfo class="schedule-component"></ScheduleInfo>
    <PartnerInfo class="partner-component"></PartnerInfo>
  </div>
</template>

<script lang="ts">
import PageHeader from '../../components/PageHeader.vue';
import MyPageNav from '../../components/MyPageNav.vue';
import PartnerInfo from '../../components/PartnerInfo.vue';
import ScheduleInfo from '../../components/ScheduleInfo.vue';
import Vue from 'vue';
import Component from 'vue-class-component';
@Component({
  components: { PageHeader, MyPageNav, PartnerInfo, ScheduleInfo },
})
export default class MyPageView extends Vue {}
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
