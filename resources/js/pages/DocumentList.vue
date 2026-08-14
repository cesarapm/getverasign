<template>
  <v-container class="document-list-page">
    <!-- Header -->
    <section class="documents-hero py-12">
      <v-row justify="center">
        <v-col cols="12" lg="10">
          <div class="documents-shell">
            <div class="documents-copy">
              <span class="documents-kicker">Gestión de Documentos</span>
              <h1 class="text-h3 font-weight-bold mb-2">Mis Documentos</h1>
              <p class="text-body-1 text-medium-emphasis">
                Administra y supervisa todos tus documentos en un solo lugar. Controla el estado de cada firma y recibe notificaciones en tiempo real.
              </p>
            </div>
          </div>
        </v-col>
      </v-row>
    </section>

    <!-- Filters and Actions -->
    <section class="filters-section mb-8">
      <v-row align="center" class="mb-6">
        <v-col cols="12" md="6">
          <v-text-field
            v-model="searchQuery"
            placeholder="Buscar documentos..."
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-magnify"
            clearable
            hide-details
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="3">
          <v-select
            v-model="selectedStatus"
            :items="statusOptions"
            label="Estado"
            variant="outlined"
            density="comfortable"
            hide-details
            item-title="label"
            item-value="value"
          ></v-select>
        </v-col>
        <v-col cols="12" md="3" class="text-right">
          <v-btn
            color="primary"
            :to="{ name: 'NewDocument' }"
            prepend-icon="mdi-plus"
            elevation="0"
          >
            Nuevo Documento
          </v-btn>
        </v-col>
      </v-row>

      <!-- Summary -->
      <v-row v-if="filteredDocuments.length > 0">
        <v-col cols="12">
          <div class="filters-summary">
            <span class="text-body-2">
              Mostrando <strong>{{ filteredDocuments.length }}</strong> de <strong>{{ documents.length }}</strong> documentos
            </span>
          </div>
        </v-col>
      </v-row>
    </section>

    <!-- Documents Table -->
    <section class="documents-table-section">
      <v-card elevation="0" outlined class="rounded-lg">
        <v-data-table
          :headers="headers"
          :items="filteredDocuments"
          :loading="loading"
          class="documents-table"
          hover
        >
          <!-- Name column -->
          <template #[`item.name`]="{ item }">
            <div class="d-flex align-center">
              <v-icon class="mr-3" size="24">mdi-file-document-outline</v-icon>
              <div>
                <div class="font-weight-bold cursor-pointer text-primary" @click="goToDetail(item.id)">
                  {{ item.name }}
                </div>
                <div class="text-caption text-medium-emphasis">
                  ID: {{ item.id }}
                </div>
              </div>
            </div>
          </template>

          <!-- Status column -->
          <template #[`item.status`]="{ item }">
            <v-chip
              :color="getStatusColor(item.status)"
              :text-color="getStatusTextColor(item.status)"
              variant="tonal"
              size="small"
              label
            >
              {{ getStatusLabel(item.status) }}
            </v-chip>
          </template>

          <!-- Signers column -->
          <template #[`item.signers`]="{ item }">
            <div class="text-body-2">
              <div class="font-weight-bold">{{ item.signers.filter(s => s.status === 'signed').length }} / {{ item.signers.length }}</div>
              <div class="text-caption text-medium-emphasis">firmados</div>
            </div>
          </template>

          <!-- Progress column -->
          <template #[`item.progress`]="{ item }">
            <div class="d-flex align-center gap-2">
              <v-progress-linear
                :value="getProgressPercentage(item)"
                height="6"
                rounded
                class="flex-grow-1"
                :color="getStatusColor(item.status)"
              ></v-progress-linear>
              <span class="text-caption">{{ getProgressPercentage(item) }}%</span>
            </div>
          </template>

          <!-- Date column -->
          <template #[`item.createdAt`]="{ item }">
            <div class="text-body-2">
              {{ formatDate(item.createdAt) }}
            </div>
          </template>

          <!-- Actions column -->
          <template #[`item.actions`]="{ item }">
            <div class="d-flex gap-2">
              <v-btn
                icon="mdi-eye"
                variant="text"
                size="small"
                color="primary"
                :to="{ name: 'DocumentDetail', params: { id: item.id } }"
              ></v-btn>
              <v-btn
                icon="mdi-download"
                variant="text"
                size="small"
                color="secondary"
                @click="downloadDocument(item.id)"
              ></v-btn>
              <v-menu>
                <template #activator="{ props }">
                  <v-btn
                    icon="mdi-dots-vertical"
                    variant="text"
                    size="small"
                    v-bind="props"
                  ></v-btn>
                </template>
                <v-list>
                  <v-list-item @click="editDocument(item.id)">
                    <template #prepend>
                      <v-icon>mdi-pencil</v-icon>
                    </template>
                    <v-list-item-title>Editar</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="duplicateDocument(item.id)">
                    <template #prepend>
                      <v-icon>mdi-content-copy</v-icon>
                    </template>
                    <v-list-item-title>Duplicar</v-list-item-title>
                  </v-list-item>
                  <v-divider></v-divider>
                  <v-list-item @click="deleteDocument(item.id)">
                    <template #prepend>
                      <v-icon color="error">mdi-delete</v-icon>
                    </template>
                    <v-list-item-title color="error">Eliminar</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>
          </template>

          <!-- Empty state -->
          <template #no-data>
            <div class="pa-8 text-center">
              <v-icon size="64" class="mb-4 text-medium-emphasis">mdi-file-document-outline</v-icon>
              <p class="text-body-1 text-medium-emphasis">
                {{ searchQuery || selectedStatus !== 'all' ? 'No se encontraron documentos' : 'No tienes documentos aún' }}
              </p>
              <v-btn
                color="primary"
                variant="outlined"
                class="mt-4"
                :to="{ name: 'NewDocument' }"
              >
                Crear Primer Documento
              </v-btn>
            </div>
          </template>
        </v-data-table>
      </v-card>
    </section>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title>Eliminar Documento</v-card-title>
        <v-card-text>
          ¿Estás seguro de que deseas eliminar este documento? Esta acción no se puede deshacer.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Cancelar</v-btn>
          <v-btn color="error" variant="tonal" @click="confirmDelete">Eliminar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// State
