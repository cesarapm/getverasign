<template>
  <v-container class="signers-page">
    <!-- Header -->
    <section class="signers-hero py-12">
      <v-row justify="center">
        <v-col cols="12" lg="10">
          <div class="signers-shell">
            <div class="signers-copy">
              <span class="signers-kicker">Gestión de Contactos</span>
              <h1 class="text-h3 font-weight-bold mb-2">Mis Firmantes</h1>
              <p class="text-body-1 text-medium-emphasis">
                Administra tu lista de firmantes y contactos recurrentes. Agrupa y organiza los que firman habitualmente tus documentos.
              </p>
            </div>
          </div>
        </v-col>
      </v-row>
    </section>

    <!-- Toolbar -->
    <section class="signers-toolbar mb-8">
      <v-row align="center" class="mb-6">
        <v-col cols="12" md="6">
          <v-text-field
            v-model="searchQuery"
            placeholder="Buscar firmantes..."
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-magnify"
            clearable
            hide-details
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="3">
          <v-select
            v-model="selectedGroup"
            :items="groupOptions"
            label="Grupo"
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
            prepend-icon="mdi-plus"
            elevation="0"
            @click="openAddSignerDialog"
          >
            Nuevo Firmante
          </v-btn>
        </v-col>
      </v-row>

      <!-- Summary -->
      <v-row v-if="filteredSigners.length > 0">
        <v-col cols="12">
          <div class="filters-summary">
            <span class="text-body-2">
              Mostrando <strong>{{ filteredSigners.length }}</strong> de <strong>{{ signers.length }}</strong> firmantes
            </span>
          </div>
        </v-col>
      </v-row>
    </section>

    <!-- Signers Grid / Table Toggle -->
    <v-row class="mb-4">
      <v-col cols="12">
        <div class="d-flex align-center gap-2">
          <v-btn-toggle v-model="viewMode" mandatory class="view-toggle">
            <v-btn icon="mdi-view-grid" value="grid"></v-btn>
            <v-btn icon="mdi-format-list-bulleted" value="list"></v-btn>
          </v-btn-toggle>
        </div>
      </v-col>
    </v-row>

    <!-- Grid View -->
    <v-row v-if="viewMode === 'grid' && filteredSigners.length > 0" class="mb-8">
      <v-col
        v-for="signer in filteredSigners"
        :key="signer.id"
        cols="12"
        sm="6"
        lg="4"
      >
        <v-card class="signer-card h-100" elevation="0" outlined>
          <v-card-text class="pa-6">
            <div class="d-flex align-center mb-4">
              <v-avatar size="56" color="primary" class="text-white font-weight-bold">
                {{ signer.name.charAt(0) }}{{ signer.name.split(' ')[1]?.charAt(0) || '' }}
              </v-avatar>
              <v-spacer></v-spacer>
              <v-menu>
                <template #activator="{ props }">
                  <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                </template>
                <v-list>
                  <v-list-item @click="editSigner(signer)">
                    <template #prepend>
                      <v-icon>mdi-pencil</v-icon>
                    </template>
                    <v-list-item-title>Editar</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="sendTestEmail(signer.id)">
                    <template #prepend>
                      <v-icon>mdi-email-send-outline</v-icon>
                    </template>
                    <v-list-item-title>Enviar Test</v-list-item-title>
                  </v-list-item>
                  <v-divider></v-divider>
                  <v-list-item @click="deleteSigner(signer.id)">
                    <template #prepend>
                      <v-icon color="error">mdi-delete</v-icon>
                    </template>
                    <v-list-item-title color="error">Eliminar</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>

            <div class="signer-info">
              <h3 class="text-body-1 font-weight-bold mb-2">{{ signer.name }}</h3>
              <p class="text-caption text-medium-emphasis mb-3">{{ signer.email }}</p>

              <div class="signer-meta mb-3">
                <v-chip size="small" variant="tonal" class="mr-2">
                  <v-icon start size="small">mdi-briefcase</v-icon>
                  {{ signer.role }}
                </v-chip>
                <v-chip size="small" variant="outlined">
                  {{ signer.group || 'Sin Grupo' }}
                </v-chip>
              </div>

              <div class="signer-stats">
                <div class="stat-item">
                  <span class="text-caption text-medium-emphasis">Documentos Firmados:</span>
                  <strong class="text-body-2">{{ signer.documentsSigned }}</strong>
                </div>
                <div class="stat-item">
                  <span class="text-caption text-medium-emphasis">Documentos Pendientes:</span>
                  <strong class="text-body-2">{{ signer.documentsPending }}</strong>
                </div>
              </div>
            </div>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4">
            <v-btn
              color="primary"
              variant="outlined"
              block
              size="small"
              @click="sendInvitation(signer.id)"
            >
              <v-icon start size="small">mdi-send</v-icon>
              Enviar Invitación
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- List View -->
    <v-card v-if="viewMode === 'list' && filteredSigners.length > 0" elevation="0" outlined class="mb-8">
      <v-data-table
        :headers="headers"
        :items="filteredSigners"
        class="signers-table"
        hover
      >
        <!-- Name column -->
        <template #[`item.name`]="{ item }">
          <div class="d-flex align-center">
            <v-avatar size="40" color="primary" class="text-white mr-3 font-weight-bold">
              {{ item.name.charAt(0) }}{{ item.name.split(' ')[1]?.charAt(0) || '' }}
            </v-avatar>
            <div>
              <div class="font-weight-bold">{{ item.name }}</div>
              <div class="text-caption text-medium-emphasis">{{ item.email }}</div>
            </div>
          </div>
        </template>

        <!-- Role column -->
        <template #[`item.role`]="{ item }">
          <v-chip size="small" variant="tonal">
            {{ item.role }}
          </v-chip>
        </template>

        <!-- Group column -->
        <template #[`item.group`]="{ item }">
          <span>{{ item.group || '-' }}</span>
        </template>

        <!-- Stats column -->
        <template #[`item.stats`]="{ item }">
          <div class="text-center">
            <div class="text-body-2 font-weight-bold">{{ item.documentsSigned }}</div>
            <div class="text-caption text-medium-emphasis">firmados</div>
          </div>
        </template>

        <!-- Actions column -->
        <template #[`item.actions`]="{ item }">
          <div class="d-flex gap-2">
            <v-btn
              icon="mdi-pencil"
              variant="text"
              size="small"
              color="primary"
              @click="editSigner(item)"
            ></v-btn>
            <v-btn
              icon="mdi-send"
              variant="text"
              size="small"
              color="secondary"
              @click="sendInvitation(item.id)"
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
                <v-list-item @click="sendTestEmail(item.id)">
                  <template #prepend>
                    <v-icon>mdi-email-send-outline</v-icon>
                  </template>
                  <v-list-item-title>Enviar Test</v-list-item-title>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item @click="deleteSigner(item.id)">
                  <template #prepend>
                    <v-icon color="error">mdi-delete</v-icon>
                  </template>
                  <v-list-item-title color="error">Eliminar</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
        </template>

        <!-- No data -->
        <template #no-data>
          <div class="pa-8 text-center">
            <v-icon size="64" class="mb-4 text-medium-emphasis">mdi-account-multiple-outline</v-icon>
            <p class="text-body-1 text-medium-emphasis">No tienes firmantes aún</p>
            <v-btn
              color="primary"
              variant="outlined"
              class="mt-4"
              @click="openAddSignerDialog"
            >
              Agregar Primer Firmante
            </v-btn>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Empty State -->
    <v-card v-if="filteredSigners.length === 0" elevation="0" outlined class="pa-8 text-center">
      <v-icon size="80" class="mb-4 text-medium-emphasis">mdi-account-multiple-outline</v-icon>
      <p class="text-body-1 text-medium-emphasis mb-4">
        {{ searchQuery || selectedGroup !== 'all' ? 'No se encontraron firmantes' : 'No tienes firmantes aún' }}
      </p>
      <v-btn
        color="primary"
        variant="outlined"
        @click="openAddSignerDialog"
      >
        Agregar Primer Firmante
      </v-btn>
    </v-card>

    <!-- Add/Edit Signer Dialog -->
    <v-dialog v-model="signerDialog" max-width="500">
      <v-card>
        <v-card-title>
          {{ editingSignerId ? 'Editar Firmante' : 'Nuevo Firmante' }}
        </v-card-title>

        <v-divider></v-divider>

        <v-card-text class="pa-6">
          <v-form ref="signerForm" @submit.prevent="saveSigner">
            <v-text-field
              v-model="currentSigner.name"
              label="Nombre Completo *"
              variant="outlined"
              class="mb-4"
              required
            ></v-text-field>

            <v-text-field
              v-model="currentSigner.email"
              label="Email *"
              type="email"
              variant="outlined"
              class="mb-4"
              required
            ></v-text-field>

            <v-text-field
              v-model="currentSigner.phone"
              label="Teléfono"
              variant="outlined"
              class="mb-4"
            ></v-text-field>

            <v-select
              v-model="currentSigner.role"
              :items="['Firmante', 'CC (Con Copia)', 'Revisor', 'Testigo']"
              label="Rol *"
              variant="outlined"
              class="mb-4"
              required
            ></v-select>

            <v-select
              v-model="currentSigner.group"
              :items="['Equipo Legal', 'Equipo Financiero', 'Directivos', 'Clientes', 'Proveedores']"
              label="Grupo"
              variant="outlined"
              clearable
            ></v-select>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="signerDialog = false">Cancelar</v-btn>
          <v-btn color="primary" variant="tonal" @click="saveSigner">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title>Eliminar Firmante</v-card-title>
        <v-card-text>
          ¿Estás seguro de que deseas eliminar este firmante? Esta acción no se puede deshacer.
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

