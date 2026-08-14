<template>
  <v-card class="benefit-card" elevation="0" outlined>
    <v-card-text class="pa-8 text-center">
      <div class="benefit-icon mb-6">
        <v-icon :size="iconSize" :color="iconColor">{{ icon }}</v-icon>
      </div>
      <h3 class="text-h6 font-weight-bold mb-3">{{ title }}</h3>
      <p class="text-body-2 text-medium-emphasis">{{ description }}</p>
    </v-card-text>
  </v-card>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  iconColor: {
    type: String,
    default: 'primary'
  },
  iconSize: {
    type: [String, Number],
    default: 56
  }
});
</script>

<style scoped>
.benefit-card {
  height: 100%;
  border-radius: 12px;
  transition: all 0.3s ease;
  background: rgba(212, 175, 55, 0.05);
  border: 1px solid rgba(212, 175, 55, 0.3) !important;
}

.benefit-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 32px rgba(212, 175, 55, 0.2);
  border-color: rgba(212, 175, 55, 0.6) !important;
  background: rgba(212, 175, 55, 0.1);
}

.benefit-card :deep(h3) {
  color: #d4af37 !important;
}

.benefit-card :deep(p) {
  color: #b0b0b0 !important;
}

.benefit-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 80px;
  border-radius: 12px;
  background: rgba(212, 175, 55, 0.15);
}
</style>
