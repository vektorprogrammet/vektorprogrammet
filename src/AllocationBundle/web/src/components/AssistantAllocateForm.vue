<template>
  <div>
    <label>
            Bolk
            <select v-bind="group">
              <option value="">Velg bolk</option>
              <option value="Bolk 1, Bolk 2">Begge</option>
              <option value="Bolk 1">Bolk 1</option>
              <option value="Bolk 2">Bolk 2</option>
            </select>
          </label>
          <label v-if="group != null">
            Dag
            <select>
              <option value="">Velg dag</option>
              <option value="">Mandag</option>
            </select>
          </label>
          <label v-if="group != null && day != null">
            Skole
            <select>
              <option value="">Velg skole</option>
              <option value="">Åsheim</option>
            </select>
          </label>
          <div class="text-center" v-if="group != null && day != null && school != null">
            <button class="button small">Fordel til skole</button>
          </div>
  </div>
</template>

<script>
  export default {
    props: ['assistant']
  }
</script>
