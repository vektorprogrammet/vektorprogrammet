<template>
  <div>
    <h3>Assitant Table</h3>
    <ol>
      <li v-for="assistant in assistants">
        {{ assistant.name }}
      </li>
    </ol>
  </div>
</template>

<script>
  export default {
    name: 'assistant-table',
    data () {
      return {
        assistants: []
      }
    },
    methods: {
      getAssistants () {
        console.log('getting assistants from api')
        this.$http
          .get('http://localhost:8000/kontrollpanel/api/assistants')
          .then((response) => {
            this.assistants = JSON.parse(response.body)
            console.log(this.assistants)
          }, (response) => {
            console.error('err!', response.statusText)
          })
      }
    },
    created () {
      this.getAssistants()
    }
  }
</script>
