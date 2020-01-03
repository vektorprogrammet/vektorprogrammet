<template>
      <div class="m-4">
            <!--h3 class="card-title">Logget inn som</h3-->
            <div class="container">
                  <ProfilePicture :picturePath="user.picture_path" pictureWidth="30%" class="mb-4"/>
                  Du er logget inn som
                  <h5 class="m-3">{{user.fullName}}</h5>
            </div>
      </div>

</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import {InjectReactive} from 'vue-property-decorator';
import ProfilePicture from "./ProfilePicture";
@Component({
    components: {ProfilePicture}
})
export default class UserInfo extends Vue {
  @InjectReactive() private user!: any;
}
</script>

<style scoped lang="scss">

</style>
