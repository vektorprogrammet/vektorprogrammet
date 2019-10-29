<template>
  <div class="content">
    <br><br><br>
    <h2>Mitt Vektorteam:</h2>

      <!--b-col>
        <ProfilePicture :picture_path="user_me.picture_path" :picture_width="picture_width"/>
          <br><br>
        <h5>{{user_me.fullName}}</h5>
        <p>{{user_me.email}}</p>
      </b-col-->

    <h3>Bolk 1</h3>
      <b-row>
        <b-col v-for="partner in this.user_partners" style="max-width: 200px">
          <ProfilePicture :picture_path="partner.picture_path" :picture_width="picture_width"/>
          <br><br>
          <h5>{{partner.fullName}}</h5>
          <p class="link"><a :href="'mailto:' + partner.email">{{partner.email}}</a></p>
          <p>
            <a class="text-default" :href="'tel: ' + partner.phone">
              <i class="fa fa-phone"></i> {{ partner.phone }}
            </a>
          </p>
        </b-col>

      </b-row>

    <h3>Bolk 2</h3>
      <b-row>
        <b-col v-for="partner in this.users1">
          <ProfilePicture :picture_path="partner.picture_path" :picture_width="picture_width"/>
          <br><br>
          <h5>{{partner.fullName}}</h5>
          <p class="link"><a :href="'mailto:' + partner.email">{{partner.email}}</a></p>
          <p>
            <a class="text-default" :href="'tel: ' + partner.phone">
              <i class="fa fa-phone">tlf: </i> {{ partner.phone }}
            </a>
          </p>
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
  @Prop() private user_partners: Array<any>;
  @Prop() private picture_width: string = '50%';
  private users1: any = [this.user_partners[0]];
  private users2: any = this.user_partners[1];


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
