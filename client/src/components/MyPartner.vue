<template>
      <div class="card text-left">
            <div class="card-body">
                  <div class="card-text">
                        <h3 class="card-title">Din vektorpartner</h3>
                        <div class="container">
                              <b-row>
                                    <b-col>
                                          <ProfilePicture :picture_path="picture_path" picture_width="30%"/>
                                    </b-col>
                                    <b-col>
                                          <h5>{{user_partner.fullName}}</h5>
                                          <a :href="'mailto:' + user_partner.email">{{user_partner.email}}</a>
                                    </b-col>
                              </b-row>
                        </div>
                  </div>
            </div>
      </div>

</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import { Prop } from 'vue-property-decorator';
import ProfilePicture from "./ProfilePicture";
@Component({
    components: {ProfilePicture}
})
export default class MyPartner extends Vue {
  @Prop() private user_partner: any;
  @Prop() private picture_path: string = 'http://localhost:8000/' + this.user_partner.picture_path;
}
</script>

<style scoped lang="scss">
</style>
