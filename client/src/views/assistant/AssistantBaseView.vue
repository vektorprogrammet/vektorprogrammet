<template>
  <div class="assisstant-base">
    <assistant-nav></assistant-nav>
    <router-view></router-view>
  </div>
</template>

<script lang="ts">
import AssistantNav from '../../components/AssistantNav.vue';
import Vue from 'vue';
import {Component} from 'vue-property-decorator';
@Component({
  components: {AssistantNav},
})
export default class AssistantBaseView extends Vue {}
</script>