const searchQuery = ref('');
const selectedStatus = ref('all');
const loading = ref(false);
const deleteDialog = ref(false);
const deleteTarget = ref(null);

// Mock data
const documents = ref([
  {
    id: 1,
    name: 'Contrato de Servicios',
    sender: 'Juan González',
    createdAt: new Date('2024-01-15'),
    status: 'pending',
    signers: [
      { id: 1, name: 'María López', status: 'pending', email: 'maria@example.com' }
    ]
  },
  {
    id: 2,
    name: 'Acuerdo de Confidencialidad',
    sender: 'Carlos Rodríguez',
    createdAt: new Date('2024-01-14'),
    status: 'in-progress',
    signers: [
      { id: 2, name: 'Pedro García', status: 'signed', email: 'pedro@example.com' },
      { id: 3, name: 'Ana Martínez', status: 'pending', email: 'ana@example.com' }
    ]
  },
  {
    id: 3,
    name: 'Propuesta Comercial',
    sender: 'Laura Fernández',
    createdAt: new Date('2024-01-13'),
    status: 'in-progress',
    signers: [
      { id: 4, name: 'Luis Sanchez', status: 'signed', email: 'luis@example.com' },
      { id: 5, name: 'Rosa Díaz', status: 'pending', email: 'rosa@example.com' }
    ]
  },
  {
    id: 4,
    name: 'Documento Completamente Firmado',
    sender: 'Miguel Hernández',
    createdAt: new Date('2024-01-12'),
    status: 'signed',
    signers: [
      { id: 6, name: 'Isabel Moreno', status: 'signed', email: 'isabel@example.com' },
      { id: 7, name: 'Fernando Torres', status: 'signed', email: 'fernando@example.com' }
    ]
  },
  {
    id: 5,
    name: 'Solicitud Rechazada',
    sender: 'José López',
    createdAt: new Date('2024-01-11'),
    status: 'rejected',
    signers: [
      { id: 8, name: 'Fernando Torres', status: 'rejected', email: 'fernando@example.com' }
    ]
  },
  {
    id: 6,
    name: 'Poder Notarial',
    sender: 'Elena García',
    createdAt: new Date('2024-01-10'),
    status: 'pending',
    signers: [
      { id: 9, name: 'Raúl Domínguez', status: 'pending', email: 'raul@example.com' },
      { id: 10, name: 'Patricia Ruiz', status: 'pending', email: 'patricia@example.com' },
      { id: 11, name: 'Oscar Guerrero', status: 'pending', email: 'oscar@example.com' }
    ]
  },
  {
    id: 7,
    name: 'Factura Electrónica',
    sender: 'Andrés Márquez',
    createdAt: new Date('2024-01-09'),
    status: 'signed',
    signers: [
      { id: 12, name: 'Sofía Reyes', status: 'signed', email: 'sofia@example.com' }
    ]
  },
  {
    id: 8,
    name: 'Presupuesto Aprobado',
    sender: 'Roberto García',
    createdAt: new Date('2024-01-08'),
    status: 'pending',
    signers: [
      { id: 13, name: 'Alejandro López', status: 'pending', email: 'alejandro@example.com' },
      { id: 14, name: 'Sandra Morales', status: 'pending', email: 'sandra@example.com' }
    ]
  }
]);

