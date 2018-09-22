<template>
  <b-container>
    <PageHeader>
      <h1>Staging server</h1>
    </PageHeader>
    <b-row>
      <b-col cols="12">
        <p>{{servers.length}} live staging servers</p>
        <b-table responsive striped hover :items="servers" :fields="fields">
          <a slot="url" slot-scope="data" :href="data.value" target="_blank">{{data.value}}</a>
        </b-table>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
  import PageHeader from '../../components/PageHeader';
  import { mapGetters, mapActions } from 'vuex';

  export default {
    name: 'StagingServerView',
    components: {PageHeader},
    data() {
      return {
        fields: [
          {
            key: 'repo',
          },
          {
            key: 'branch',
          },
          {
            key: 'url',
          },
        ],
      };
    },
    mounted() {
      this.getServers().then(() => {
        console.log(this.servers);
      });
    },
    computed: {
      ...mapGetters('staging', ['servers']),
    },
    methods: {
      ...mapActions('staging', ['getServers']),
    },
  };
</script>

<style scoped>

</style>
