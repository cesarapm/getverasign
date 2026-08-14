<template>
  <v-container class="new-document-page">
    <!-- Header -->
    <section class="new-document-header py-12">
      <v-row justify="center">
        <v-col cols="12" lg="10">
          <div class="document-wizard-header">
            <h1 class="text-h3 font-weight-bold mb-2">Nuevo Documento</h1>
            <p class="text-body-1 text-medium-emphasis mb-8">
              Sube un documento PDF y configura los firmantes que necesitan autorizar tu documento.
            </p>

            <!-- Stepper -->
            <v-stepper v-model="currentStep" non-linear class="document-stepper">
              <v-stepper-header>
                <v-stepper-item
                  :complete="currentStep > 1"
                  :value="1"
                  @click="currentStep = 1"
                  class="stepper-item"
                >
                  <template #title>
                    <span class="stepper-title">Documento</span>
                  </template>
                </v-stepper-item>

                <v-divider></v-divider>

                <v-stepper-item
                  :complete="currentStep > 2"
                  :value="2"
                  :rules="[() => form.pdfFile !== null]"
                  @click="currentStep = 2"
                  class="stepper-item"
                >
                  <template #title>
                    <span class="stepper-title">Firmantes</span>
                  </template>
                </v-stepper-item>

                <v-divider></v-divider>

                <v-stepper-item
                  :complete="currentStep > 3"
                  :value="3"
                  :rules="[() => form.signers.length > 0]"
                  @click="currentStep = 3"
                  class="stepper-item"
                >
                  <template #title>
                    <span class="stepper-title">Configuración</span>
                  </template>
                </v-stepper-item>
              </v-stepper-header>

              <!-- Step 1: Document Upload -->
              <v-stepper-window>
                <v-stepper-window-item :value="1">
                  <v-card class="document-step pa-8" flat>
                    <div class="step-content">
                      <div class="step-title mb-8">
                        <h2 class="text-h6">Cargar Documento</h2>
                        <p class="text-body-2 text-medium-emphasis mt-2">
                          Selecciona un archivo PDF de tu computadora. Máximo 25MB.
                        </p>
                      </div>

                      <!-- File Upload Area -->
                      <div class="file-upload-area mb-8">
                        <input
                          ref="fileInput"
                          type="file"
                          accept=".pdf"
                          style="display: none"
                          @change="handleFileSelect"
                        />

                        <div
                          v-if="!form.pdfFile"
                          class="upload-zone"
                          @click="$refs.fileInput?.click()"
                          @dragover.prevent="isDragging = true"
                          @dragleave.prevent="isDragging = false"
                          @drop.prevent="handleFileDrop"
                        >
                          <v-icon size="64" class="mb-4 text-medium-emphasis">
                            mdi-cloud-upload-outline
                          </v-icon>
                          <h3 class="text-h6 mb-2">Arrastra tu PDF aquí</h3>
                          <p class="text-body-2 text-medium-emphasis mb-4">
                            o haz clic para seleccionar un archivo
                          </p>
                          <v-btn color="primary" variant="outlined">
                            Seleccionar archivo
                          </v-btn>
                        </div>

                        <div v-else class="file-selected pa-6">
                          <v-icon size="48" color="success" class="mb-4">
                            mdi-file-pdf-box
                          </v-icon>
                          <div class="font-weight-bold mb-2">{{ form.pdfFile.name }}</div>
                          <div class="text-caption text-medium-emphasis mb-4">
                            {{ formatFileSize(form.pdfFile.size) }}
                          </div>
                          <v-btn
                            color="error"
                            variant="text"
                            size="small"
                            @click="clearFile"
                          >
                            Cambiar archivo
                          </v-btn>
                        </div>
                      </div>

                      <!-- Document Name -->
                      <div class="mb-8">
                        <v-text-field
                          v-model="form.documentName"
                          label="Nombre del Documento *"
                          placeholder="Ej: Contrato de Servicios 2024"
                          variant="outlined"
                          hint="Este nombre será visible para todos los firmantes"
                          persistent-hint
                        ></v-text-field>
                      </div>

                      <!-- Document Description -->
                      <div class="mb-8">
                        <v-textarea
                          v-model="form.description"
                          label="Descripción (Opcional)"
                          placeholder="Agrega detalles adicionales sobre este documento"
                          variant="outlined"
                          rows="4"
                          hint="Esta descripción es solo para referencia interna"
                          persistent-hint
                        ></v-textarea>
                      </div>

                      <!-- Step Actions -->
                      <div class="step-actions">
                        <v-btn
                          color="primary"
                          :disabled="!form.pdfFile || !form.documentName"
                          @click="currentStep = 2"
                          class="px-8"
                        >
                          Continuar
                          <v-icon end>mdi-chevron-right</v-icon>
                        </v-btn>
                      </div>
                    </div>
                  </v-card>
                </v-stepper-window-item>

                <!-- Step 2: Add Signers -->
                <v-stepper-window-item :value="2">
                  <v-card class="document-step pa-8" flat>
                    <div class="step-content">
                      <div class="step-title mb-8">
                        <h2 class="text-h6">Agregar Firmantes</h2>
                        <p class="text-body-2 text-medium-emphasis mt-2">
                          Define quiénes deben firmar este documento y en qué orden.
                        </p>
                      </div>

                      <!-- Signer Form -->
                      <div class="signer-form mb-8 pa-6 bg-light rounded-lg">
                        <v-row class="mb-6">
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model="newSigner.name"
                              label="Nombre *"
                              variant="outlined"
                              density="comfortable"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model="newSigner.email"
                              label="Email *"
                              type="email"
                              variant="outlined"
                              density="comfortable"
                            ></v-text-field>
                          </v-col>
                        </v-row>

                        <v-row class="mb-6">
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model="newSigner.phone"
                              label="Teléfono (Opcional)"
                              variant="outlined"
                              density="comfortable"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-select
                              v-model="newSigner.role"
                              :items="['Firmante', 'CC (Con Copia)', 'Revisor']"
                              label="Rol *"
                              variant="outlined"
                              density="comfortable"
                            ></v-select>
                          </v-col>
                        </v-row>

                        <div class="text-right">
                          <v-btn
                            color="primary"
                            variant="outlined"
                            @click="addSigner"
                            :disabled="!newSigner.name || !newSigner.email || !newSigner.role"
                            class="px-6"
                          >
                            <v-icon start>mdi-plus</v-icon>
                            Agregar Firmante
                          </v-btn>
                        </div>
                      </div>

                      <!-- Signers List -->
                      <div v-if="form.signers.length > 0" class="mb-8">
                        <h3 class="text-body-1 font-weight-bold mb-4">
                          Firmantes Agregados ({{ form.signers.length }})
                        </h3>
                        <v-list class="signer-list">
                          <template v-for="(signer, idx) in form.signers" :key="idx">
                            <v-list-item class="mb-2">
                              <template #prepend>
                                <v-avatar size="40" color="primary" class="text-white">
                                  {{ idx + 1 }}
                                </v-avatar>
                              </template>

                              <v-list-item-title>{{ signer.name }}</v-list-item-title>
                              <v-list-item-subtitle>
                                {{ signer.email }} • {{ signer.role }}
                              </v-list-item-subtitle>

                              <template #append>
                                <div class="d-flex gap-2">
                                  <v-btn
                                    v-if="idx > 0"
                                    icon="mdi-arrow-up"
                                    variant="text"
                                    size="small"
                                    @click="moveSigner(idx, idx - 1)"
                                  ></v-btn>
                                  <v-btn
                                    v-if="idx < form.signers.length - 1"
                                    icon="mdi-arrow-down"
                                    variant="text"
                                    size="small"
                                    @click="moveSigner(idx, idx + 1)"
                                  ></v-btn>
                                  <v-btn
                                    icon="mdi-delete"
                                    variant="text"
                                    size="small"
                                    color="error"
                                    @click="removeSigner(idx)"
                                  ></v-btn>
                                </div>
                              </template>
                            </v-list-item>
                          </template>
                        </v-list>
                      </div>

                      <!-- Step Actions -->
                      <div class="step-actions">
                        <v-btn
                          variant="outlined"
                          @click="currentStep = 1"
                          class="px-6"
                        >
                          Atrás
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="primary"
                          :disabled="form.signers.length === 0"
                          @click="currentStep = 3"
                          class="px-8"
                        >
                          Continuar
                          <v-icon end>mdi-chevron-right</v-icon>
                        </v-btn>
                      </div>
                    </div>
                  </v-card>
                </v-stepper-window-item>

                <!-- Step 3: Configuration -->
                <v-stepper-window-item :value="3">
                  <v-card class="document-step pa-8" flat>
                    <div class="step-content">
                      <div class="step-title mb-8">
                        <h2 class="text-h6">Configuración Final</h2>
                        <p class="text-body-2 text-medium-emphasis mt-2">
                          Configura opciones adicionales para este proceso de firma.
                        </p>
                      </div>

                      <!-- Signing Order -->
                      <v-card class="mb-6" outlined>
                        <v-card-title class="text-body-1">Orden de Firma</v-card-title>
                        <v-card-text>
                          <v-radio-group v-model="form.signingOrder">
                            <v-radio value="sequential" label="Secuencial (Uno a uno)"></v-radio>
                            <div class="text-caption text-medium-emphasis ml-8 mb-4">
                              Los firmantes firman en el orden definido
                            </div>
                            <v-radio value="parallel" label="En Paralelo (Simultáneo)"></v-radio>
                            <div class="text-caption text-medium-emphasis ml-8">
                              Todos los firmantes pueden firmar al mismo tiempo
                            </div>
                          </v-radio-group>
                        </v-card-text>
                      </v-card>

                      <!-- Expiration -->
                      <v-card class="mb-6" outlined>
                        <v-card-title class="text-body-1">Expiración</v-card-title>
                        <v-card-text>
                          <v-text-field
                            v-model.number="form.expirationDays"
                            label="Días hasta la expiración"
                            type="number"
                            variant="outlined"
                            min="1"
                            max="365"
                            hint="El documento expirará después de este tiempo si no es completamente firmado"
                            persistent-hint
                          ></v-text-field>
                        </v-card-text>
                      </v-card>

                      <!-- Notifications -->
                      <v-card class="mb-6" outlined>
                        <v-card-title class="text-body-1">Notificaciones</v-card-title>
                        <v-card-text>
                          <v-checkbox
                            v-model="form.notifyOnSigning"
                            label="Notificarme cuando alguien firme"
                          ></v-checkbox>
                          <v-checkbox
                            v-model="form.notifyOnRejection"
                            label="Notificarme si alguien rechaza firmar"
                          ></v-checkbox>
                          <v-checkbox
                            v-model="form.notifyOnExpiration"
                            label="Notificarme cuando esté a punto de expirar"
                          ></v-checkbox>
                        </v-card-text>
                      </v-card>

                      <!-- Summary -->
                      <v-card class="mb-8" outlined color="info" theme="light">
                        <v-card-title class="text-body-1">Resumen</v-card-title>
                        <v-card-text>
                          <div class="mb-3">
                            <strong>Documento:</strong> {{ form.documentName }}
                          </div>
                          <div class="mb-3">
                            <strong>Firmantes:</strong> {{ form.signers.length }}
                          </div>
                          <div class="mb-3">
                            <strong>Orden:</strong>
                            {{ form.signingOrder === 'sequential' ? 'Secuencial' : 'En Paralelo' }}
                          </div>
                          <div>
                            <strong>Expiración:</strong> {{ form.expirationDays }} días
                          </div>
                        </v-card-text>
                      </v-card>

                      <!-- Step Actions -->
                      <div class="step-actions">
                        <v-btn
                          variant="outlined"
                          @click="currentStep = 2"
                          class="px-6"
                        >
                          Atrás
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="primary"
                          :loading="submitting"
                          @click="submitDocument"
                          class="px-8"
                        >
                          <v-icon start>mdi-check</v-icon>
                          Crear Documento
                        </v-btn>
                      </div>
                    </div>
                  </v-card>
                </v-stepper-window-item>
              </v-stepper-window>
            </v-stepper>
          </div>
        </v-col>
      </v-row>
    </section>

    <!-- Success Dialog -->
    <v-dialog v-model="successDialog" max-width="500">
      <v-card>
        <v-card-text class="pa-8 text-center">
          <v-icon size="80" color="success" class="mb-4">mdi-check-circle</v-icon>
          <h2 class="text-h5 mb-4">¡Documento Creado Exitosamente!</h2>
          <p class="text-body-2 text-medium-emphasis mb-8">
            Tu documento ha sido cargado y los firmantes han sido notificados.
          </p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            variant="tonal"
            @click="goToDashboard"
            class="px-6"
          >
            Ir al Dashboard
          </v-btn>
          <v-btn
            color="primary"
            @click="createAnother"
            class="px-6"
          >
            Crear Otro
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// State
const currentStep = ref(1);
const isDragging = ref(false);
const submitting = ref(false);
const successDialog = ref(false);
const fileInput = ref(null);

