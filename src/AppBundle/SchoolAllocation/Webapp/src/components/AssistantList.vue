<template>
    <div>
        <h5>{{day}} ({{assistants.length}}/{{capacity}})</h5>
        <ul>
            <li v-for="assistant in sortedAssistants" :class="{double: assistant.double}">
                {{assistant.name}} - {{assistant.score}}
            </li>
        </ul>
    </div>
</template>

<script>
  export default {
    props: ['assistants', 'capacity', 'day'],
    computed: {
      sortedAssistants: function () {
        return this.assistants.sort((a, b) => a.score > b.score);
      }
    }
  }
</script>

<style lang="stylus" scoped>
    .double
        color: #0000ff;
</style>
