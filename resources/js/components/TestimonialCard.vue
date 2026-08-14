<template>
  <v-card class="testimonial-card" elevation="0" outlined>
    <v-card-text class="pa-8">
      <!-- Rating -->
      <div class="rating mb-4">
        <v-rating
          :model-value="rating"
          readonly
          color="warning"
          size="small"
        ></v-rating>
      </div>

      <!-- Text -->
      <p class="text-body-1 mb-6 italic">
        "{{ text }}"
      </p>

      <!-- Author -->
      <div class="author d-flex align-center gap-3">
        <v-avatar :color="avatarColor" class="text-white font-weight-bold">
          {{ getInitials(author) }}
        </v-avatar>
        <div>
          <div class="font-weight-bold text-body-2">{{ author }}</div>
          <div class="text-caption text-medium-emphasis">{{ company }}</div>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
defineProps({
  text: {
    type: String,
    required: true
  },
  author: {
    type: String,
    required: true
  },
  company: {
    type: String,
    required: true
  },
  rating: {
    type: Number,
    default: 5
  },
  avatarColor: {
    type: String,
    default: 'primary'
  }
});

const getInitials = (name) => {
  return name
    .split(' ')
    .map(n => n.charAt(0))
    .join('')
    .toUpperCase();
};
</script>

<style scoped>
.testimonial-card {
  border-radius: 12px;
  transition: all 0.3s ease;
  background: rgba(212, 175, 55, 0.05);
  border: 1px solid rgba(212, 175, 55, 0.3) !important;
  height: 100%;
}

.testimonial-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(212, 175, 55, 0.2);
  border-color: rgba(212, 175, 55, 0.6) !important;
  background: rgba(212, 175, 55, 0.1);
}

.testimonial-card :deep(p) {
  color: #b0b0b0 !important;
}

.testimonial-card :deep(.font-weight-bold) {
  color: #d4af37 !important;
}

.testimonial-card :deep(.text-caption) {
  color: #888 !important;
}

.rating {
  display: inline-flex;
}

.gap-3 {
  gap: 0.75rem;
}
</style>
