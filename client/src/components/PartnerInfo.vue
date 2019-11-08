<template>
  <div class="content">
    <br><br><br>
    <h2>Mitt Vektorteam:</h2>

      <!--b-col>
        <ProfilePicture :picturePath="me.picture_path" :pictureWidth="pictureWidth"/>
          <br><br>
        <h5>{{me.fullName}}</h5>
        <p>{{me.email}}</p>
      </b-col-->

    <div v-if="this.partners['Bolk 1']">
      <h3>Bolk 1</h3>
      <b-row>
        <b-col v-for="partner in this.partners['Bolk 1']">
          <ProfilePicture :picturePath="partner.picture_path" :pictureWidth="pictureWidth"/>
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

    <div v-if="this.partners['Bolk 2']">
    <h3>Bolk 2</h3>
      <b-row>
        <b-col v-for="partner in this.partners['Bolk 2']">
          <ProfilePicture :picturePath="partner.picture_path" :pictureWidth="pictureWidth"/>
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

    <div v-if="this.partners['Bolk 1, Bolk 2']">
      <h3>Både bolk 1 og bolk 2</h3>
      <b-row>
        <b-col v-for="partner in this.partners['Bolk 1, Bolk 2']">
          <ProfilePicture :picturePath="partner.picture_path" :pictureWidth="pictureWidth"/>
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
  @Prop() private me: any;
  @Prop() private partners: any;
  @Prop() private pictureWidth: string = '250px';

  public async mounted() {
  }

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
