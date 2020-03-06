<template>
  <div class="top-header desktop-header d-none d-md-flex">
    <div class="container">
      <nav class="main-navigation">
        <img src="../assets/vektor-logo.svg" class="logo img-fluid" alt="Vektor logo">
        <div class="menu-items">
          <!--{{ include('base/user_dropdown.html.twig') }}-->


          <div class="dropdown">
            <span>Lang og fion dropdown boi</span>
          </div>



          <div class="menu-links">

            <a class="menu-link active " href="#">Assistenter</a>
            <a class="menu-link active " href="#">Team</a>
            <a class="menu-link active " href="#">For skoler</a>
            <a class="menu-link active " href="#">For foreldre</a>
            <a class="menu-link active " href="#">Om oss</a>
            <a class="menu-link active " href="#">Kontakt</a>

            <!--{% include('base/menu_item.html.twig') with {route: 'assistants', name: 'Assistenter'} %}
            {% include('base/menu_item.html.twig') with {route: 'team', name: 'Team'} %}
            {% include('base/menu_item.html.twig') with {route: 'teachers', name: 'For skoler'} %}
            {% include('base/menu_item.html.twig') with {route: 'parents', name: 'For foreldre'} %}
            {% include('base/menu_item.html.twig') with {route: 'about', name: 'Om oss'} %}
            {% include('base/menu_item.html.twig') with {route: 'contact', name: 'Kontakt'} %}-->
          </div>
        </div>
      </nav>
    </div>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
@Component
export default class AssistantNav extends Vue {}
</script>

<style scoped lang="scss">
  .breadcrumb{
    margin-bottom: 0;
  }

  .assistant-nav {
    z-index: 100;
    display: block;
  }

  .top-header {
    padding: 10px 0;
  }
  .main-navigation{
    height: 85px;
    display: flex;
    justify-content: center;
  }

  .logo {
    display: block;
    margin: 0 auto;
    max-height: 100%;
    max-width: 100%;
    height: 85px;
  }

  .menu-items {
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    margin-bottom: 2px;
    white-space: nowrap;
  }

  .menu-link {
    padding: 14px 16px;
    -ms-flex-item-align: end;
    align-self: flex-end;
    font-size: 17px;
    color: #4A5155;
  }

  .user-dropdown {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: end;
    -ms-flex-pack: end;
    justify-content: flex-end;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
  }

  img {
    vertical-align: middle;
    border-style: none;
  }

</style>