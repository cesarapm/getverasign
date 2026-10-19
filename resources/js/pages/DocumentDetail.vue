<template>
  <v-container class="document-detail-page">
    <!-- Back Button -->
    <div class="mb-6 d-flex align-center justify-space-between gap-3">
      <v-btn
        variant="text"
        color="primary"
        prepend-icon="mdi-chevron-left"
        @click="$router.back()"
      >
        Volver
      </v-btn>
      <v-btn
        v-if="document"
        color="error"
        variant="tonal"
        prepend-icon="mdi-delete"
        :loading="deletingDocument"
        @click="deleteDocument"
      >
        Borrar trabajo
      </v-btn>
    </div>

    <!-- Loading State -->
    <v-card v-if="loading" class="pa-12 text-center">
      <v-progress-circular indeterminate color="primary" class="mb-4"></v-progress-circular>
      <p>Cargando documento...</p>
    </v-card>

    <!-- Error State -->
    <v-card v-else-if="error" class="pa-6 bg-error mb-6">
      <v-card-title class="text-error">Error al cargar el documento</v-card-title>
      <v-card-text class="text-white">{{ error }}</v-card-text>
    </v-card>

    <!-- Content -->
    <template v-else-if="document">
      <v-row>
        <!-- Main Content -->
        <v-col cols="12" lg="8">
          <!-- Document Info Card -->
          <v-card class="mb-6" elevation="0" outlined>
            <v-card-title class="pa-6 d-flex align-center gap-4">
              <v-icon size="32" color="primary">mdi-file-document</v-icon>
              <div class="flex-grow-1">
                <div class="text-h6">{{ document.document_name }}</div>
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
                    <div class="text-caption text-medium-emphasis mb-1">Creado</div>
                    <div class="font-weight-bold">{{ formatDate(document.created_at) }}</div>
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
                    <div class="text-caption text-medium-emphasis mb-1">Orden de Firma</div>
                    <div class="font-weight-bold">{{ signingOrderLabel }}</div>
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

          <!-- PDF Preview with Signatures -->
          <v-card class="mb-6" elevation="0" outlined>
            <v-card-title class="pa-6 d-flex align-center justify-between">
              <span>Vista Previa del Documento con Firmas</span>
              <v-btn
                color="success"
                variant="tonal"
                prepend-icon="mdi-download"
                :loading="downloadingSignedPdf"
                @click="downloadSignedPdf"
                size="small"
              >
                {{ document.signed_pdf_path ? 'Descargar PDF Firmado' : 'Generar y descargar PDF firmado' }}
              </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pa-6">
              <!-- PDF Canvas Container -->
              <div v-if="pdfLoaded && pdfPages.length > 0" class="pdf-viewer-container">
                <!-- Page Navigation -->
                <div class="mb-4 d-flex align-center justify-center gap-4">
                  <v-btn
                    icon="mdi-chevron-left"
                    size="small"
                    @click="currentPage = Math.max(0, currentPage - 1)"
                    :disabled="currentPage === 0"
                  ></v-btn>
                  <span class="text-body-2">
                    Página {{ currentPage + 1 }} de {{ totalPages }}
                  </span>
                  <v-btn
                    icon="mdi-chevron-right"
                    size="small"
                    @click="currentPage = Math.min(totalPages - 1, currentPage + 1)"
                    :disabled="currentPage === totalPages - 1"
                  ></v-btn>
                </div>

                <!-- PDF Canvas -->
                <div class="pdf-canvas-wrapper">
                  <div class="pdf-page-container">
                    <canvas
                      :key="`pdf-canvas-${currentPage}`"
                      ref="pdfCanvasRef"
                      class="pdf-canvas"
                    ></canvas>
                    
                    <!-- Signature Overlays -->
                    <div v-if="!usingSignedPdf" class="signatures-overlay">
                      <div
                        v-for="field in signatureFieldsForCurrentPage"
                        :key="field.id"
                        class="signature-field"
                        :style="getSignatureFieldStyle(field)"
                      >
                        <div class="signature-box">
                          <img
                            v-if="field.signatureImagePath"
                            :src="`${assetUrl}storage/${field.signatureImagePath}`"
                            :alt="`Firma de ${field.signerName}`"
                            class="signature-image"
                          />
                          <div class="signature-label">
                            {{ field.signerName }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Empty State with Load Button -->
              <div v-if="!pdfLoaded" class="text-center pa-8">
                <v-icon size="80" class="mb-4 text-medium-emphasis">mdi-file-pdf-box</v-icon>
                <div class="text-body-2 text-medium-emphasis mb-6">
                  <p>Presiona el botón para cargar la vista previa del PDF</p>
                </div>
                <v-btn
                  color="primary"
                  variant="tonal"
                  size="large"
                  prepend-icon="mdi-eye"
                  :loading="loadingPdf"
                  @click="loadPdfPreview"
                >
                  Ver Miniatura
                </v-btn>
              </div>

              <!-- Download Button -->
              <v-btn
                v-if="document.signed_pdf_url || document.pdf_url"
                color="primary"
                variant="outlined"
                class="mt-4"
                :href="document.signed_pdf_url || document.pdf_url"
                target="_blank"
              >
                <v-icon start>mdi-download</v-icon>
                {{ document.signed_pdf_url ? 'Descargar PDF Firmado' : 'Descargar PDF Original' }}
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Sidebar -->
        <v-col cols="12" lg="4">
          <!-- Download Options Card -->
          <v-card class="mb-6" elevation="0" outlined v-if="document">
            <v-card-title class="pa-6">
              <v-icon class="mr-2" color="primary">mdi-download-multiple</v-icon>
              Descargas
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-6">
              <v-row class="gap-3">
                <!-- Descargar PDF Original -->
                <v-col cols="12">
                  <v-btn
                    block
                    color="info"
                    variant="tonal"
                    prepend-icon="mdi-file-pdf"
                    :href="assetUrl + 'storage/' + document.pdf_path"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    PDF Original
                  </v-btn>
                </v-col>

                <!-- Descargar PDF Firmado -->
                <v-col cols="12">
                  <v-btn
                    block
                    color="success"
                    variant="tonal"
                    prepend-icon="mdi-file-pdf-box"
                    :loading="downloadingSignedPdf"
                    @click="downloadSignedPdf"
                  >
                    {{ document.signed_pdf_path ? 'PDF Firmado ✓' : 'Generar PDF Firmado' }}
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
          
          <!-- Signers Card -->
          <v-card class="mb-6" elevation="0" outlined>
            <v-card-title class="pa-6">
              <span>Firmantes</span>
              <v-spacer></v-spacer>
              <v-chip size="small" label>
                {{ signedCount }} / {{ document.signers.length }}
              </v-chip>
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-0">
              <v-list class="py-0">
                <v-list-item
                  v-for="(signer, idx) in document.signers"
                  :key="idx"
                  class="py-3"
                >
                  <template #prepend>
                    <v-avatar
                      :color="getSignerStatusColor(signer.status)"
                      size="small"
                      class="mr-3"
                    >
                      <v-icon :icon="getSignerStatusIcon(signer.status)" size="small"></v-icon>
                    </v-avatar>
                  </template>

                  <div class="flex-grow-1">
                    <div class="text-body-2 font-weight-bold">
                      {{ signer.name }}
                    </div>
                    <div class="text-caption text-medium-emphasis">
                      {{ signer.email }}
                    </div>
                    <div class="text-caption text-primary">
                      {{ getRoleLabel(signer.role) }}
                    </div>
                  </div>

                  <v-chip
                    size="x-small"
                    :color="getSignerStatusColor(signer.status)"
                    :text-color="getSignerStatusTextColor(signer.status)"
                    variant="tonal"
                  >
                    {{ getSignerStatusLabel(signer.status) }}
                  </v-chip>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Signature Fields Card -->
          <v-card elevation="0" outlined>
            <v-card-title class="pa-6">
              Campos de Firma
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-0">
              <v-list class="py-0">
                <v-list-item
                  v-for="field in document.signature_fields"
                  :key="field.id"
                  class="py-2 px-6"
                >
                  <div class="flex-grow-1">
                    <div class="text-body-2 font-weight-bold">
                      {{ field.signerName }}
                    </div>
                    <div class="text-caption text-medium-emphasis">
                      Página {{ field.page + 1 }} • Posición: ({{ Math.round(field.x) }}%, {{ Math.round(field.y) }}%)
                    </div>
                  </div>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </template>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch, shallowRef, markRaw, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import * as pdfjsLib from 'pdfjs-dist';
import http from '../utils/http';

// Setup pdf.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = `//cdnjs.cloudflare.com/ajax/libs/pdf.js/${pdfjsLib.version}/pdf.worker.min.js`;

const route = useRoute();
const router = useRouter();
const assetUrl = window.location.origin + '/';

// State
const loading = ref(true);
const error = ref(null);
const document = ref(null);
const pdfDocument = shallowRef(null);
const pdfPages = ref([]);
const totalPages = ref(0);
const currentPage = ref(0);
const downloadingSignedPdf = ref(false);
const deletingDocument = ref(false);
const loadingPdf = ref(false);
const pdfLoaded = ref(false);
const pdfCanvasRef = ref(null);
const usingSignedPdf = computed(() => Boolean(document.value?.signed_pdf_url));

// Computed
const progressPercentage = computed(() => {
  if (!document.value) return 0;
  const total = document.value.signers.length;
  const signed = document.value.signers.filter(s => s.status === 'signed').length;
  return total === 0 ? 0 : Math.round((signed / total) * 100);
});

const signedCount = computed(() => {
  if (!document.value) return 0;
  return document.value.signers.filter(s => s.status === 'signed').length;
});

const signingOrderLabel = computed(() => {
  if (!document.value) return '';
  return document.value.signing_order === 'sequential' ? 'Secuencial' : 'Paralelo';
});

const expirationDate = computed(() => {
  if (!document.value) return null;
  const exp = new Date(document.value.created_at);
  exp.setDate(exp.getDate() + (document.value.expiration_days || 30));
  return exp;
});

const isExpired = computed(() => {
  return expirationDate.value ? new Date() > expirationDate.value : false;
});

const signatureFieldsForCurrentPage = computed(() => {
  if (!document.value) return [];
  return document.value.signature_fields.filter(f => f.page === currentPage.value);
});

// Methods
const loadDocument = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await http.get(`/signature-documents/${route.params.id}`);
    document.value = response.data;

    // No cargar el PDF automáticamente, esperar a que el usuario presione el botón
  } catch (err) {
    error.value = err.response?.data?.message || err.message || 'Error al cargar el documento';
    console.error('Error loading document:', err);
  } finally {
    loading.value = false;
  }
};

