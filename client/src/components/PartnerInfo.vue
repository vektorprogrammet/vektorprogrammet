<template>
  <div class="content">
    <br><br><br>
    <h2>Mitt Vektorteam:</h2>
    <b-row>
      <b-col>
        <ProfilePicture :picture_path="full_path_me" :picture_width="picture_width"/>
          <br><br>
        <h5>{{user_me.fullName}}</h5>
        <p>{{user_me.email}}</p>
      </b-col>
      <b-col>
        <ProfilePicture :picture_path="full_path_partner" :picture_width="picture_width"/>
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
import ProfilePicture from "./ProfilePicture";
@Component({
    components: {ProfilePicture}
})
export default class PartnerInfo extends Vue {
  @Prop() private user_me: any;
  @Prop() private user_partner: any;
  @Prop() private picture_width: string = '50%';
  private full_path_partner: string = 'http://localhost:8000/' +this.user_partner.picture_path;
  private full_path_me: string = 'http://localhost:8000/' +this.user_me.picture_path;
}
</script>

<style scoped lang="scss">
.profile-photo {
  max-width: 50%;
  height: auto;
  border-radius: 50em;
  height: auto;
  width: 100%;
  object-fit: fill
}


</style>
