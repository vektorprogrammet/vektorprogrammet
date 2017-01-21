<template>
  <div>
    <h3 class="text-center">Assistenter</h3>
    <assistant-card :assistant="assistant" v-for="assistant in assistants"></assistant-card>
  </div>
</template>

<script>
  import AssistantAvailability from './AssistantAvailability.vue'
  import AssistantSuitability from './AssistantSuitability.vue'
  import AssistantCard from './AssistantCard.vue'

  export default {
    data () {
      return {
        assistants: []
      }
    },
    created () {
      this.getAssistants()
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
    components: {
      'assistant-card': AssistantCard
    }
  }
</script>
