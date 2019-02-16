<template>
  <b-container>
    <PageHeader>
      <h1>Staging server</h1>
    </PageHeader>
    <b-row>
      <b-col cols="12">
        <p>{{ servers.length }} live staging servers</p>
        <b-table responsive striped hover :items="servers" :fields="fields">
          <a slot="url" slot-scope="data" :href="data.value" target="_blank">{{
            data.value
          }}</a>
        </b-table>
      </b-col>
    </b-row>
    <div class="progress">
      <div
        v-bind:class="[
          'progress-bar',
          'progress-bar-striped',
          'progress-bar-animated'
        ]"
        role="progressbar"
        v-bind:style="{ width: this.diskSpacePercent + '%' }"
      >
        {{ diskSpacePercent + "%" }} full
      </div>
    </div>
    <p>{{ diskSpaceUsed }} GB used out of {{ diskSpaceSize }} GB total</p>
  </b-container>
</template>

<script lang="ts">
import PageHeader from "../../components/PageHeader.vue";
import {Action, Getter} from 'vuex-class';
import Vue from "vue";
import {Component} from 'vue-property-decorator'

const namespace: string = 'staging';
@Component({
  components: { PageHeader },
})
export default class StagingServerView extends Vue {
  @Action('getServers', {namespace}) getServers: any;
  @Action('getDiskSpace', {namespace}) getDiskSpace: any;
  @Getter('servers', {namespace}) servers: any;
  @Getter('diskSpaceSize', {namespace}) diskSpaceSize: any;
  @Getter('diskSpaceUsed', {namespace}) diskSpaceUsed: any;
  @Getter('diskSpacePercent', {namespace}) diskSpacePercent: any;
  fields: any[] = [
    {
      key: "repo"
    },
    {
      key: "branch"
    },
    {
      key: "url"
    }
  ];
  mounted() {
    this.getServers();
    this.getDiskSpace();
  };
};
</script>

<style scoped></style>