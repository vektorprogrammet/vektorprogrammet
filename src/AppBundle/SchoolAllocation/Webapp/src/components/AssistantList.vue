<template>
  <div>
    <h5>{{day}} ({{assistants.length}}/{{capacity}})</h5>
    <ul>
      <li v-for="assistant in assistants">
        {{assistant.name}}
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    props: ['assistants', 'capacity', 'day'],
  }
</script>
