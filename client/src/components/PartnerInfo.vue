<template>
  <div class="content" >
    <h2>Din Vektorpartner:</h2>
    <b-row>
      <b-col>
        <img src="../assets/tor.png" alt="tor1" id="Me" class="profile-photo">
        <p>Deg</p>
      </b-col>
      <b-col>
        <img src="../assets/tor.png" alt="tor2" class="profile-photo">
        <p>Amir</p>
      </b-col>
    </b-row>

  </div>
</template>

<script>
  export default {
    name: 'AssistantNav',
  };
</script>

<style scoped lang="scss">

  .profile-photo {
    max-width: 50%;
    height: auto;
    border-radius: 50em;
  }

</style>
