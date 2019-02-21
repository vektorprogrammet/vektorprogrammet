<template>
  <div class="content">
    <h2>Ditt Vektorteam:</h2>
    <b-row>
      <b-col>
        <img
          :src="this.full_path"
          alt="tor1"
          id="Me"
          class="profile-photo"
        />
        <p>{{this.user_me.fullName}}</p>
      </b-col>
      <b-col>
        <img  src="../assets/logo.png" alt="tor2" class="profile-photo">
        <p>Assistent Assistentsson</p>
      </b-col>
    </b-row>
  </div>
</template>

<script lang="ts">
import { accountService } from '../services';
import Vue from 'vue';
import Component from 'vue-class-component';
@Component
export default class AssistantNav extends Vue {
  private user_me: any;
  private full_path: string = '';
  public mounted() {
    accountService.getUser().then(result => {
      this.user_me = result;
      this.full_path = 'http://localhost:8000/' + this.user_me.picture_path
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