// Form state
const form = ref({
  pdfFile: null,
  documentName: '',
  description: '',
  signers: [],
  signingOrder: 'sequential',
  expirationDays: 30,
  notifyOnSigning: true,
  notifyOnRejection: true,
  notifyOnExpiration: true
});

const newSigner = ref({
  name: '',
  email: '',
  phone: '',
  role: 'Firmante'
});

// Methods
const handleFileSelect = (e) => {
  const file = e.target.files?.[0];
  if (file && file.type === 'application/pdf') {
    form.value.pdfFile = file;
  }
};

const handleFileDrop = (e) => {
  isDragging.value = false;
  const file = e.dataTransfer.files?.[0];
  if (file && file.type === 'application/pdf') {
    form.value.pdfFile = file;
  }
};

const clearFile = () => {
  form.value.pdfFile = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const addSigner = () => {
  if (newSigner.value.name && newSigner.value.email && newSigner.value.role) {
    form.value.signers.push({ ...newSigner.value });
    newSigner.value = { name: '', email: '', phone: '', role: 'Firmante' };
  }
};

const removeSigner = (index) => {
  form.value.signers.splice(index, 1);
};

const moveSigner = (from, to) => {
  const temp = form.value.signers[from];
  form.value.signers[from] = form.value.signers[to];
  form.value.signers[to] = temp;
};

const submitDocument = async () => {
  submitting.value = true;
  
  // Simular envío
  setTimeout(() => {
    submitting.value = false;
    successDialog.value = true;
  }, 1500);
};

const goToDashboard = () => {
  router.push({ name: 'Dashboard' });
};

const createAnother = () => {
  successDialog.value = false;
  currentStep.value = 1;
  form.value = {
    pdfFile: null,
    documentName: '',
    description: '',
    signers: [],
    signingOrder: 'sequential',
    expirationDays: 30,
    notifyOnSigning: true,
    notifyOnRejection: true,
    notifyOnExpiration: true
  };
  newSigner.value = { name: '', email: '', phone: '', role: 'Firmante' };
};
</script>

<style scoped>
.new-document-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%);
  padding: 2rem 1rem;
  margin: 0;
  margin-top: 100px;
  width: 100%;
  max-width: 100%;
}