const loadPdf = async (pdfUrl) => {
  try {
    const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
    pdfDocument.value = markRaw(pdf);
    totalPages.value = pdf.numPages;
    pdfPages.value = Array.from({ length: pdf.numPages }, (_, i) => i);

    currentPage.value = 0;
    await nextTick();
    await renderPage(0);
  } catch (err) {
    error.value = 'Error al cargar el PDF: ' + err.message;
    console.error('Error loading PDF:', err);
  }
};

const downloadSignedPdf = async () => {
  if (!document.value?.id || downloadingSignedPdf.value) {
    return;
  }

  try {
    downloadingSignedPdf.value = true;

    const response = await http.get(`/signature-documents/${document.value.id}/download-signed`, {
      responseType: 'blob',
    });

    const blobUrl = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = window.document.createElement('a');
    const fallbackName = `${(document.value.document_name || 'documento').replace(/[^\w\-]+/g, '_')}-firmado.pdf`;
    link.href = blobUrl;
    link.download = fallbackName;
    window.document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(blobUrl);
    await loadDocument();
  } catch (err) {
    console.error('Error downloading signed PDF:', err);
    window.alert('No se pudo descargar el PDF firmado. Intenta de nuevo.');
  } finally {
    downloadingSignedPdf.value = false;
  }
};

