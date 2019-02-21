<template>
  <div class="content">
    <br><br><br>
    <h2>Ditt Vektorteam:</h2>
    <b-row>
      <b-col>
        <img
          :src="this.full_path_me"
          alt="Mitt Profilbilde"
          class="profile-photo"
        />
          <br><br>
        <h5>{{user_me.fullName}}</h5>
        <p>{{user_me.email}}</p>
      </b-col>
      <b-col>
        <img
          :src="this.full_path_partner"
          alt="Partners profilbilde"
          class="profile-photo"
        />
        <br><br>
        <h5>{{user_partner.fullName}}</h5>
        <p>{{user_partner.email}}</p>
      </b-col>
    </b-row>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import { Prop, Watch } from 'vue-property-decorator';

@Component
export default class AssistantNav extends Vue {
  @Prop() private user_me: any;
  @Prop() private user_partner: any;
  private full_path_partner: string = 'http://localhost:8000/' +this.user_partner.picture_path;
  private full_path_me: string = 'http://localhost:8000/' +this.user_me.picture_path;
  /*@Watch('user_me')
  onPropertyChanged() {
    this.full_path_me = 'http://localhost:8000/' +this.user_me.picture_path;
    this.full_path_partner = 'http://localhost:8000/' + this.user_partner.picture_path;
    console.log(this.full_path_me)
  };*/
  @Watch('user_partner')
  onPropertyChanged() {
    this.full_path_partner = 'http://localhost:8000/' + this.user_partner.picture_path;
    this.full_path_me = 'http://localhost:8000/' +this.user_me.picture_path;
  };
}
</script>

<style scoped lang="scss">
.profile-photo {
  max-width: 50%;
  height: auto;
  border-radius: 50em;
}
</style>
