<template>
  <div class="content">
    <MyPageNav></MyPageNav>
    <PageHeader><h1>Min side</h1></PageHeader>
    <PartnerInfo class="partner-component"></PartnerInfo>
  </div>
</template>

<script lang="ts">
import PageHeader from "../../components/PageHeader.vue";
import MyPageNav from "../../components/MyPageNav.vue";
import PartnerInfo from "../../components/PartnerInfo.vue";
import {Component, Vue} from 'vue-property-decorator'
@Component({
  components: {PageHeader, MyPageNav, PartnerInfo}
})
export default class MyPageView extends Vue {};
</script>

<style scoped lang="scss">
.partner-component {
  width: 100%;
  text-align: center;
}

.content {
  text-align: center;
  align-items: center;
  width: 100%;
}
</style>