const renderPage = async (pageNum) => {
  if (!pdfDocument.value || !pdfCanvasRef.value) return;

  try {
    const page = await pdfDocument.value.getPage(pageNum + 1);
    const canvas = pdfCanvasRef.value;
    
    const viewport = page.getViewport({ scale: 1.5 });
    canvas.width = viewport.width;
    canvas.height = viewport.height;

    const context = canvas.getContext('2d');
    await page.render({
      canvasContext: context,
      viewport: viewport
    }).promise;
  } catch (err) {
    console.error(`Error rendering page ${pageNum}:`, err);
  }
};

const loadPdfPreview = async () => {
  if (!document.value) return;
  
  try {
    loadingPdf.value = true;
    
    // Obtener la URL del PDF
    const pdfUrl = document.value.signed_pdf_url || document.value.pdf_url;
    
    if (!pdfUrl) {
      throw new Error('No hay PDF disponible');
    }
    
    // Cargar el PDF usando pdf.js
    const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
    pdfDocument.value = markRaw(pdf);
    totalPages.value = pdf.numPages;
    
    // Crear array de páginas
    pdfPages.value = Array.from({ length: pdf.numPages }, (_, i) => i);
    
    // Marcar como cargado
    pdfLoaded.value = true;
    currentPage.value = 0;
    
    // Renderizar la primera página
    await nextTick();
    await new Promise(resolve => setTimeout(resolve, 100));
    await renderPage(0);
    
  } catch (err) {
    console.error('Error loading PDF preview:', err);
    error.value = 'No se pudo cargar la vista previa del PDF';
  } finally {
    loadingPdf.value = false;
  }
};

