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
    <div class=progress >
      <div v-bind:class="['progress-bar', 'progress-bar-striped', 'progress-bar-animated']"
      role=progressbar v-bind:style="{width: this.diskSpacePercent + '%'}">
      {{diskSpacePercent +'%'}} full
      </div>
    </div>
    <p>{{diskSpaceUsed}} GB used out of {{diskSpaceSize}} GB total</p>
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
      this.getServers();
      this.getDiskSpace();
    },
    computed: {
      ...mapGetters('staging', ['servers']),
      ...mapGetters('staging', ['diskSpaceSize', 'diskSpaceUsed', 'diskSpacePercent']),
    },
    methods: {
      ...mapActions('staging', ['getServers']),
      ...mapActions('staging', ['getDiskSpace']),
    },
  };
</script>

<style scoped>

</style>
