<template>
  <v-container class="dashboard-page">
    <!-- Header -->
    <section class="dashboard-header py-8">
      <v-row align="center">
        <v-col cols="12">
          <div class="dashboard-title">
            <h1 class="text-h3 font-weight-bold">Dashboard</h1>
            <p class="text-body-2 text-medium-emphasis mt-2">
              Bienvenido a tu plataforma de gestión de firmas electrónicas
            </p>
          </div>
        </v-col>
      </v-row>
    </section>

    <!-- Estadísticas Cards -->
    <section class="statistics-section mb-12">
      <v-row>
        <!-- Documentos Pendientes -->
        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card stat-card--pending" elevation="0">
            <v-card-text class="pa-6">
              <div class="stat-header">
                <v-icon class="stat-icon stat-icon--pending">mdi-file-document-outline</v-icon>
              </div>
              <div class="stat-content">
                <div class="stat-value">{{ pendingCount }}</div>
                <div class="stat-label">Documentos Pendientes</div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Documentos en Proceso -->
        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card stat-card--progress" elevation="0">
            <v-card-text class="pa-6">
              <div class="stat-header">
                <v-icon class="stat-icon stat-icon--progress">mdi-file-document-clock-outline</v-icon>
              </div>
              <div class="stat-content">
                <div class="stat-value">{{ inProgressCount }}</div>
                <div class="stat-label">En Proceso</div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Documentos Firmados -->
        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card stat-card--signed" elevation="0">
            <v-card-text class="pa-6">
              <div class="stat-header">
                <v-icon class="stat-icon stat-icon--signed">mdi-file-document-check-outline</v-icon>
              </div>
              <div class="stat-content">
                <div class="stat-value">{{ signedCount }}</div>
                <div class="stat-label">Documentos Firmados</div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Documentos Rechazados -->
        <v-col cols="12" sm="6" md="3">
          <v-card class="stat-card stat-card--rejected" elevation="0">
            <v-card-text class="pa-6">
              <div class="stat-header">
                <v-icon class="stat-icon stat-icon--rejected">mdi-file-document-alert-outline</v-icon>
              </div>
              <div class="stat-content">
                <div class="stat-value">{{ rejectedCount }}</div>
                <div class="stat-label">Documentos Rechazados</div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </section>

    <!-- Main Content -->
    <v-row>
      <!-- Documentos Pendientes de Firma -->
      <v-col cols="12" lg="8">
        <v-card class="mb-8" elevation="0" outlined>
          <v-card-title class="pa-6 d-flex align-center">
            <span class="text-h6">Documentos Pendientes de Firma</span>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              variant="text"
              :to="{ name: 'DocumentList', query: { status: 'pending' } }"
            >
              Ver todos
              <v-icon end>mdi-chevron-right</v-icon>
            </v-btn>
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text class="pa-0">
            <v-list v-if="pendingDocuments.length > 0" class="pa-0">
              <template v-for="(doc, idx) in pendingDocuments.slice(0, 5)" :key="doc.id">
                <v-list-item
                  :to="{ name: 'DocumentDetail', params: { id: doc.id } }"
                  class="px-6 py-4 cursor-pointer hover:bg-gray-50"
                >
                  <template #prepend>
                    <v-icon class="mr-4" color="warning">mdi-file-document</v-icon>
                  </template>

                  <v-list-item-title>{{ doc.name }}</v-list-item-title>
                  <v-list-item-subtitle>
                    <span class="text-body-2">Enviado por: {{ doc.sender }}</span>
                    <span class="ml-4 text-body-2 text-medium-emphasis">
                      {{ formatDate(doc.createdAt) }}
                    </span>
                  </v-list-item-subtitle>

                  <template #append>
                    <div class="text-right">
                      <v-chip
                        size="small"
                        color="warning"
                        variant="tonal"
                        label
                        class="mb-1"
                      >
                        Pendiente
                      </v-chip>
                      <div class="text-body-2 text-medium-emphasis">
                        {{ doc.signers.length }} {{ doc.signers.length === 1 ? 'firmante' : 'firmantes' }}
                      </div>
                    </div>
                  </template>
                </v-list-item>

                <v-divider v-if="idx < pendingDocuments.slice(0, 5).length - 1"></v-divider>
              </template>
            </v-list>
            <div v-else class="pa-8 text-center">
              <v-icon size="48" class="mb-4 text-medium-emphasis">mdi-file-document-outline</v-icon>
              <p class="text-body-2 text-medium-emphasis">No hay documentos pendientes</p>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Actividad Reciente -->
      <v-col cols="12" lg="4">
        <v-card elevation="0" outlined>
          <v-card-title class="pa-6">
            <span class="text-h6">Actividad Reciente</span>
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text class="pa-0">
            <v-timeline
              v-if="recentActivity.length > 0"
              side="start"
              density="compact"
              class="pa-4"
            >
              <v-timeline-item
                v-for="(activity, idx) in recentActivity.slice(0, 6)"
                :key="idx"
                :dot-color="getActivityColor(activity.type)"
                :icon="getActivityIcon(activity.type)"
                size="small"
              >
                <div class="text-body-2">
                  <strong>{{ activity.title }}</strong>
                  <div class="text-caption text-medium-emphasis">
                    {{ formatDate(activity.date) }}
                  </div>
                  <p class="mt-1 text-body-2">{{ activity.description }}</p>
                </div>
              </v-timeline-item>
            </v-timeline>
            <div v-else class="pa-8 text-center">
              <v-icon size="48" class="mb-4 text-medium-emphasis">mdi-history</v-icon>
              <p class="text-body-2 text-medium-emphasis">Sin actividad reciente</p>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Acciones Rápidas -->
    <section class="quick-actions-section mt-12">
      <v-row>
        <v-col cols="12">
          <h2 class="text-h6 mb-6">Acciones Rápidas</h2>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" sm="6" md="3">
          <v-card
            class="action-card cursor-pointer"
            elevation="0"
            outlined
            :to="{ name: 'NewDocument' }"
          >
            <v-card-text class="pa-6 text-center">
              <v-icon size="40" class="mb-4 text-primary">mdi-file-plus-outline</v-icon>
              <div class="text-body-2 font-weight-bold">Nuevo Documento</div>
              <p class="text-caption text-medium-emphasis mt-2">
                Cargar y configurar un nuevo documento
              </p>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card
            class="action-card cursor-pointer"
            elevation="0"
            outlined
            :to="{ name: 'DocumentList' }"
          >
            <v-card-text class="pa-6 text-center">
              <v-icon size="40" class="mb-4 text-info">mdi-file-multiple-outline</v-icon>
              <div class="text-body-2 font-weight-bold">Mis Documentos</div>
              <p class="text-caption text-medium-emphasis mt-2">
                Gestionar todos tus documentos
              </p>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card
            class="action-card cursor-pointer"
            elevation="0"
            outlined
            :to="{ name: 'Signers' }"
          >
            <v-card-text class="pa-6 text-center">
              <v-icon size="40" class="mb-4 text-success">mdi-account-multiple-outline</v-icon>
              <div class="text-body-2 font-weight-bold">Firmantes</div>
              <p class="text-caption text-medium-emphasis mt-2">
                Administrar tus firmantes
              </p>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card
            class="action-card cursor-pointer"
            elevation="0"
            outlined
            :to="{ name: 'Settings' }"
          >
            <v-card-text class="pa-6 text-center">
              <v-icon size="40" class="mb-4 text-secondary">mdi-cog-outline</v-icon>
              <div class="text-body-2 font-weight-bold">Configuración</div>
              <p class="text-caption text-medium-emphasis mt-2">
                Ajustar preferencias
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </section>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue';

