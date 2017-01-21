<template>
  <div>
    <h3 class="text-center">Assistenter</h3>
    <div class="panel radius" v-for="assistant in assistants">
      <div class="row">
        <div class="small-8 columns">
          <h6>
            {{ assistant.name }} <br>
            <small>
              <assistant-availability :availability="assistant.availability"></assistant-availability>
               -
              <span v-if="assistant.doublePosition">Dobbel</span>
              <span v-else>Bolk {{ assistant.preferredGroup }}</span>
              -
              {{ assistant.score }} Poeng
            </small>
          </h6>
        </div>
        <div class="small-4 columns">
          <span class="float-right hover-hand" @click="removeAssistant">X</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import AssistantAvailability from './AssistantAvailability.vue'

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
      },
      removeAssistant () {
        confirm('Er du sikker på at du vil slette assistenten?')
      }
    },
    components: {
      'assistant-availability': AssistantAvailability
    }
  }
</script>