const deleteDocument = async () => {
  if (!document.value?.id || deletingDocument.value) {
    return;
  }

  const confirmed = window.confirm('¿Seguro que deseas borrar este trabajo? Esta acción no se puede deshacer.');
  if (!confirmed) {
    return;
  }

  try {
    deletingDocument.value = true;
    await http.delete(`/signature-documents/${document.value.id}`);
    window.alert('Trabajo eliminado correctamente.');
    await router.push('/dashboard');
  } catch (err) {
    console.error('Error deleting document:', err);
    const message = err.response?.data?.message || 'No se pudo borrar el trabajo.';
    window.alert(message);
  } finally {
    deletingDocument.value = false;
  }
};

const getSignatureFieldStyle = (field) => {
  return {
    left: `${field.x}%`,
    top: `${field.y}%`,
    width: `${field.width}%`,
    height: `${field.height}%`,
  };
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return date.toLocaleDateString('es-ES', options);
};

const getStatusColor = (status) => {
  const colors = {
    'pending': 'warning',
    'signed': 'success',
    'rejected': 'error',
    'completed': 'success'
  };
  return colors[status] || 'secondary';
};

const getStatusTextColor = (status) => {
  return status === 'signed' || status === 'completed' ? 'white' : 'black';
};

const getStatusLabel = (status) => {
  const labels = {
    'pending': 'Pendiente',
    'signed': 'Firmado',
    'rejected': 'Rechazado',
    'completed': 'Completado'
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

const getSignerStatusTextColor = (status) => {
  return status === 'signed' ? 'white' : status === 'pending' ? 'black' : 'white';
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

const getRoleLabel = (role) => {
  const labels = {
    'Firmante': 'Firmante',
    'CC (Con Copia)': 'CC',
    'Revisor': 'Revisor'
  };
  return labels[role] || role;
};

// Watchers
watch(currentPage, async (newPage) => {
  if (!pdfLoaded.value) return;
  await nextTick();
  await renderPage(newPage);
});

// Lifecycle
onMounted(() => {
  loadDocument();
});
</script>

<style scoped>
.document-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%);
  padding: 2rem 1rem;
  margin: 0;
  width: 100%;
  max-width: 100%;
}

@media (min-width: 960px) {
  .document-detail-page {
    padding: 3rem 2rem 4rem 2rem;
  }
}

.pdf-viewer-container {
  background: #0a0a0a;
  border-radius: 8px;
  padding: 1rem;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.pdf-canvas-wrapper {
  display: flex;
  justify-content: center;
  background: #0d0d0d;
  border-radius: 4px;
  overflow: auto;
  max-height: 600px;
}

.pdf-page-container {
  position: relative;
  display: inline-block;
  background: white;
}

.pdf-canvas {
  display: block;
  border: 1px solid rgba(212, 175, 55, 0.1);
}

.signatures-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.signature-field {
  position: absolute;
  border: 2px dashed rgba(212, 175, 55, 0.5);
  border-radius: 4px;
  background: rgba(212, 175, 55, 0.05);
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.signature-box {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
}

.signature-image {
  max-width: 100%;
  max-height: 70%;
  object-fit: contain;
}

.signature-label {
  font-size: 0.65rem;
  color: #d4af37;
  text-align: center;
  font-weight: bold;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
}

.info-item {
  padding: 0.75rem 0;
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

:deep(.text-primary) {
  color: #d4af37 !important;
}

:deep(.text-error) {
  color: #ff5252 !important;
}

:deep(.bg-error) {
  background: #ff5252 !important;
}

:deep(.v-container) {
  background: transparent;
  padding: 0 2rem;
}

:deep(.v-main) {
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%) !important;
}
</style>
