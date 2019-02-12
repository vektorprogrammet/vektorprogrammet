<template>
  <div class="content" >
    <h2>Din Vektorpartner:</h2>
    <b-row>
      <b-col>
        <img :src="myProfilePicture" alt="Mitt Bilde" class="profile-photo">
        <p>{{this.user_me.fullName}}</p>
      </b-col>
      <b-col>
        <img src="../assets/tor.png" alt="Partners Bilde" id="My Partner" class="profile-photo">
        <p>Min Partner</p>
      </b-col>
    </b-row>

  </div>
</template>

<script>
  export default {
    name: 'AssistantNav',

      data() {
        return {
            user_me: '',
            partner: ''
        }
      },
      mounted() {
        this.user_me = this.$store.getters['account/user'];
        console.log(this.user_me.fullName)
      },

      computed: {
        myProfilePicture: function() {
            return 'http://localhost:8000/' + this.user_me.picture_path ;
        }
      }
  };



</script>

<style scoped lang="scss">

  .profile-photo {
    max-width: 50%;
    height: auto;
    border-radius: 50em;
  }

</style>
