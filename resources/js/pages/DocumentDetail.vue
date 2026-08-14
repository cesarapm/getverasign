<template>
  <v-container class="document-detail-page">
    <!-- Back Button -->
    <div class="mb-6">
      <v-btn
        variant="text"
        color="primary"
        prepend-icon="mdi-chevron-left"
        @click="$router.back()"
      >
        Volver
      </v-btn>
    </div>

    <v-row>
      <!-- Main Content -->
      <v-col cols="12" lg="8">
        <!-- Document Info Card -->
        <v-card class="mb-6" elevation="0" outlined>
          <v-card-title class="pa-6 d-flex align-center gap-4">
            <v-icon size="32" color="primary">mdi-file-document</v-icon>
            <div class="flex-grow-1">
              <div class="text-h6">{{ document.name }}</div>
              <div class="text-caption text-medium-emphasis">ID: {{ document.id }}</div>
            </div>
            <v-chip
              :color="getStatusColor(document.status)"
              :text-color="getStatusTextColor(document.status)"
              variant="tonal"
              label
            >
              {{ getStatusLabel(document.status) }}
            </v-chip>
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text class="pa-6">
            <v-row class="mb-4">
              <v-col cols="12" md="3">
                <div class="info-item">
                  <div class="text-caption text-medium-emphasis mb-1">Enviado Por</div>
                  <div class="font-weight-bold">{{ document.sender }}</div>
                </div>
              </v-col>
              <v-col cols="12" md="3">
                <div class="info-item">
                  <div class="text-caption text-medium-emphasis mb-1">Fecha de Creación</div>
                  <div class="font-weight-bold">{{ formatDate(document.createdAt) }}</div>
                </div>
              </v-col>
              <v-col cols="12" md="3">
                <div class="info-item">
                  <div class="text-caption text-medium-emphasis mb-1">Vencimiento</div>
                  <div class="font-weight-bold" :class="isExpired ? 'text-error' : ''">
                    {{ formatDate(expirationDate) }}
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="3">
                <div class="info-item">
                  <div class="text-caption text-medium-emphasis mb-1">Progreso</div>
                  <div class="font-weight-bold">{{ progressPercentage }}%</div>
                </div>
              </v-col>
            </v-row>

            <v-progress-linear
              :value="progressPercentage"
              height="8"
              rounded
              :color="getStatusColor(document.status)"
              class="mb-4"
            ></v-progress-linear>

            <p v-if="document.description" class="text-body-2">
              <strong>Descripción:</strong> {{ document.description }}
            </p>
          </v-card-text>
        </v-card>

        <!-- PDF Preview (Mock) -->
        <v-card class="mb-6" elevation="0" outlined>
          <v-card-title class="pa-6">Vista Previa del Documento</v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-6 text-center">
            <div class="pdf-preview bg-light pa-8" style="min-height: 400px">
              <v-icon size="80" class="mb-4 text-medium-emphasis">mdi-file-pdf-box</v-icon>
              <div class="text-body-2 text-medium-emphasis">
                <p>{{ document.name }}.pdf</p>
                <p class="text-caption">Tamaño: 2.3 MB</p>
              </div>
              <v-btn
                color="primary"
                variant="outlined"
                class="mt-4"
                @click="downloadDocument"
              >
                <v-icon start>mdi-download</v-icon>
                Descargar PDF
              </v-btn>
            </div>
          </v-card-text>
        </v-card>

        <!-- Timeline -->
        <v-card elevation="0" outlined>
          <v-card-title class="pa-6">Línea de Tiempo</v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-6">
            <v-timeline
              v-if="timeline.length > 0"
              side="start"
              density="compact"
            >
              <v-timeline-item
                v-for="(event, idx) in timeline"
                :key="idx"
                :dot-color="getEventColor(event.type)"
                :icon="getEventIcon(event.type)"
                size="small"
              >
                <div class="text-body-2">
                  <strong>{{ event.title }}</strong>
                  <div class="text-caption text-medium-emphasis">
                    {{ formatDateTime(event.date) }}
                  </div>
                  <p class="mt-2 text-body-2">{{ event.description }}</p>
                </div>
              </v-timeline-item>
            </v-timeline>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Sidebar -->
      <v-col cols="12" lg="4">
        <!-- Signers Card -->
        <v-card class="mb-6" elevation="0" outlined>
          <v-card-title class="pa-6">
            <span>Firmantes</span>
            <v-spacer></v-spacer>
            <v-chip size="small" label>
              {{ document.signers.filter(s => s.status === 'signed').length }} / {{ document.signers.length }}
            </v-chip>
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text class="pa-0">
            <v-list>
              <template v-for="(signer, idx) in document.signers" :key="idx">
                <v-list-item class="px-6 py-4">
                  <template #prepend>
                    <v-avatar
                      :color="getSignerStatusColor(signer.status)"
                      class="text-white"
                      size="40"
                    >
                      <v-icon>{{ getSignerStatusIcon(signer.status) }}</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title>{{ signer.name }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ signer.email }}
                  </v-list-item-subtitle>

                  <template #append>
                    <v-chip
                      :color="getSignerStatusColor(signer.status)"
                      variant="tonal"
                      size="small"
                      label
                    >
                      {{ getSignerStatusLabel(signer.status) }}
                    </v-chip>
                  </template>
                </v-list-item>

                <v-divider v-if="idx < document.signers.length - 1"></v-divider>
              </template>
            </v-list>
          </v-card-text>
        </v-card>

        <!-- Document Settings -->
        <v-card class="mb-6" elevation="0" outlined>
          <v-card-title class="pa-6">Configuración</v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-6">
            <div class="setting-item mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Orden de Firma</div>
              <div class="text-body-2">
                {{ document.signingOrder === 'sequential' ? 'Secuencial' : 'En Paralelo' }}
              </div>
            </div>

            <div class="setting-item mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Notificaciones</div>
              <div class="text-body-2">
                <v-icon size="16" class="mr-2">
                  {{ document.notifications.enabled ? 'mdi-bell-outline' : 'mdi-bell-off-outline' }}
                </v-icon>
                {{ document.notifications.enabled ? 'Habilitadas' : 'Deshabilitadas' }}
              </div>
            </div>

            <div class="setting-item">
              <div class="text-caption text-medium-emphasis mb-1">Tipo de Firma</div>
              <div class="text-body-2">Electrónica</div>
            </div>
          </v-card-text>
        </v-card>

        <!-- Actions -->
        <v-card elevation="0" outlined>
          <v-card-title class="pa-6">Acciones</v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <v-btn
              color="primary"
              variant="tonal"
              block
              class="mb-3"
              @click="resendReminders"
              :disabled="document.status === 'signed' || document.status === 'rejected'"
            >
              <v-icon start>mdi-send</v-icon>
              Reenviar Recordatorios
            </v-btn>

            <v-btn
              color="warning"
              variant="outlined"
              block
              class="mb-3"
              @click="cancelDocument"
              :disabled="document.status === 'signed' || document.status === 'rejected'"
            >
              <v-icon start>mdi-close</v-icon>
              Cancelar Documento
            </v-btn>

            <v-btn
              color="secondary"
              variant="outlined"
              block
              @click="archiveDocument"
            >
              <v-icon start>mdi-archive</v-icon>
              Archivar
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Action Dialogs -->
    <v-dialog v-model="confirmDialog" max-width="400">
      <v-card>
        <v-card-title>{{ confirmDialog.title }}</v-card-title>
        <v-card-text>{{ confirmDialog.message }}</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="confirmDialog = false">Cancelar</v-btn>
          <v-btn color="error" variant="tonal" @click="handleConfirm">{{ confirmDialog.action }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

// Mock document data
const mockDocuments = {
  1: {
    id: 1,
    name: 'Contrato de Servicios',
    sender: 'Juan González',
    createdAt: new Date('2024-01-15'),
    status: 'pending',
    description: 'Contrato de servicios profesionales entre ambas partes',
    signingOrder: 'sequential',
    notifications: { enabled: true },
    signers: [
      { id: 1, name: 'María López', status: 'pending', email: 'maria@example.com' }
    ],
    timeline: [
      { type: 'created', title: 'Documento Creado', description: 'El documento ha sido subido', date: new Date('2024-01-15') },
      { type: 'sent', title: 'Enviado para Firma', description: 'Invitación enviada a María López', date: new Date('2024-01-15T10:30:00') }
    ]
  },
  2: {
    id: 2,
    name: 'Acuerdo de Confidencialidad',
    sender: 'Carlos Rodríguez',
    createdAt: new Date('2024-01-14'),
    status: 'in-progress',
    description: 'Acuerdo de confidencialidad y no divulgación',
    signingOrder: 'parallel',
    notifications: { enabled: true },
    signers: [
      { id: 2, name: 'Pedro García', status: 'signed', email: 'pedro@example.com' },
      { id: 3, name: 'Ana Martínez', status: 'pending', email: 'ana@example.com' }
    ],
    timeline: [
      { type: 'created', title: 'Documento Creado', description: 'El documento ha sido subido', date: new Date('2024-01-14') },
      { type: 'sent', title: 'Enviado para Firma', description: 'Invitación enviada a 2 firmantes', date: new Date('2024-01-14T09:00:00') },
      { type: 'signed', title: 'Firmado por Pedro García', description: 'Pedro García ha completado su firma', date: new Date('2024-01-15T11:30:00') }
    ]
  },
  3: {
    id: 3,
    name: 'Propuesta Comercial',
    sender: 'Laura Fernández',
    createdAt: new Date('2024-01-13'),
    status: 'signed',
    description: 'Propuesta comercial para proyecto de desarrollo',
    signingOrder: 'sequential',
    notifications: { enabled: false },
    signers: [
      { id: 4, name: 'Luis Sanchez', status: 'signed', email: 'luis@example.com' },
      { id: 5, name: 'Rosa Díaz', status: 'signed', email: 'rosa@example.com' }
    ],
    timeline: [
      { type: 'created', title: 'Documento Creado', description: 'El documento ha sido subido', date: new Date('2024-01-13') },
      { type: 'sent', title: 'Enviado para Firma', description: 'Invitación enviada', date: new Date('2024-01-13T14:00:00') },
      { type: 'signed', title: 'Firmado por Luis Sanchez', description: 'Luis ha completado su firma', date: new Date('2024-01-14T09:15:00') },
      { type: 'completed', title: 'Documento Completado', description: 'Todos los firmantes han completado', date: new Date('2024-01-15T10:45:00') }
    ]
  }
};

// State
const confirmDialog = ref(false);
const document = ref(mockDocuments[route.params.id] || mockDocuments[1]);

// Computed
const progressPercentage = computed(() => {
  const total = document.value.signers.length;
  const signed = document.value.signers.filter(s => s.status === 'signed').length;
  return total === 0 ? 0 : Math.round((signed / total) * 100);
});

const expirationDate = computed(() => {
  const exp = new Date(document.value.createdAt);
  exp.setDate(exp.getDate() + 30);
  return exp;
});

const isExpired = computed(() => new Date() > expirationDate.value);

const timeline = computed(() => document.value.timeline || []);

// Methods
const formatDate = (date) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return date.toLocaleDateString('es-ES', options);
};

