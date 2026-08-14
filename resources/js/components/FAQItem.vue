<template>
  <v-expansion-panel class="faq-item">
    <v-expansion-panel-title class="text-body-1 font-weight-bold">
      {{ question }}
      <template #actions="{ expanded }">
        <v-icon :class="{ 'rotate-icon': expanded }">mdi-chevron-down</v-icon>
      </template>
    </v-expansion-panel-title>
    <v-expansion-panel-text class="text-body-2 text-medium-emphasis">
      {{ answer }}
    </v-expansion-panel-text>
  </v-expansion-panel>
</template>

<script setup>
defineProps({
  question: {
    type: String,
    required: true
  },
  answer: {
    type: String,
    required: true
  }
});
</script>

<style scoped>
.faq-item {
  background: rgba(212, 175, 55, 0.05) !important;
  border: 1px solid rgba(212, 175, 55, 0.3) !important;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.faq-item :deep(.v-expansion-panel-title) {
  padding: 1rem 1.5rem;
  font-size: 1rem;
  color: #d4af37;
  font-weight: 600;
}

.faq-item:hover {
  background: rgba(212, 175, 55, 0.1) !important;
  border-color: rgba(212, 175, 55, 0.4) !important;
}

.faq-item :deep(.v-expansion-panel-text__wrapper) {
  padding: 0 1.5rem 1rem 1.5rem;
}

.faq-item :deep(.v-expansion-panel-text) {
  color: #b0b0b0;
}

.faq-item :deep(.v-expansion-panel-title__icon) {
  color: #d4af37;
}

.rotate-icon {
  transform: rotate(180deg);
  transition: transform 0.3s ease;
  color: #d4af37 !important;
}

:deep(.v-expansion-panel-title__icon) {
  margin-inline-start: auto;
}
</style>
