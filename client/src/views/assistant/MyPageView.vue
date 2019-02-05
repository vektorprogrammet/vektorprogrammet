<template>
    <div class="content">
        <MyPageNav></MyPageNav>
        <PageHeader><h1>Min side</h1></PageHeader>
        <PartnerInfo class="partner-component"></PartnerInfo>
    </div>
</template>

<script>
  import PageHeader from '../../components/PageHeader';
  import MyPageNav from '../../components/MyPageNav';
  import PartnerInfo from '../../components/PartnerInfo';
  export default {
    name: 'MyPageView',
    components: {PageHeader, MyPageNav, PartnerInfo},
  };
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
