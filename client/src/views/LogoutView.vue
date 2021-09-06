<template>
  <b-container>
    <PageHeader>
      <h1>Du er nå logget ut</h1>
    </PageHeader>
      <b-col >
          <h3>Takk for denne gang!</h3>
          <p >
            <b-button @click="click" variant="primary">Til innlogging</b-button>
          </p>
      </b-col>
  </b-container>
</template>

<script>
  import PageHeader from '../components/PageHeader';
  import { mapActions } from 'vuex';


  export default {
    name: 'LogoutView',
    components: {PageHeader},
    methods: {
      ...mapActions('account', ['logout']),
      click(evt) {
        evt.preventDefault();
        this.$router.push({name: 'login'});
      }
    },
    beforeMount() {
      this.logout()
    }
  };
</script>

<style scoped>
  label {
    width: 100%;
  }

  .container {
    text-align: center;
  }

  p {
    padding: 50px;
  }

</style>
