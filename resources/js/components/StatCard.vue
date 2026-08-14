<template>
  <v-card class="stat-card" elevation="0" outlined>
    <v-card-text class="pa-6 text-center">
      <div class="stat-value text-h4 font-weight-bold mb-2">
        {{ value }}
      </div>
      <p class="text-body-2">{{ label }}</p>
    </v-card-text>
  </v-card>
</template>

<script setup>
defineProps({
  value: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  }
});
</script>

<style scoped>
.stat-card {
  border-radius: 12px;
  transition: all 0.3s ease;
  background: rgba(212, 175, 55, 0.05);
  border: 1px solid rgba(212, 175, 55, 0.3) !important;
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(212, 175, 55, 0.2);
  background: rgba(212, 175, 55, 0.1);
}

.stat-card :deep(.stat-value) {
  background: linear-gradient(135deg, #d4af37 0%, #ffeb3b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1;
  margin-bottom: 0.5rem;
  font-weight: 900;
}

.stat-card p {
  color: #b0b0b0 !important;
}
</style>