// Table configuration
const headers = [
  { title: 'Documento', key: 'name', sortable: true },
  { title: 'Estado', key: 'status', sortable: true },
  { title: 'Firmas', key: 'signers', sortable: false, width: '100' },
  { title: 'Progreso', key: 'progress', sortable: false, width: '150' },
  { title: 'Fecha', key: 'createdAt', sortable: true, width: '120' },
  { title: 'Acciones', key: 'actions', sortable: false, width: '120' }
];

const statusOptions = [
  { label: 'Todos', value: 'all' },
  { label: 'Pendiente', value: 'pending' },
  { label: 'En Proceso', value: 'in-progress' },
  { label: 'Firmado', value: 'signed' },
  { label: 'Rechazado', value: 'rejected' }
];

// Computed
const filteredDocuments = computed(() => {
  return documents.value.filter(doc => {
    const matchesSearch = doc.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         doc.sender.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = selectedStatus.value === 'all' || doc.status === selectedStatus.value;
    return matchesSearch && matchesStatus;
  });
});

// Methods
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

const getProgressPercentage = (doc) => {
  const total = doc.signers.length;
  const signed = doc.signers.filter(s => s.status === 'signed').length;
  return total === 0 ? 0 : Math.round((signed / total) * 100);
};

const formatDate = (date) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return date.toLocaleDateString('es-ES', options);
};

const goToDetail = (id) => {
  router.push({ name: 'DocumentDetail', params: { id } });
};

const downloadDocument = (id) => {
  console.log('Descargar documento:', id);
  // Implementar descarga de PDF
};

const editDocument = (id) => {
  console.log('Editar documento:', id);
  // Implementar edición
};

const duplicateDocument = (id) => {
  const docToDuplicate = documents.value.find(d => d.id === id);
  if (docToDuplicate) {
    const newDoc = {
      ...JSON.parse(JSON.stringify(docToDuplicate)),
      id: Math.max(...documents.value.map(d => d.id)) + 1,
      name: `${docToDuplicate.name} (Copia)`,
      createdAt: new Date()
    };
    documents.value.unshift(newDoc);
  }
};

const deleteDocument = (id) => {
  deleteTarget.value = id;
  deleteDialog.value = true;
};

const confirmDelete = () => {
  const index = documents.value.findIndex(d => d.id === deleteTarget.value);
  if (index > -1) {
    documents.value.splice(index, 1);
  }
  deleteDialog.value = false;
  deleteTarget.value = null;
};
</script>

<style scoped>
.document-list-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%);
  padding: 2rem 1rem;
  margin: 0;
  margin-top: 100px;
  width: 100%;
  max-width: 100%;
}

@media (min-width: 960px) {
  .document-list-page {
    padding: 3rem 2rem;
  }
}

.documents-hero {
  background: #1a1a1a;
  border-radius: 12px;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.documents-kicker {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #b0b0b0;
  margin-bottom: 0.5rem;
}

.filters-section {
  background: #1a1a1a;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.filters-summary {
  padding: 1rem;
  background: #0d0d0d;
  border-radius: 8px;
  border: 1px solid rgba(212, 175, 55, 0.15);
}

:deep(.documents-table .v-data-table__tr) {
  transition: all 0.2s ease;
}

:deep(.documents-table .v-data-table__tr:hover) {
  background-color: #0d0d0d !important;
}

:deep(.documents-table) {
  background: #1a1a1a;
  border: 1px solid rgba(212, 175, 55, 0.2);
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
