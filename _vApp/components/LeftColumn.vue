<template>
  <div class="">
    <v-expansion-panels accordion>
      <v-expansion-panel>
        <v-expansion-panel-header>{{ heading }}</v-expansion-panel-header>
        <v-expansion-panel-content
          eager
          v-for="item in checkboxData"
          :key="item.id">
            <v-checkbox
              v-model="item.id"
              :label="item.name"
            ></v-checkbox>
        </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
  </div>
</template>

<script>
export default {
  props: [
    'items',
    'heading'
  ],
  data () {
      return {
        checkboxData : this.items
      }
  }
}
</script>