// State
const searchQuery = ref('');
const selectedGroup = ref('all');
const viewMode = ref('grid');
const signerDialog = ref(false);
const deleteDialog = ref(false);
const editingSignerId = ref(null);
const deleteTargetId = ref(null);

// Mock data
const signers = ref([
  {
    id: 1,
    name: 'María López',
    email: 'maria@example.com',
    phone: '+34 912 345 678',
    role: 'Firmante',
    group: 'Equipo Legal',
    documentsSigned: 15,
    documentsPending: 2,
    lastSigned: new Date('2024-01-15')
  },
  {
    id: 2,
    name: 'Pedro García',
    email: 'pedro@example.com',
    phone: '+34 923 456 789',
    role: 'Revisor',
    group: 'Equipo Financiero',
    documentsSigned: 23,
    documentsPending: 0,
    lastSigned: new Date('2024-01-14')
  },
  {
    id: 3,
    name: 'Ana Martínez',
    email: 'ana@example.com',
    phone: '+34 934 567 890',
    role: 'Firmante',
    group: 'Directivos',
    documentsSigned: 42,
    documentsPending: 5,
    lastSigned: new Date('2024-01-13')
  },
  {
    id: 4,
    name: 'Luis Sánchez',
    email: 'luis@example.com',
    phone: '+34 945 678 901',
    role: 'CC (Con Copia)',
    group: 'Clientes',
    documentsSigned: 8,
    documentsPending: 1,
    lastSigned: new Date('2024-01-12')
  },
  {
    id: 5,
    name: 'Rosa Díaz',
    email: 'rosa@example.com',
    phone: '+34 956 789 012',
    role: 'Testigo',
    group: 'Equipo Legal',
    documentsSigned: 12,
    documentsPending: 3,
    lastSigned: new Date('2024-01-11')
  },
  {
    id: 6,
    name: 'Carlos Hernández',
    email: 'carlos@example.com',
    phone: '+34 967 890 123',
    role: 'Firmante',
    group: 'Proveedores',
    documentsSigned: 5,
    documentsPending: 0,
    lastSigned: new Date('2024-01-10')
  }
]);

