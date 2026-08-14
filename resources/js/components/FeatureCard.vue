<template>
  <div class="feature-item d-flex gap-4">
    <div class="feature-icon flex-shrink-0">
      <v-icon :color="iconColor" size="32">{{ icon }}</v-icon>
    </div>
    <div class="feature-content">
      <h4 class="text-body-1 font-weight-bold mb-1">{{ title }}</h4>
      <p class="text-body-2 text-medium-emphasis">{{ description }}</p>
    </div>
  </div>
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
  }
});
</script>

<style scoped>
.feature-item {
  padding: 1.5rem;
  border-radius: 8px;
  background: rgba(212, 175, 55, 0.05);
  transition: all 0.3s ease;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.feature-item:hover {
  background: rgba(212, 175, 55, 0.1);
  border-color: rgba(212, 175, 55, 0.4);
  box-shadow: 0 8px 20px rgba(212, 175, 55, 0.15);
}

.feature-item h4 {
  color: #d4af37 !important;
}

.feature-item p {
  color: #b0b0b0 !important;
}

.feature-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 8px;
  background: rgba(212, 175, 55, 0.15);
  color: #d4af37;
}

.gap-4 {
  gap: 1rem;
}
</style>
