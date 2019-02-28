<template>
<div>
  <b-img-lazy
          :src="picture_path"
          rounded="circle"
          alt="Profilbilde"
          width="picture_width"
          class="profile-picture"
          v-bind:style="{width: picture_width}"
  />
</div>

</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import { Prop } from 'vue-property-decorator';

@Component
export default class ProfilePicture extends Vue {
  @Prop() private picture_path: string;
  @Prop() private picture_width: string;
}
</script>

<style scoped lang="scss">
.profile-picture {
  height: auto;
  object-fit: fill
}


</style>