const formatDateTime = (date) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return date.toLocaleDateString('es-ES', options);
};

const getStatusColor = (status) => {
  const colors = {
    'pending': 'warning',
    'in-progress': 'info',
    'signed': 'success',
    'rejected': 'error'
  };
  return colors[status] || 'secondary';
};

const getStatusTextColor = (status) => {
  return status === 'signed' ? 'white' : 'black';
};

const getStatusLabel = (status) => {
  const labels = {
    'pending': 'Pendiente',
    'in-progress': 'En Proceso',
    'signed': 'Completado',
    'rejected': 'Rechazado'
  };
  return labels[status] || status;
};

const getSignerStatusColor = (status) => {
  const colors = {
    'signed': 'success',
    'pending': 'warning',
    'rejected': 'error',
    'viewed': 'info'
  };
  return colors[status] || 'secondary';
};

const getSignerStatusIcon = (status) => {
  const icons = {
    'signed': 'mdi-check',
    'pending': 'mdi-clock-outline',
    'rejected': 'mdi-close',
    'viewed': 'mdi-eye'
  };
  return icons[status] || 'mdi-help';
};

const getSignerStatusLabel = (status) => {
  const labels = {
    'signed': 'Firmado',
    'pending': 'Pendiente',
    'rejected': 'Rechazado',
    'viewed': 'Visto'
  };
  return labels[status] || status;
};

