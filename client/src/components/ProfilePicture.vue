<template>
<div>
  <b-img-lazy
          :src="fullPicturePath"
          rounded="circle"
          alt="Profilbilde"
          width="pictureWidth"
          class="profile-picture"
          v-bind:style="{width: pictureWidth}"
  />
</div>

</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import { Prop } from 'vue-property-decorator';

@Component
export default class ProfilePicture extends Vue {
  @Prop() private picturePath: string | undefined;
  @Prop() private pictureWidth: string | undefined;
  private fullPicturePath: string = 'http://localhost:8000/' + this.picturePath;
}
</script>

<style scoped lang="scss">
.profile-picture {
  height: auto;
  object-fit: fill
}


</style>
