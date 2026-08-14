<template>
  <div class="how-it-works-step">
    <div class="step-container">
      <div class="step-number">{{ step }}</div>
      <div class="step-connector" v-if="showConnector"></div>
    </div>
    <div class="step-content">
      <h3 class="text-h6 font-weight-bold mb-2">{{ title }}</h3>
      <p class="text-body-2 text-medium-emphasis">{{ description }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  step: {
    type: [String, Number],
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
  showConnector: {
    type: Boolean,
    default: true
  }
});
</script>

<style scoped>
.how-it-works-step {
  display: flex;
  gap: 2rem;
  position: relative;
}

.step-container {
  position: relative;
  flex-shrink: 0;
}

.step-number {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #d4af37 0%, #b8941d 100%);
  color: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: bold;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
}

.step-connector {
  position: absolute;
  top: 56px;
  left: 28px;
  width: 2px;
  height: 80px;
  background: linear-gradient(to bottom, #d4af37, rgba(212, 175, 55, 0.2));
}

.step-content {
  padding-top: 0.5rem;
  color: white;
}

.step-content h3 {
  color: #d4af37 !important;
}

.step-content p {
  color: #b0b0b0 !important;
}

@media (max-width: 600px) {
  .how-it-works-step {
    gap: 1rem;
  }

  .step-number {
    width: 48px;
    height: 48px;
    font-size: 1.25rem;
  }

  .step-connector {
    left: 24px;
    height: 60px;
  }
}
</style>
