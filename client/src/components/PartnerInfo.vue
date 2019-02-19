<template>
  <div class="content" >
    <h2>Din Vektorpartner:</h2>
    <b-row>
      <b-col>
        <img src="../assets/logo.png" alt="tor1" id="Me" class="profile-photo">
        <p>Deg</p>
      </b-col>
      <b-col>
        <img v-bind:src="myProfilePicture" alt="tor2" class="profile-photo">
        <p>Amir</p>
      </b-col>
    </b-row>
  </div>
</template>

<script lang="ts">
  import {accountService} from '../services';
  import Vue from 'vue';
  import Component from 'vue-class-component'
  @Component
  export default class AssistantNav extends Vue {
    public myProfilePicture: string = '';
    public mounted() {
        accountService.getProfilePicture().then((result) => {
            console.log(result);
        });
    }
  }
</script>

<style scoped lang="scss">
  .profile-photo {
    max-width: 50%;
    height: auto;
    border-radius: 50em;
  }
</style>