const getEventColor = (type) => {
  const colors = {
    'created': 'primary',
    'sent': 'info',
    'signed': 'success',
    'completed': 'success',
    'rejected': 'error',
    'reminder': 'warning'
  };
  return colors[type] || 'secondary';
};

const getEventIcon = (type) => {
  const icons = {
    'created': 'mdi-plus-circle',
    'sent': 'mdi-send',
    'signed': 'mdi-check-circle',
    'completed': 'mdi-check-all',
    'rejected': 'mdi-close-circle',
    'reminder': 'mdi-bell'
  };
  return icons[type] || 'mdi-circle';
};

const downloadDocument = () => {
  console.log('Descargando documento');
};

const resendReminders = () => {
  confirmDialog.value = {
    title: 'Reenviar Recordatorios',
    message: '¿Deseas reenviar recordatorios a todos los firmantes pendientes?',
    action: 'Reenviar',
    type: 'resend'
  };
};

const cancelDocument = () => {
  confirmDialog.value = {
    title: 'Cancelar Documento',
    message: '¿Estás seguro? Este documento será cancelado y no podrá ser firmado.',
    action: 'Cancelar',
    type: 'cancel'
  };
};

const archiveDocument = () => {
  console.log('Documento archivado');
};

const handleConfirm = () => {
  if (confirmDialog.value.type === 'resend') {
    console.log('Recordatorios reenviados');
  } else if (confirmDialog.value.type === 'cancel') {
    document.value.status = 'rejected';
    console.log('Documento cancelado');
  }
  confirmDialog.value = false;
};
</script>

