<template>
  <div class="panel radius">
    <span class="float-right hover-hand" @click="removeAssistant">X</span>
    <h6 class="hover-hand" @click="toggleForm">
      {{ assistant.name }} <br>
      <small>
        <assistant-suitability
          :score="assistant.score"
          :suitable="assistant.suitable"
          :previousParticipation="assistant.previousParticipation"></assistant-suitability>
        <br>
        <assistant-availability
          :availability="assistant.availability"
          :preferredGroup="assistant.preferredGroup"
          :doublePosition="assistant.doublePosition"></assistant-availability>
      </small>
    </h6>
    <assistant-allocate-form
      v-if="showForm"
      :assistant="assistant"
      :group="assistant.group"
      :day="assistant.assignedDay"
      :school="assistant.assignedSchool"></assistant-allocate-form>
  </div>
</template>

<script>
  import AssistantAvailability from './AssistantAvailability.vue'
  import AssistantSuitability from './AssistantSuitability.vue'
  import AssistantAllocateForm from './AssistantAllocateForm.vue'

  export default {
    props: ['assistant'],
    data () {
      return {
        showForm: false
      }
    },
    components: {
      'assistant-availability': AssistantAvailability,
      'assistant-suitability': AssistantSuitability,
      'assistant-allocate-form': AssistantAllocateForm
    },
    methods: {
      removeAssistant () {
        confirm('Er du sikker på at du vil fjerne ' + this.assistant.name + ' fra fordelingen?')
      },
      toggleForm() {
        this.showForm = !this.showForm;
      }
    }
  }
</script>
