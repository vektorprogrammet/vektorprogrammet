<template>
  <div class="content">
    <br><br><br>
    <h2>Mitt Vektorteam:</h2>

    <div v-if="this.partners['Bolk 1']">
      <h3>Bolk 1</h3>
      <b-row>
        <b-col v-for="partner in this.partners['Bolk 1']">
          <ProfilePicture :picturePath="partner.picture_path" :pictureWidth="pictureWidth"/>
          <br><br>
          <h5>{{partner.fullName}}</h5>
          <p class="link">
            <a :href="'mailto:' + partner.email">
              <font-awesome-icon
                      icon="envelope"
                      id="mail"
              />
              {{partner.email}}
            </a>
          </p>
          <p>
            <a class="text-default" :href="'tel: ' + partner.phone">
              <font-awesome-icon
                      icon="phone"
                      id="phone"
              />
              {{ partner.phone }}
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
          <p class="link">
            <a :href="'mailto:' + partner.email">
              <font-awesome-icon
                      icon="envelope"
                      id="mail"
              />
              {{partner.email}}
            </a>
          </p>
          <p>
            <a class="text-default" :href="'tel: ' + partner.phone">
              <font-awesome-icon
                      icon="phone"
                      id="phone"
              />
              {{ partner.phone }}
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
import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faPhone,
  faEnvelope
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {InjectReactive} from 'vue-property-decorator';
import ProfilePicture from "./ProfilePicture";

library.add(faPhone, faEnvelope);
Vue.component('font-awesome-icon', FontAwesomeIcon);

@Component({
    components: {ProfilePicture}
})
export default class PartnerInfo extends Vue {
  @InjectReactive() private partners!: any;
  private pictureWidth: string = '250px';

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
