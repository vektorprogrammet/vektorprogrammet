<template>
  <b-container>
      <PageHeader>
        <h1>Min side</h1>
      </PageHeader>
        <b-col >           
          <p> <b-button @click="partyClick" variant="primary">Til partypage</b-button> </p>
          <p> <b-button @click="logoutClick" variant="primary">Logg ut</b-button> </p>
        </b-col>
    </b-container>
    
</template>

<script>
  import PageHeader from '../../components/PageHeader';
  export default {
    name: 'MyPageView',
    components: {PageHeader},
    methods: {
      partyClick(evt) {
        evt.preventDefault();
        this.$router.push({name: 'party'});
      },
      logoutClick(evt) {
        evt.preventDefault();
        this.$router.push({name: 'logout'});
      },
    },
  };
</script>

<style scoped>

.container {
    text-align: center;
  }

  p {
    padding: 50px 0 0 0;
  }

</style>