const currentSigner = ref({
  name: '',
  email: '',
  phone: '',
  role: 'Firmante',
  group: ''
});

// Options
const groupOptions = [
  { label: 'Todos los Grupos', value: 'all' },
  { label: 'Equipo Legal', value: 'Equipo Legal' },
  { label: 'Equipo Financiero', value: 'Equipo Financiero' },
  { label: 'Directivos', value: 'Directivos' },
  { label: 'Clientes', value: 'Clientes' },
  { label: 'Proveedores', value: 'Proveedores' },
  { label: 'Sin Grupo', value: 'none' }
];

const headers = [
  { title: 'Nombre', key: 'name', sortable: true },
  { title: 'Rol', key: 'role', sortable: true, width: '120' },
  { title: 'Grupo', key: 'group', sortable: true, width: '150' },
  { title: 'Documentos', key: 'stats', sortable: false, width: '100' },
  { title: 'Acciones', key: 'actions', sortable: false, width: '120' }
];

// Computed
const filteredSigners = computed(() => {
  return signers.value.filter(signer => {
    const matchesSearch = signer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         signer.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesGroup = selectedGroup.value === 'all' || 
                        (selectedGroup.value === 'none' ? !signer.group : signer.group === selectedGroup.value);
    return matchesSearch && matchesGroup;
  });
});

