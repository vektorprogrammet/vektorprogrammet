<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>

  export default {
    name: 'app',
  };
</script>

<style lang="scss">
  #app {
    font-family: 'Montserrat', sans-serif;
    color: #333;
    background: #fafdff;

    h1, h2, h3, h4, h5 {
      font-family: 'Lato', sans-serif;
      line-height: 1.2;
      font-weight: 700;
    }

    h2 {
      font-size: 28px;
    }

    h3 {
      font-size: 24px;
    }

    p {
      line-height: 1.6;
      font-size: 18px;
    }

    strong {
      font-weight: 600;
    }

    strong strong {
      font-weight: 700;
    }

    .text-dark-blue {
      color: #023874;
    }
  }
</style>