// Mock data
const documents = ref([
  {
    id: 1,
    name: 'Contrato de Servicios',
    sender: 'Juan González',
    createdAt: new Date('2024-01-15'),
    status: 'pending',
    signers: [{ id: 1, name: 'María López', status: 'pending' }]
  },
  {
    id: 2,
    name: 'Acuerdo de Confidencialidad',
    sender: 'Carlos Rodríguez',
    createdAt: new Date('2024-01-14'),
    status: 'pending',
    signers: [
      { id: 2, name: 'Pedro García', status: 'pending' },
      { id: 3, name: 'Ana Martínez', status: 'pending' }
    ]
  },
  {
    id: 3,
    name: 'Propuesta Comercial',
    sender: 'Laura Fernández',
    createdAt: new Date('2024-01-13'),
    status: 'in-progress',
    signers: [
      { id: 4, name: 'Luis Sanchez', status: 'signed' },
      { id: 5, name: 'Rosa Díaz', status: 'pending' }
    ]
  },
  {
    id: 4,
    name: 'Documento Firmado',
    sender: 'Miguel Hernández',
    createdAt: new Date('2024-01-12'),
    status: 'signed',
    signers: [{ id: 6, name: 'Isabel Moreno', status: 'signed' }]
  },
  {
    id: 5,
    name: 'Solicitud Rechazada',
    sender: 'José López',
    createdAt: new Date('2024-01-11'),
    status: 'rejected',
    signers: [{ id: 7, name: 'Fernando Torres', status: 'rejected' }]
  },
  {
    id: 6,
    name: 'Poder Notarial',
    sender: 'Elena García',
    createdAt: new Date('2024-01-10'),
    status: 'pending',
    signers: [
      { id: 8, name: 'Raúl Domínguez', status: 'pending' },
      { id: 9, name: 'Patricia Ruiz', status: 'pending' },
      { id: 10, name: 'Oscar Guerrero', status: 'pending' }
    ]
  },
  {
    id: 7,
    name: 'Factura Electrónica',
    sender: 'Andrés Márquez',
    createdAt: new Date('2024-01-09'),
    status: 'signed',
    signers: [{ id: 11, name: 'Sofía Reyes', status: 'signed' }]
  }
]);

