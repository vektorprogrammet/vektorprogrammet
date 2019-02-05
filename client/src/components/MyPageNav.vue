<template>
  <div class="assistant-nav">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="/">Forsiden</a>
      </li>
      <li class="breadcrumb-item">
        Min side
      </li>
      <li class="breadcrumb-menu">
      </li>
    </ol>
    <UserDropdownComponent></UserDropdownComponent>
  </div>
</template>

<script>
  import UserDropdownComponent from "./UserDropdownComponent";
  export default {
    name: 'AssistantNav',
      components: {UserDropdownComponent},
  };
</script>
