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
            <a class="menu-link" href="#">Team</a>
            <a class="menu-link" href="#">For skoler</a>
            <a class="menu-link" href="#">For foreldre</a>
            <a class="menu-link" href="#">Om oss</a>
            <a class="menu-link" href="#">Kontakt</a>

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
  @import "../../../app/Resources/assets/scss/bootstrap";
  @import "../../../app/Resources/assets/scss/footer";
  @import "../../../app/Resources/assets/scss/header";
  @import "../../../app/Resources/assets/scss/mobile_nav";
</style>