// Methods
const openAddSignerDialog = () => {
  editingSignerId.value = null;
  currentSigner.value = { name: '', email: '', phone: '', role: 'Firmante', group: '' };
  signerDialog.value = true;
};

const editSigner = (signer) => {
  editingSignerId.value = signer.id;
  currentSigner.value = { ...signer };
  signerDialog.value = true;
};

const saveSigner = () => {
  if (editingSignerId.value) {
    const index = signers.value.findIndex(s => s.id === editingSignerId.value);
    if (index > -1) {
      signers.value[index] = { ...signers.value[index], ...currentSigner.value };
    }
  } else {
    const newId = Math.max(...signers.value.map(s => s.id), 0) + 1;
    signers.value.push({
      id: newId,
      ...currentSigner.value,
      documentsSigned: 0,
      documentsPending: 0,
      lastSigned: new Date()
    });
  }
  signerDialog.value = false;
};

const deleteSigner = (id) => {
  deleteTargetId.value = id;
  deleteDialog.value = true;
};

const confirmDelete = () => {
  const index = signers.value.findIndex(s => s.id === deleteTargetId.value);
  if (index > -1) {
    signers.value.splice(index, 1);
  }
  deleteDialog.value = false;
  deleteTargetId.value = null;
};

const sendInvitation = (id) => {
  console.log('Enviar invitación a:', id);
};

const sendTestEmail = (id) => {
  console.log('Enviar email de prueba a:', id);
};
</script>

<style scoped>
.signers-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%);
  padding: 2rem 1rem;
  margin: 0;
  margin-top: 100px;
  width: 100%;
  max-width: 100%;
}

@media (min-width: 960px) {
  .signers-page {
    padding: 3rem 2rem;
  }
}

.signers-hero {
  background: #1a1a1a;
  border-radius: 12px;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.signers-kicker {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #b0b0b0;
  margin-bottom: 0.5rem;
}

.signers-toolbar {
  background: #1a1a1a;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.filters-summary {
  padding: 1rem;
  background: #0d0d0d;
  border-radius: 8px;
  border: 1px solid rgba(212, 175, 55, 0.15);
}

.signer-card {
  border-radius: 12px;
  transition: all 0.3s ease;
  background: #1a1a1a;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.signer-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(212, 175, 55, 0.2);
  border-color: #d4af37;
}

.signer-info {
  padding: 0.5rem 0;
}

.signer-meta {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.signer-stats {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.view-toggle {
  background: #1a1a1a;
  border-radius: 8px;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

:deep(.signers-table) {
  background: #1a1a1a;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

:deep(.signers-table .v-data-table__tr) {
  transition: all 0.2s ease;
}

:deep(.signers-table .v-data-table__tr:hover) {
  background-color: #0d0d0d !important;
}

.gap-2 {
  gap: 0.5rem;
}

.h-100 {
  height: 100%;
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