<style scoped>
.document-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%);
  padding: 2rem 1rem;
  margin: 0;
  margin-top: 100px;
  width: 100%;
  max-width: 100%;
}

@media (min-width: 960px) {
  .document-detail-page {
    padding: 3rem 2rem 4rem 2rem;
  }
}

.info-item {
  padding: 0.75rem 0;
}

.setting-item {
  padding-bottom: 1rem;
}

.pdf-preview {
  background: #0d0d0d;
  border: 2px dashed rgba(212, 175, 55, 0.3);
  border-radius: 8px;
}

.bg-light {
  background: #0d0d0d;
}

.gap-4 {
  gap: 1rem;
}

/* Card Styles */
:deep(.v-card) {
  background: #1a1a1a !important;
  color: #ffffff !important;
}

:deep(.v-card__title) {
  color: #ffffff !important;
}

:deep(.v-card__text) {
  color: #b0b0b0 !important;
}

:deep(.v-divider) {
  border-color: rgba(212, 175, 55, 0.2) !important;
}

:deep(.v-list) {
  background: #1a1a1a !important;
}

:deep(.v-list-item) {
  color: #b0b0b0 !important;
}

:deep(.v-list-item__title) {
  color: #ffffff !important;
}

:deep(.v-list-item__subtitle) {
  color: #888 !important;
}

:deep(.v-list-item:hover) {
  background: rgba(212, 175, 55, 0.1) !important;
}

:deep(.v-chip) {
  background: rgba(212, 175, 55, 0.2) !important;
  color: #d4af37 !important;
}

:deep(.v-timeline) {
  background: #1a1a1a;
}

:deep(.text-medium-emphasis) {
  color: #b0b0b0 !important;
}

:deep(.text-body-2) {
  color: #b0b0b0 !important;
}

:deep(.text-body-1) {
  color: #ffffff !important;
}

:deep(.text-h6) {
  color: #ffffff !important;
}

:deep(.text-h5) {
  color: #888 !important;
}

:deep(.text-h3) {
  color: #ffffff !important;
}

:deep(.v-container) {
  background: transparent;
  padding: 0 2rem;
}

:deep(.v-main) {
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%) !important;
}
</style>