// Computed properties
const pendingCount = computed(() => documents.value.filter(d => d.status === 'pending').length);
const inProgressCount = computed(() => documents.value.filter(d => d.status === 'in-progress').length);
const signedCount = computed(() => documents.value.filter(d => d.status === 'signed').length);
const rejectedCount = computed(() => documents.value.filter(d => d.status === 'rejected').length);

const pendingDocuments = computed(() => 
  documents.value.filter(d => d.status === 'pending' || d.status === 'in-progress')
);

const recentActivity = computed(() => {
  const activities = [];
  documents.value.forEach(doc => {
    if (doc.status === 'signed') {
      activities.push({
        type: 'signed',
        title: 'Documento Completado',
        description: `"${doc.name}" ha sido completamente firmado`,
        date: doc.createdAt
      });
    }
    if (doc.status === 'in-progress') {
      activities.push({
        type: 'in-progress',
        title: 'Documento en Firma',
        description: `"${doc.name}" está en proceso de firma`,
        date: doc.createdAt
      });
    }
    if (doc.status === 'pending') {
      activities.push({
        type: 'pending',
        title: 'Nuevo Documento',
        description: `"${doc.name}" enviado por ${doc.sender}`,
        date: doc.createdAt
      });
    }
  });
  return activities.sort((a, b) => b.date - a.date);
});

// Methods
const formatDate = (date) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return date.toLocaleDateString('es-ES', options);
};

const getActivityColor = (type) => {
  const colors = {
    'signed': 'success',
    'in-progress': 'warning',
    'pending': 'info',
    'rejected': 'error'
  };
  return colors[type] || 'primary';
};

const getActivityIcon = (type) => {
  const icons = {
    'signed': 'mdi-file-document-check',
    'in-progress': 'mdi-file-document-clock',
    'pending': 'mdi-file-document',
    'rejected': 'mdi-file-document-alert'
  };
  return icons[type] || 'mdi-file-document';
};
</script>

<style scoped>
.dashboard-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%);
  padding: 2rem 1rem;
  margin: 0;
  margin-top: 100px;
  width: 100%;
  max-width: 100%;
}

@media (min-width: 960px) {
  .dashboard-page {
    padding: 3rem 2rem;
  }
}

.dashboard-header {
  background: #1a1a1a;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 3rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.dashboard-header :deep(.text-h3) {
  color: #ffffff !important;
}

.dashboard-header .text-medium-emphasis {
  color: #b0b0b0 !important;
}

.statistics-section {
  margin-bottom: 3rem;
}

.stat-card {
  border-radius: 12px;
  background: #1a1a1a;
  transition: all 0.3s ease;
  border: 1px solid rgba(212, 175, 55, 0.2) !important;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.stat-header {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.stat-icon {
  font-size: 2.5rem;
  border-radius: 12px;
  padding: 0.75rem;
  background: rgba(212, 175, 55, 0.1);
}

.stat-icon--pending {
  color: #d4af37;
  background: rgba(212, 175, 55, 0.15);
}

.stat-icon--progress {
  color: #42a5f5;
  background: rgba(66, 165, 245, 0.1);
}

.stat-icon--signed {
  color: #66bb6a;
  background: rgba(102, 187, 106, 0.1);
}

.stat-icon--rejected {
  color: #ef5350;
  background: rgba(239, 83, 80, 0.1);
}

.stat-content {
  text-align: center;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #ffffff;
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  color: #b0b0b0;
  margin-top: 0.5rem;
}

.action-card {
  border-radius: 12px;
  transition: all 0.3s ease;
  background: #1a1a1a;
  border: 1px solid rgba(212, 175, 55, 0.2) !important;
}

.action-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(212, 175, 55, 0.2);
  border-color: #d4af37 !important;
}

.cursor-pointer {
  cursor: pointer;
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

:deep(.text-h6) {
  color: #ffffff !important;
}

:deep(.text-h3) {
  color: #ffffff !important;
}

:deep(.v-container) {
  background: transparent;
  padding: 0 2rem;
}

/* Main Vuetify Component Overrides */
:deep(.v-main) {
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%) !important;
}
</style>