@media (min-width: 960px) {
  .new-document-page {
    padding: 3rem 2rem 4rem 2rem;
  }
}

.new-document-header {
  background: #1a1a1a;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.2);
}

:deep(.document-stepper) {
  background: transparent;
  box-shadow: none;
}

:deep(.stepper-item) {
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

.document-step {
  background: #1a1a1a;
  border-radius: 12px;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.step-content {
  max-width: 100%;
}

.step-title {
  border-bottom: 1px solid rgba(212, 175, 55, 0.2);
  padding-bottom: 1rem;
}

.file-upload-area {
  min-height: 200px;
}

.upload-zone {
  border: 2px dashed rgba(212, 175, 55, 0.3);
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #0d0d0d;
}

.upload-zone:hover {
  border-color: #d4af37;
  background: rgba(212, 175, 55, 0.05);
}

.upload-zone.dragging {
  border-color: #1976d2;
  background: #f0f7ff;
}

.file-selected {
  border: 2px solid #66bb6a;
  border-radius: 12px;
  background: rgba(102, 187, 106, 0.1);
  text-align: center;
}

.signer-form {
  background: #0d0d0d;
  border-radius: 8px;
}

.signer-list {
  background: #1a1a1a;
  border-radius: 8px;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.step-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid rgba(212, 175, 55, 0.2);
}

.gap-2 {
  gap: 0.5rem;
}

.bg-light {
  background: #0d0d0d;
}

.rounded-lg {
  border-radius: 8px;
}

:deep(.v-container) {
  background: transparent;
  padding: 0 2rem;
}

:deep(.v-main) {
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%) !important;
}
</style>
