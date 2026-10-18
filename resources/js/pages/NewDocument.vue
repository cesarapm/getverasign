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

                <v-divider></v-divider>

                <v-stepper-item
                  :complete="currentStep > 3"
                  :value="3"
                  :rules="[() => form.signers.length > 0]"
                  @click="currentStep = 3"
                  class="stepper-item"
                >
                  <template #title>
                    <span class="stepper-title">Posicionar Firmas</span>
                  </template>
                </v-stepper-item>

                <v-divider></v-divider>

                <v-stepper-item
                  :complete="currentStep > 4"
                  :value="4"
                  :rules="[() => form.signatureFields.length > 0]"
                  @click="currentStep = 4"
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

                <!-- Step 3: Position Signatures -->
                <v-stepper-window-item :value="3">
                  <v-card class="document-step pa-8" flat>
                    <div class="step-content">
                      <div class="step-title mb-8">
                        <h2 class="text-h6">Posicionar Firmas en el Documento</h2>
                        <p class="text-body-2 text-medium-emphasis mt-2">
                          Selecciona un firmante y haz clic en el documento para colocar su campo de firma.
                        </p>
                      </div>

                      <!-- Zoom Controls -->
                      <div v-if="pdfPages.length > 0" class="zoom-controls mb-4">
                        <v-btn
                          size="small"
                          variant="outlined"
                          @click="setZoom(50)"
                          :color="currentZoom === 50 ? 'primary' : ''"
                        >
                          50%
                        </v-btn>
                        <v-btn
                          size="small"
                          variant="outlined"
                          @click="setZoom(75)"
                          :color="currentZoom === 75 ? 'primary' : ''"
                        >
                          75%
                        </v-btn>
                        <v-btn
                          size="small"
                          variant="outlined"
                          @click="setZoom(100)"
                          :color="currentZoom === 100 ? 'primary' : ''"
                        >
                          100%
                        </v-btn>
                        <v-btn
                          size="small"
                          variant="outlined"
                          @click="setZoom(125)"
                          :color="currentZoom === 125 ? 'primary' : ''"
                        >
                          125%
                        </v-btn>
                        <v-btn
                          size="small"
                          variant="outlined"
                          @click="setZoom(150)"
                          :color="currentZoom === 150 ? 'primary' : ''"
                        >
                          150%
                        </v-btn>
                        <v-divider vertical class="mx-2"></v-divider>
                        <span class="text-caption text-medium-emphasis">
                          {{ totalPages }} página{{ totalPages !== 1 ? 's' : '' }}
                        </span>
                      </div>

                      <!-- Main signature positioning area -->
                      <v-row class="mb-6 signature-workspace-row" style="height: 700px">
                        <!-- PDF Viewer - Main Area (70%) -->
                        <v-col cols="12" lg="8" class="d-flex flex-column pdf-viewer-col">
                          <div class="pdf-viewer-wrapper">
                            <!-- PDF Container with all pages -->
                            <div
                              ref="pdfContainerRef"
                              class="pdf-container"
                              :class="{ 'placing-mode': isPlacingSignature }"
                            >
                              <!-- Status Message -->
                              <div v-if="isPlacingSignature" class="placing-status">
                                <v-icon>mdi-cursor-default-click</v-icon>
                                <span class="ml-2">
                                  Selecciona un lugar en el documento para colocar la firma de 
                                  <strong>{{ form.signers[selectedSignerId]?.name }}</strong>
                                </span>
                              </div>

                              <!-- Loading State -->
                              <div v-if="pdfLoading" class="pdf-placeholder">
                                <v-progress-circular
                                  indeterminate
                                  color="primary"
                                  size="48"
                                />
                                <p class="text-body-2 mt-4">
                                  Cargando PDF...
                                </p>
                              </div>

                              <!-- Error State -->
                              <div v-else-if="pdfError" class="pdf-placeholder">
                                <v-icon size="64" color="error">
                                  mdi-file-alert-outline
                                </v-icon>
                                <p class="text-body-2 mt-4 text-error">
                                  {{ pdfError }}
                                </p>
                                <v-btn
                                  v-if="form.pdfFile"
                                  color="primary"
                                  variant="outlined"
                                  class="mt-4"
                                  @click="loadPDF(form.pdfFile)"
                                >
                                  Reintentar
                                </v-btn>
                              </div>

                              <!-- PDF Pages -->
                              <div v-else-if="pdfPages.length > 0" class="pages-container">
                                <template v-for="(_, pageIndex) in pdfPages" :key="pageIndex">
                                  <div class="page-wrapper" :style="{ marginBottom: '2rem' }">
                                    <!-- Page Number Indicator -->
                                    <div class="page-number">
                                      Página {{ pageIndex + 1 }} de {{ totalPages }}
                                    </div>

                                    <!-- PDF Canvas -->
                                    <div class="page-content">
                                      <canvas
                                        :id="`pdf-canvas-${pageIndex}`"
                                        class="pdf-canvas"
                                        :class="{ 'cursor-crosshair': isPlacingSignature }"
                                        @click="handlePDFClick($event, pageIndex)"
                                      ></canvas>

                                      <!-- Signature Fields Overlay -->
                                      <div class="signature-overlay">
                                        <template v-for="field in getSignatureFieldsForPage(pageIndex)" :key="field.id">
                                          <div
                                            class="signature-field-box"
                                            :style="{
                                              left: field.x + '%',
                                              top: field.y + '%',
                                              width: field.width + '%',
                                              height: field.height + '%'
                                            }"
                                            @pointerdown="startDrag($event, field.id)"
                                            @click.stop="openSignatureImageDialog(field.id)"
                                          >
                                            <div class="signature-field-content">
                                              <div class="signature-signer-name">
                                                {{ field.signerName }}
                                              </div>
                                              <div class="signature-sign-area">
                                                <img
                                                  v-if="field.signatureImageUrl"
                                                  :src="field.signatureImageUrl"
                                                  alt="Firma cargada"
                                                  class="signature-image"
                                                />
                                                <div v-else class="signature-label">Firmar aquí</div>
                                              </div>
                                              <div class="signature-line"></div>
                                              <div v-if="!field.signatureImageUrl" class="signature-upload-hint">
                                                Clic o toque para cargar firma
                                              </div>
                                              <v-btn
                                                :icon="field.signatureImageUrl ? 'mdi-pencil' : 'mdi-image-plus'"
                                                size="x-small"
                                                class="signature-edit-btn"
                                                @click.stop="openSignatureImageDialog(field.id)"
                                              ></v-btn>
                                              <v-btn
                                                icon="mdi-plus"
                                                size="x-small"
                                                class="signature-clone-btn"
                                                @click.stop="prepareAnotherFieldForSigner(field.signerId)"
                                              ></v-btn>
                                              <v-btn
                                                icon="mdi-close"
                                                size="x-small"
                                                class="signature-remove-btn"
                                                @click.stop="removeSignatureField(field.id)"
                                              ></v-btn>
                                            </div>
                                          </div>
                                        </template>
                                      </div>
                                    </div>
                                  </div>
                                </template>
                              </div>

                              <!-- Empty State -->
                              <div v-else class="pdf-placeholder">
                                <v-icon size="64" class="mb-4">mdi-file-pdf-box</v-icon>
                                <p class="text-body-2 text-medium-emphasis">
                                  Carga un PDF en el Paso 1 para posicionar las firmas
                                </p>
                              </div>
                            </div>
                          </div>
                        </v-col>

                        <!-- Signers Panel (30%) -->
                        <v-col cols="12" lg="4">
                          <div class="signers-panel">
                            <!-- Signers to Assign -->
                            <div class="signers-section">
                              <h3 class="text-body-1 font-weight-bold mb-4">
                                <v-icon small class="mr-2">mdi-account-multiple</v-icon>
                                Firmantes
                              </h3>

                              <div
                                v-if="form.signers.length > 0 && form.signers.every((_, idx) => hasSignatureFieldsSigner(idx) || form.signers[idx].role !== 'Firmante')"
                                class="text-center py-6 all-assigned-banner"
                              >
                                <v-icon size="56" color="success">mdi-check-circle</v-icon>
                                <p class="text-body-2 text-medium-emphasis mt-3">
                                  ¡Todas las firmas posicionadas!
                                </p>
                              </div>

                              <v-list class="signers-list pa-0">
                                <template v-for="(signer, signerIdx) in form.signers" :key="signerIdx">
                                  <v-list-item
                                    class="signer-list-item"
                                    :class="{
                                      'pending': signer.role === 'Firmante' && !hasSignatureFieldsSigner(signerIdx),
                                      'assigned': hasSignatureFieldsSigner(signerIdx),
                                      'not-required': signer.role !== 'Firmante',
                                      'selected': selectedSignerId === signerIdx && isPlacingSignature
                                    }"
                                    @click="selectSignerForPlacing(signerIdx)"
                                  >
                                    <template #prepend>
                                      <v-avatar
                                        size="36"
                                        :color="hasSignatureFieldsSigner(signerIdx) ? 'success' : getSignerColor(signerIdx)"
                                        class="signer-avatar text-white font-weight-bold"
                                      >
                                        <v-icon v-if="hasSignatureFieldsSigner(signerIdx)" size="20" color="white">
                                          mdi-check
                                        </v-icon>
                                        <span v-else class="signer-avatar-number">{{ signerIdx + 1 }}</span>
                                      </v-avatar>
                                    </template>

                                    <v-list-item-title class="font-weight-medium">
                                      {{ signer.name }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle class="text-truncate">
                                      {{ signer.email }}
                                    </v-list-item-subtitle>

                                    <template #append>
                                      <div class="d-flex flex-column align-center gap-1">
                                        <div v-if="hasSignatureFieldsSigner(signerIdx)" class="text-caption text-success">
                                          {{ getSignatureCountForSigner(signerIdx) }} firma{{ getSignatureCountForSigner(signerIdx) !== 1 ? 's' : '' }}
                                        </div>
                                        <v-btn
                                          v-if="signer.role === 'Firmante'"
                                          :icon="hasSignatureFieldsSigner(signerIdx) ? 'mdi-plus-circle' : 'mdi-plus-circle-outline'"
                                          size="small"
                                          variant="text"
                                          color="primary"
                                          class="assign-signature-btn"
                                          @click.stop="selectSignerForPlacing(signerIdx)"
                                        ></v-btn>
                                      </div>
                                    </template>
                                  </v-list-item>
                                </template>
                              </v-list>
                            </div>

                            <!-- Help Section -->
                            <v-divider class="my-4"></v-divider>

                            <div class="help-section">
                              <h4 class="text-body-2 font-weight-bold mb-2">
                                <v-icon small class="mr-2">mdi-lightbulb-outline</v-icon>
                                Cómo posicionar firmas:
                              </h4>
                              <ol class="text-caption text-medium-emphasis help-list">
                                <li>Haz clic en un firmante</li>
                                <li>Haz clic en el documento para colocar la firma</li>
                                <li>Arrastra el campo para ajustar la posición</li>
                                <li>Usa el botón + para colocar otra firma del mismo firmante</li>
                                <li>Usa el botón X para eliminar</li>
                                <li>Repite para cada firmante</li>
                              </ol>
                            </div>
                          </div>
                        </v-col>
                      </v-row>

                      <!-- Step Actions -->
                      <div class="step-actions mt-6">
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
                          :disabled="!canProceedToStep4()"
                          @click="currentStep = 4"
                          class="px-8"
                        >
                          Continuar
                          <v-icon end>mdi-chevron-right</v-icon>
                        </v-btn>
                      </div>
                    </div>
                  </v-card>
                </v-stepper-window-item>

                <!-- Step 4: Configuration -->
                <v-stepper-window-item :value="4">
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
                      <v-alert
                        v-if="submitError"
                        type="error"
                        variant="tonal"
                        class="mb-4"
                        closable
                        @click:close="submitError = ''"
                      >
                        {{ submitError }}
                      </v-alert>

                      <div class="step-actions">
                        <v-btn
                          variant="outlined"
                          @click="currentStep = 3"
                          class="px-6"
                        >
                          Atrás
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="primary"
                          :loading="submitting"
                          :disabled="submitting"
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

    <v-dialog v-model="signatureImageDialog" max-width="560">
      <v-card>
        <v-card-title class="text-body-1 d-flex align-center justify-space-between">
          <span>Firma del campo</span>
          <v-btn icon="mdi-close" variant="text" size="small" @click="closeSignatureImageDialog"></v-btn>
        </v-card-title>
        <v-card-text>
          <p class="text-body-2 mb-3">
            Firmante: <strong>{{ editingSignatureField?.signerName || 'N/A' }}</strong>
          </p>

          <v-tabs v-model="signatureModalTab" color="primary" density="comfortable" class="mb-4">
            <v-tab value="draw">
              <v-icon start>mdi-draw</v-icon>
              Dibujar firma
            </v-tab>
            <v-tab value="upload">
              <v-icon start>mdi-image-plus</v-icon>
              Subir imagen
            </v-tab>
          </v-tabs>

          <v-window v-model="signatureModalTab">
            <v-window-item value="draw">
              <div class="signature-pad-container mb-4">
                <canvas
                  ref="signaturePadCanvasRef"
                  class="signature-pad-canvas"
                  @pointerdown="onSignaturePointerDown"
                  @pointermove="onSignaturePointerMove"
                  @pointerup="onSignaturePointerUp"
                  @pointercancel="onSignaturePointerCancel"
                ></canvas>
              </div>

              <p class="text-caption text-medium-emphasis mb-3">
                Firma con mouse, lápiz digital o tu dedo. También puedes reemplazar la firma cuantas veces necesites.
              </p>

              <div v-if="editingSignatureField?.signatureImageUrl" class="signature-image-dialog-preview mb-4">
                <img
                  :src="editingSignatureField.signatureImageUrl"
                  alt="Vista previa de firma"
                  class="signature-image-dialog-preview-img"
                />
              </div>

              <div class="d-flex flex-wrap gap-2 justify-end">
                <v-btn variant="text" @click="clearSignaturePad">Limpiar</v-btn>
                <v-btn variant="outlined" @click="closeSignatureImageDialog">Cancelar</v-btn>
                <v-btn color="primary" @click="saveDrawnSignature">Guardar firma</v-btn>
              </div>
            </v-window-item>

            <v-window-item value="upload">
              <input
                ref="signatureImageInput"
                type="file"
                accept="image/png,image/jpeg,image/webp"
                class="signature-file-input"
                @change="handleSignatureImageFileChange"
              />

              <div class="d-flex flex-wrap gap-2 mb-4">
                <v-btn color="primary" variant="outlined" @click="openSignatureImagePicker">
                  <v-icon start>mdi-image-plus</v-icon>
                  Seleccionar imagen
                </v-btn>

                <v-btn
                  v-if="editingSignatureField?.signatureImageUrl"
                  color="warning"
                  variant="tonal"
                  @click="removeSignatureImageFromField"
                >
                  <v-icon start>mdi-image-remove</v-icon>
                  Eliminar imagen
                </v-btn>
              </div>

              <div v-if="editingSignatureField?.signatureImageUrl" class="signature-image-dialog-preview">
                <img
                  :src="editingSignatureField.signatureImageUrl"
                  alt="Vista previa de firma"
                  class="signature-image-dialog-preview-img"
                />
              </div>
              <p v-else class="text-body-2 text-medium-emphasis mb-0">
                No hay imagen cargada para este campo.
              </p>

              <div class="d-flex justify-end mt-4">
                <v-btn variant="outlined" @click="closeSignatureImageDialog">Cancelar</v-btn>
              </div>
            </v-window-item>
          </v-window>

          <p v-if="signatureImageError" class="text-body-2 text-error mb-3">
            {{ signatureImageError }}
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, watch, nextTick, shallowRef, markRaw, computed } from 'vue';
import { useRouter } from 'vue-router';
import * as pdfjsLib from 'pdfjs-dist';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.min.js?url';
import http from '../utils/http';

const router = useRouter();

// Configurar el worker de PDF.js para Vite
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

// State
const currentStep = ref(1);
const submitting = ref(false);
const successDialog = ref(false);
const submitError = ref('');
const fileInput = ref(null);

// PDF State
const pdfDocument = shallowRef(null);
const pdfPages = ref([]);
const totalPages = ref(0);
const currentZoom = ref(100);
const pdfContainerRef = ref(null);
const pdfLoading = ref(false);
const pdfError = ref(null);
const renderingPages = ref(false);
const pdfLoadingTask = shallowRef(null);
const loadRequestId = ref(0);
const pageRenderTasks = new Map();

// Firma Placement State
const selectedSignerId = ref(null);
const isPlacingSignature = ref(false);
const draggedField = ref(null);
// Dimensiones de A4 en mm (para conversión backend)
// No necesitamos conversiones complicadas, solo porcentajes
const A4_WIDTH_MM = 210;
const A4_HEIGHT_MM = 297;
const suppressFieldClickId = ref(null);
const isTouchPointerDevice = ref(
  typeof window !== 'undefined' && typeof window.matchMedia === 'function'
    ? window.matchMedia('(pointer: coarse)').matches
    : false
);

const signatureImageDialog = ref(false);
const selectedFieldIdForImage = ref(null);
const signatureImageError = ref('');
const signatureImageInput = ref(null);
const signatureModalTab = ref('draw');
const signaturePadCanvasRef = ref(null);
const signaturePadContext = shallowRef(null);
const signaturePadPointerId = ref(null);
const isDrawingSignature = ref(false);
const didMoveOnCurrentStroke = ref(false);
const hasSignatureStroke = ref(false);

const editingSignatureField = computed(() => {
  if (!selectedFieldIdForImage.value) return null;
  return form.value.signatureFields.find((field) => field.id === selectedFieldIdForImage.value) || null;
});

// Form state
const form = ref({
  pdfFile: null,
  documentName: '',
  description: '',
  signers: [],
  signatureFields: [], // Array de {id, signerId, signerName, signerEmail, page, x, y, width, height}
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

// Watcher para renderizar PDF cuando cambias al Paso 3
watch(currentStep, async (newStep) => {
  if (newStep === 3 && pdfDocument.value) {
    await nextTick();
    await renderAllPages();
  }
});

// Methods
const handleFileSelect = (e) => {
  const file = e.target.files?.[0];
  if (file) {
    validateAndLoadPDF(file);
  }
};

const handleFileDrop = (e) => {
  const file = e.dataTransfer.files?.[0];
  if (file) {
    validateAndLoadPDF(file);
  }
};

const validateAndLoadPDF = async (file) => {
  // Validar tipo
  if (file.type !== 'application/pdf') {
    console.error('El archivo debe ser un PDF');
    return;
  }

  // Validar tamaño (25 MB máximo)
  if (file.size > 25 * 1024 * 1024) {
    console.error('El PDF no puede exceder 25 MB');
    return;
  }

  form.value.pdfFile = file;
  form.value.documentName = file.name.replace('.pdf', '');
  await loadPDF(file);
};

const withTimeout = (promise, timeoutMs, errorMessage) => {
  return new Promise((resolve, reject) => {
    const timeoutId = setTimeout(() => {
      reject(new Error(errorMessage));
    }, timeoutMs);

    promise
      .then((result) => {
        clearTimeout(timeoutId);
        resolve(result);
      })
      .catch((error) => {
        clearTimeout(timeoutId);
        reject(error);
      });
  });
};

const cancelCurrentLoadingTask = async () => {
  if (!pdfLoadingTask.value) return;

  try {
    await pdfLoadingTask.value.destroy();
    console.log('[PDF] LoadingTask anterior cancelado');
  } catch (error) {
    console.warn('[PDF] No se pudo cancelar LoadingTask anterior:', error);
  } finally {
    pdfLoadingTask.value = null;
  }
};

const cancelActiveRenderTasks = () => {
  for (const [pageIndex, renderTask] of pageRenderTasks.entries()) {
    try {
      renderTask.cancel();
      console.log(`[PDF] RenderTask cancelado para página ${pageIndex + 1}`);
    } catch (error) {
      console.warn(`[PDF] No se pudo cancelar render de página ${pageIndex + 1}:`, error);
    }
  }

  pageRenderTasks.clear();
};

const waitForCanvas = async (pageIndex, retries = 8) => {
  for (let attempt = 0; attempt < retries; attempt++) {
    const canvas = document.getElementById(`pdf-canvas-${pageIndex}`);
    if (canvas) return canvas;
    await nextTick();
    await new Promise((resolve) => requestAnimationFrame(resolve));
  }
  return null;
};

const revokeSignatureObjectUrl = (url) => {
  if (typeof url === 'string' && url.startsWith('blob:')) {
    URL.revokeObjectURL(url);
  }
};

const revokeAllSignatureImageUrls = () => {
  for (const field of form.value.signatureFields) {
    revokeSignatureObjectUrl(field.signatureImageUrl);
  }
};

const loadPDF = async (file) => {
  const currentRequestId = ++loadRequestId.value;
  pdfLoading.value = true;
  pdfError.value = null;

  try {
    await cancelCurrentLoadingTask();
    cancelActiveRenderTasks();

    console.log('[PDF] Iniciando carga:', file.name);
    console.log('[PDF] Tamaño:', file.size);

    const arrayBuffer = await file.arrayBuffer();
    console.log('[PDF] ArrayBuffer obtenido');

    const loadingTask = pdfjsLib.getDocument({
      data: new Uint8Array(arrayBuffer)
    });
    pdfLoadingTask.value = loadingTask;

    console.log('[PDF] LoadingTask creado');

    const pdf = await withTimeout(
      loadingTask.promise,
      20000,
      'Tiempo de espera agotado al cargar el PDF'
    );

    if (currentRequestId !== loadRequestId.value) {
      console.warn('[PDF] Resultado ignorado por nueva carga en curso');
      try {
        await loadingTask.destroy();
      } catch (error) {
        console.warn('[PDF] No se pudo destruir task obsoleta:', error);
      }
      return;
    }

    console.log('[PDF] Documento cargado:', pdf.numPages, 'páginas');
    
    pdfDocument.value = markRaw(pdf);
    totalPages.value = pdf.numPages;
    revokeAllSignatureImageUrls();
    pdfPages.value = Array.from({ length: pdf.numPages }, () => null);

    currentZoom.value = 100;
    form.value.signatureFields = [];

    console.log('[PDF] Estado listo para render');
  } catch (error) {
    if (currentRequestId !== loadRequestId.value) {
      return;
    }

    console.error('[PDF] Error cargando PDF:', error);

    pdfDocument.value = null;
    pdfPages.value = [];
    totalPages.value = 0;

    pdfError.value = error?.message || 'No fue posible cargar el PDF';
  } finally {
    if (currentRequestId === loadRequestId.value) {
      pdfLoading.value = false;

      if (currentStep.value === 3 && pdfDocument.value) {
        await nextTick();
        await renderAllPages();
      }
    }

    if (pdfLoadingTask.value) {
      pdfLoadingTask.value = null;
    }
  }
};

const renderAllPages = async () => {
  if (!pdfDocument.value || renderingPages.value || pdfLoading.value) {
    return;
  }

  renderingPages.value = true;

  try {
    console.log('[PDF] Iniciando render de', totalPages.value, 'páginas');

    await nextTick();

    for (let pageIndex = 0; pageIndex < totalPages.value; pageIndex++) {
      const canvas = await waitForCanvas(pageIndex);

      if (!canvas) {
        console.warn(`[PDF] Canvas no encontrado para página ${pageIndex + 1}`);
        continue;
      }

      console.log(`[PDF] Renderizando página ${pageIndex + 1}/${totalPages.value}`);

      try {
        await renderPage(pageIndex, canvas);
      } catch (error) {
        console.error(`[PDF] Error en página ${pageIndex + 1}:`, error);
        pdfError.value = error?.message || 'Error al renderizar el PDF';
      }
    }

    console.log('[PDF] Render finalizado');
  } catch (error) {
    console.error('[PDF] Error renderizando páginas:', error);
    pdfError.value = error?.message || 'Error al renderizar el PDF';
  } finally {
    renderingPages.value = false;
  }
};

const renderPage = async (pageIndex, canvas) => {
  if (!pdfDocument.value) {
    throw new Error('No hay documento PDF cargado para renderizar');
  }

  if (!canvas) {
    throw new Error(`Canvas no disponible para página ${pageIndex + 1}`);
  }

  try {
    const previousTask = pageRenderTasks.get(pageIndex);
    if (previousTask) {
      previousTask.cancel();
      pageRenderTasks.delete(pageIndex);
    }

    console.log(`[PDF] getPage(${pageIndex + 1})`);
    const page = await pdfDocument.value.getPage(pageIndex + 1);

    if (!page) {
      throw new Error(`No fue posible obtener la página ${pageIndex + 1}`);
    }

    pdfPages.value[pageIndex] = markRaw(page);

    const scale = currentZoom.value / 100;
    const viewport = page.getViewport({ scale });
    
    console.log('[PDF] viewport:', viewport.width, 'x', viewport.height);

    const context = canvas.getContext('2d');

    if (!context) {
      throw new Error(`No se pudo obtener el contexto del canvas en página ${pageIndex + 1}`);
    }

    context.setTransform(1, 0, 0, 1, 0, 0);
    context.clearRect(0, 0, canvas.width, canvas.height);

    canvas.width = Math.floor(viewport.width);
    canvas.height = Math.floor(viewport.height);

    context.setTransform(1, 0, 0, 1, 0, 0);
    context.clearRect(0, 0, canvas.width, canvas.height);

    const renderContext = {
      canvasContext: context,
      viewport: viewport
    };

    const renderTask = page.render(renderContext);
    pageRenderTasks.set(pageIndex, renderTask);

    await renderTask.promise;

    if (pageRenderTasks.get(pageIndex) === renderTask) {
      pageRenderTasks.delete(pageIndex);
    }

    console.log(`[PDF] render terminado: página ${pageIndex + 1}`);
  } catch (error) {
    if (error?.name === 'RenderingCancelledException') {
      console.warn(`[PDF] Render cancelado: página ${pageIndex + 1}`);
      return;
    }

    throw error;
  }
};

const calculateCanvasDimensions = (pageIndex) => {
  if (!pdfPages.value[pageIndex]) return { width: 600, height: 800 };
  
  const page = pdfPages.value[pageIndex];
  const scale = currentZoom.value / 100;
  const viewport = page.getViewport({ scale });
  
  return {
    width: viewport.width,
    height: viewport.height,
    scale: scale
  };
};

const setZoom = async (level) => {
  if (renderingPages.value) return;

  if (currentZoom.value === level) return;

  currentZoom.value = level;
  await nextTick();
  await renderAllPages();
};

const clearFile = () => {
  loadRequestId.value += 1;
  cancelCurrentLoadingTask();
  cancelActiveRenderTasks();

  form.value.pdfFile = null;
  form.value.documentName = '';
  pdfDocument.value = null;
  revokeAllSignatureImageUrls();
  pdfPages.value = [];
  totalPages.value = 0;
  pdfLoading.value = false;
  pdfError.value = null;
  form.value.signatureFields = [];
  selectedSignerId.value = null;
  isPlacingSignature.value = false;
  closeSignatureImageDialog();
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const openSignatureImageDialog = (fieldId) => {
  if (suppressFieldClickId.value === fieldId) {
    return;
  }

  const field = form.value.signatureFields.find((item) => item.id === fieldId);
  if (!field) return;

  selectedFieldIdForImage.value = fieldId;
  signatureImageError.value = '';
  signatureImageDialog.value = true;
};

const closeSignatureImageDialog = () => {
  resetSignaturePadState();
  signatureImageDialog.value = false;
  signatureImageError.value = '';
  selectedFieldIdForImage.value = null;
  signatureModalTab.value = 'draw';

  if (signatureImageInput.value) {
    signatureImageInput.value.value = '';
  }
};

const openSignatureImagePicker = () => {
  signatureImageInput.value?.click();
};

const handleSignatureImageFileChange = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  const allowedTypes = ['image/png', 'image/jpeg', 'image/webp'];
  if (!allowedTypes.includes(file.type)) {
    signatureImageError.value = 'El archivo debe ser PNG, JPEG o WEBP.';
    event.target.value = '';
    return;
  }

  const maxSize = 5 * 1024 * 1024;
  if (file.size > maxSize) {
    signatureImageError.value = 'La imagen no puede exceder 5 MB.';
    event.target.value = '';
    return;
  }

  const field = editingSignatureField.value;
  if (!field) {
    event.target.value = '';
    return;
  }

  revokeSignatureObjectUrl(field.signatureImageUrl);
  field.signatureImageUrl = URL.createObjectURL(file);
  field.signatureImageName = file.name;
  field.signatureImageFile = file;
  signatureImageError.value = '';
  event.target.value = '';
};

const resetSignaturePadState = () => {
  isDrawingSignature.value = false;
  signaturePadPointerId.value = null;
  didMoveOnCurrentStroke.value = false;
  hasSignatureStroke.value = false;
};

const setupSignaturePad = async () => {
  if (!signatureImageDialog.value || signatureModalTab.value !== 'draw') return;

  await nextTick();

  const canvas = signaturePadCanvasRef.value;
  if (!canvas) return;

  const rect = canvas.getBoundingClientRect();
  const cssWidth = Math.max(1, Math.floor(rect.width));
  const cssHeight = Math.max(1, Math.floor(rect.height));
  const dpr = Math.max(window.devicePixelRatio || 1, 1);

  canvas.width = Math.floor(cssWidth * dpr);
  canvas.height = Math.floor(cssHeight * dpr);

  const ctx = canvas.getContext('2d');
  if (!ctx) return;

  ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  ctx.clearRect(0, 0, cssWidth, cssHeight);
  ctx.lineCap = 'round';
  ctx.lineJoin = 'round';
  ctx.strokeStyle = '#111111';
  ctx.lineWidth = 2.2;

  signaturePadContext.value = ctx;
  resetSignaturePadState();
  signatureImageError.value = '';
};

const getCanvasPointFromPointer = (event) => {
  const canvas = signaturePadCanvasRef.value;
  if (!canvas) return null;

  const rect = canvas.getBoundingClientRect();
  const x = Math.max(0, Math.min(rect.width, event.clientX - rect.left));
  const y = Math.max(0, Math.min(rect.height, event.clientY - rect.top));
  return { x, y };
};

const onSignaturePointerDown = (event) => {
  if (event.pointerType === 'mouse' && event.button !== 0) return;

  const canvas = signaturePadCanvasRef.value;
  const ctx = signaturePadContext.value;
  if (!canvas || !ctx) return;

  const point = getCanvasPointFromPointer(event);
  if (!point) return;

  signaturePadPointerId.value = event.pointerId;
  isDrawingSignature.value = true;
  didMoveOnCurrentStroke.value = false;

  canvas.setPointerCapture(event.pointerId);

  ctx.beginPath();
  ctx.moveTo(point.x, point.y);

  event.preventDefault();
};

const onSignaturePointerMove = (event) => {
  if (!isDrawingSignature.value || signaturePadPointerId.value !== event.pointerId) return;

  const ctx = signaturePadContext.value;
  if (!ctx) return;

  const point = getCanvasPointFromPointer(event);
  if (!point) return;

  ctx.lineTo(point.x, point.y);
  ctx.stroke();

  didMoveOnCurrentStroke.value = true;
  hasSignatureStroke.value = true;

  event.preventDefault();
};

const finishSignatureStroke = (event) => {
  if (signaturePadPointerId.value !== event.pointerId) return;

  const canvas = signaturePadCanvasRef.value;
  const ctx = signaturePadContext.value;

  if (isDrawingSignature.value && ctx) {
    if (!didMoveOnCurrentStroke.value) {
      const point = getCanvasPointFromPointer(event);
      if (point) {
        // Permite un "toque" sin desplazamiento para marcar un punto.
        ctx.beginPath();
        ctx.arc(point.x, point.y, Math.max(1.2, ctx.lineWidth / 2), 0, Math.PI * 2);
        ctx.fillStyle = ctx.strokeStyle;
        ctx.fill();
        hasSignatureStroke.value = true;
      }
    }

    ctx.closePath();
  }

  if (canvas?.hasPointerCapture(event.pointerId)) {
    canvas.releasePointerCapture(event.pointerId);
  }

  isDrawingSignature.value = false;
  signaturePadPointerId.value = null;
  didMoveOnCurrentStroke.value = false;

  event.preventDefault();
};

const onSignaturePointerUp = (event) => {
  finishSignatureStroke(event);
};

const onSignaturePointerCancel = (event) => {
  finishSignatureStroke(event);
};

const clearSignaturePad = () => {
  const canvas = signaturePadCanvasRef.value;
  const ctx = signaturePadContext.value;
  if (!canvas || !ctx) return;

  ctx.save();
  ctx.setTransform(1, 0, 0, 1, 0, 0);
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.restore();

  ctx.lineCap = 'round';
  ctx.lineJoin = 'round';
  ctx.strokeStyle = '#111111';
  ctx.lineWidth = 2.2;

  hasSignatureStroke.value = false;
  signatureImageError.value = '';
};

const saveDrawnSignature = () => {
  const field = editingSignatureField.value;
  const canvas = signaturePadCanvasRef.value;

  if (!field || !canvas) return;

  if (!hasSignatureStroke.value) {
    signatureImageError.value = 'Dibuja una firma antes de guardar.';
    return;
  }

  const dataUrl = canvas.toDataURL('image/png');

  revokeSignatureObjectUrl(field.signatureImageUrl);
  field.signatureImageUrl = dataUrl;
  field.signatureImageName = 'firma-dibujada.png';
  field.signatureImageFile = null;
  signatureImageError.value = '';
  closeSignatureImageDialog();
};

const removeSignatureImageFromField = () => {
  const field = editingSignatureField.value;
  if (!field) return;

  revokeSignatureObjectUrl(field.signatureImageUrl);
  field.signatureImageUrl = null;
  field.signatureImageName = null;
  field.signatureImageFile = null;
  signatureImageError.value = '';
};

const buildSignersPayload = () => {
  return form.value.signers.map((signer, idx) => ({
    name: signer.name,
    email: signer.email,
    phone: signer.phone || null,
    role: signer.role,
    order: idx + 1
  }));
};

const buildSignatureFieldsPayload = () => {
  return form.value.signatureFields.map((field) => {
    const payload = {
      id: field.id,
      signerId: field.signerId,
      signerName: field.signerName,
      signerEmail: field.signerEmail,
      page: field.page,
      x: field.x,
      y: field.y,
      width: field.width,
      height: field.height,
      signatureImageDataUrl: null,
      signatureImageFileKey: null
    };

    if (field.signatureImageFile instanceof File) {
      payload.signatureImageFileKey = field.id;
    } else if (typeof field.signatureImageUrl === 'string' && field.signatureImageUrl.startsWith('data:image/')) {
      payload.signatureImageDataUrl = field.signatureImageUrl;
    }

    return payload;
  });
};

const validateDocumentBeforeSubmit = () => {
  if (!form.value.pdfFile) {
    return 'Debes cargar un archivo PDF.';
  }

  if (!form.value.documentName || !form.value.documentName.trim()) {
    return 'Debes indicar el nombre del documento.';
  }

  if (form.value.signers.length === 0) {
    return 'Debes agregar al menos un firmante.';
  }

  const hasSignerWithoutField = form.value.signers.some((signer, idx) => {
    return signer.role === 'Firmante' && !hasSignatureFieldsSigner(idx);
  });

  if (hasSignerWithoutField) {
    return 'Cada firmante con rol Firmante debe tener al menos un campo de firma.';
  }

  if (form.value.signatureFields.length === 0) {
    return 'Debes posicionar al menos un campo de firma.';
  }

  if (!['sequential', 'parallel'].includes(form.value.signingOrder)) {
    return 'El orden de firma seleccionado no es válido.';
  }

  if (!Number.isFinite(form.value.expirationDays) || form.value.expirationDays < 1 || form.value.expirationDays > 365) {
    return 'La expiración debe estar entre 1 y 365 días.';
  }

  return null;
};

watch(signatureImageDialog, async (isOpen) => {
  if (!isOpen) {
    resetSignaturePadState();
    signaturePadContext.value = null;
    return;
  }

  signatureModalTab.value = 'draw';
  await setupSignaturePad();
});

watch(signatureModalTab, async (tab) => {
  if (!signatureImageDialog.value) return;

  signatureImageError.value = '';
  if (tab === 'draw') {
    await setupSignaturePad();
  }
});

const selectSignerForPlacing = (signerId) => {
  selectedSignerId.value = signerId;
  isPlacingSignature.value = true;
};

const prepareAnotherFieldForSigner = (signerId) => {
  selectSignerForPlacing(signerId);
};

const handlePDFClick = (event, pageNum) => {
  if (!isPlacingSignature.value || selectedSignerId.value === null) return;
  
  const canvas = document.getElementById(`pdf-canvas-${pageNum}`);
  if (!canvas) return;
  
  const rect = canvas.getBoundingClientRect();
  const canvasX = event.clientX - rect.left;
  const canvasY = event.clientY - rect.top;
  
  // Convertir a porcentaje relativo al canvas visible
  const x = (canvasX / rect.width) * 100;
  const y = (canvasY / rect.height) * 100;
  
  // Validar que esté dentro del canvas
  if (x < 0 || x > 100 || y < 0 || y > 100) return;
  
  const signer = form.value.signers[selectedSignerId.value];
  
  // Crear campo de firma
  const fieldId = `field-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
  form.value.signatureFields.push({
    id: fieldId,
    signerId: selectedSignerId.value,
    signerName: signer.name,
    signerEmail: signer.email,
    page: pageNum,
    x: Math.max(0, Math.min(100 - 15, x - 7.5)),
    y: Math.max(0, Math.min(100 - 10, y - 5)),
    width: 15,
    height: 10,
    signatureImageUrl: null,
    signatureImageName: null,
    signatureImageFile: null
  });

  // En touch mantenemos la selección para crear varios campos consecutivos.
  const keepPlacingOnTouch = isTouchPointerDevice.value;

  if (!keepPlacingOnTouch) {
    nextTick(() => {
      openSignatureImageDialog(fieldId);
    });

    // En desktop, tras colocar uno salimos del modo para evitar colocaciones accidentales.
    isPlacingSignature.value = false;
    selectedSignerId.value = null;
  }
};

const startDrag = (event, fieldId) => {
  const startX = event.clientX;
  const startY = event.clientY;
  let didMove = false;

  if (event.pointerType === 'touch') {
    event.preventDefault();
  }
  
  const field = form.value.signatureFields.find(f => f.id === fieldId);
  if (!field) return;
  
  const canvas = document.getElementById(`pdf-canvas-${field.page}`);
  if (!canvas) return;
  
  const rect = canvas.getBoundingClientRect();
  // Calcular posición en píxeles basada en porcentaje del canvas visible
  const fieldPixelX = (field.x / 100) * rect.width;
  const fieldPixelY = (field.y / 100) * rect.height;
  
  const dragOffset = {
    x: event.clientX - rect.left - fieldPixelX,
    y: event.clientY - rect.top - fieldPixelY
  };
  
  draggedField.value = fieldId;
  
  const handlePointerMove = (moveEvent) => {
    if (!draggedField.value) return;

    if (!didMove) {
      const deltaX = Math.abs(moveEvent.clientX - startX);
      const deltaY = Math.abs(moveEvent.clientY - startY);
      if (deltaX > 3 || deltaY > 3) {
        didMove = true;
      }
    }
    
    const moveField = form.value.signatureFields.find(f => f.id === draggedField.value);
    if (!moveField) return;
    
    const moveCanvas = document.getElementById(`pdf-canvas-${moveField.page}`);
    if (!moveCanvas) return;
    
    const parentRect = moveCanvas.getBoundingClientRect();
    // Convertir posición del mouse a porcentaje del canvas visible
    let newX = ((moveEvent.clientX - parentRect.left - dragOffset.x) / parentRect.width) * 100;
    let newY = ((moveEvent.clientY - parentRect.top - dragOffset.y) / parentRect.height) * 100;
    
    // Mantener dentro de los límites
    newX = Math.max(0, Math.min(100 - (moveField.width), newX));
    newY = Math.max(0, Math.min(100 - (moveField.height), newY));
    
    moveField.x = newX;
    moveField.y = newY;
  };
  
  const handlePointerUp = () => {
    draggedField.value = null;

    if (didMove) {
      suppressFieldClickId.value = fieldId;
      setTimeout(() => {
        if (suppressFieldClickId.value === fieldId) {
          suppressFieldClickId.value = null;
        }
      }, 180);
    }

    document.removeEventListener('pointermove', handlePointerMove);
    document.removeEventListener('pointerup', handlePointerUp);
  };
  
  document.addEventListener('pointermove', handlePointerMove);
  document.addEventListener('pointerup', handlePointerUp);
};

const removeSignatureField = (fieldId) => {
  const index = form.value.signatureFields.findIndex(f => f.id === fieldId);
  if (index !== -1) {
    revokeSignatureObjectUrl(form.value.signatureFields[index].signatureImageUrl);

    if (selectedFieldIdForImage.value === fieldId) {
      closeSignatureImageDialog();
    }

    form.value.signatureFields.splice(index, 1);
  }
};

const getSignerColor = (signerId) => {
  const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F'];
  return colors[signerId % colors.length];
};

const getSignatureFieldsForPage = (pageNum) => {
  return form.value.signatureFields.filter(field => field.page === pageNum);
};

const getSignatureCountForSigner = (signerId) => {
  return form.value.signatureFields.filter(field => field.signerId === signerId).length;
};

const hasSignatureFieldsSigner = (signerId) => {
  return getSignatureCountForSigner(signerId) > 0;
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
  // Remover campos de firma asociados a este firmante
  form.value.signatureFields = form.value.signatureFields.filter(f => f.signerId !== index);
};

const moveSigner = (from, to) => {
  const temp = form.value.signers[from];
  form.value.signers[from] = form.value.signers[to];
  form.value.signers[to] = temp;
};

const canProceedToStep4 = () => {
  // Debe existir al menos un firmante
  if (form.value.signers.length === 0) return false;
  
  // Cada firmante con rol "Firmante" debe tener al menos un campo de firma
  for (let i = 0; i < form.value.signers.length; i++) {
    if (form.value.signers[i].role === 'Firmante') {
      if (!hasSignatureFieldsSigner(i)) {
        return false;
      }
    }
  }
  
  return true;
};

const submitDocument = async () => {
  if (submitting.value) return;

  submitError.value = '';

  const validationError = validateDocumentBeforeSubmit();
  if (validationError) {
    submitError.value = validationError;
    return;
  }

  submitting.value = true;

  try {
    const signersPayload = buildSignersPayload();
    const signatureFieldsPayload = buildSignatureFieldsPayload();

    const payload = new FormData();
    payload.append('pdfFile', form.value.pdfFile);
    payload.append('documentName', form.value.documentName.trim());
    payload.append('description', form.value.description?.trim() || '');
    payload.append('signers', JSON.stringify(signersPayload));
    payload.append('signatureFields', JSON.stringify(signatureFieldsPayload));
    payload.append('signingOrder', form.value.signingOrder);
    payload.append('expirationDays', String(form.value.expirationDays));
    payload.append('notifyOnSigning', form.value.notifyOnSigning ? '1' : '0');
    payload.append('notifyOnRejection', form.value.notifyOnRejection ? '1' : '0');
    payload.append('notifyOnExpiration', form.value.notifyOnExpiration ? '1' : '0');

    for (const field of form.value.signatureFields) {
      if (field.signatureImageFile instanceof File) {
        payload.append(`signatureImages[${field.id}]`, field.signatureImageFile, field.signatureImageFile.name);
      }
    }

    const response = await http.post('/signature-documents', payload, {
      headers: {
        Accept: 'application/json'
      }
    });

    const data = response?.data || {};

    if (data.success === false) {
      const validationErrors = data?.errors
        ? Object.values(data.errors).flat().filter(Boolean)
        : [];

      submitError.value = validationErrors[0] || data?.message || 'No se pudo crear el documento.';
      return;
    }

    successDialog.value = true;
  } catch (error) {
    const errorData = error?.response?.data || {};
    const validationErrors = errorData?.errors
      ? Object.values(errorData.errors).flat().filter(Boolean)
      : [];

    submitError.value =
      validationErrors[0] ||
      errorData?.message ||
      error?.message ||
      'Ocurrió un error de red al crear el documento.';
  } finally {
    submitting.value = false;
  }
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
    signatureFields: [],
    signingOrder: 'sequential',
    expirationDays: 30,
    notifyOnSigning: true,
    notifyOnRejection: true,
    notifyOnExpiration: true
  };
  newSigner.value = { name: '', email: '', phone: '', role: 'Firmante' };
  selectedSignerId.value = null;
  isPlacingSignature.value = false;
  pdfDocument.value = null;
  revokeAllSignatureImageUrls();
  pdfPages.value = [];
  totalPages.value = 0;
  pdfLoading.value = false;
  pdfError.value = null;
  loadRequestId.value += 1;
  cancelCurrentLoadingTask();
  cancelActiveRenderTasks();
  closeSignatureImageDialog();
  submitError.value = '';
};
</script>

<style scoped>
.new-document-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%);
  padding: 2rem 1rem;
  margin: 0;
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

/* Zoom Controls */
.zoom-controls {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  padding: 0.75rem;
  background: #0d0d0d;
  border-radius: 8px;
  border: 1px solid rgba(212, 175, 55, 0.1);
  flex-wrap: wrap;
}

.zoom-controls .v-btn {
  min-width: 50px;
}

/* PDF Viewer Styles */
.pdf-viewer-wrapper {
  display: flex;
  flex-direction: column;
  flex: 1 1 auto;
  height: 100%;
  min-height: 0;
  border: 1px solid rgba(212, 175, 55, 0.2);
  border-radius: 8px;
  background: #2a2a2a;
  overflow: hidden;
}

.signature-workspace-row {
  height: 700px;
  display: flex;
  flex-wrap: nowrap;
  align-items: stretch;
  min-height: 0;
}

.signature-workspace-row > .v-col {
  height: 100%;
  min-height: 0;
}

.pdf-viewer-col {
  height: 100%;
  min-height: 0;
}

.pdf-container {
  flex: 1 1 auto;
  min-height: 0;
  height: 100%;
  max-height: 100%;
  overflow-y: auto;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  touch-action: pan-x pan-y;
  scrollbar-gutter: stable;
  background: linear-gradient(135deg, #5a5a5a 0%, #4a4a4a 100%);
  padding: 2rem;
  position: relative;
  transition: cursor 0.2s ease;
}

.pdf-container::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

.pdf-container::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.15);
}

.pdf-container::-webkit-scrollbar-thumb {
  background: rgba(212, 175, 55, 0.6);
  border-radius: 8px;
}

.pdf-container::-webkit-scrollbar-thumb:hover {
  background: rgba(212, 175, 55, 0.85);
}

@media (max-width: 1264px) {
  .signature-workspace-row {
    height: auto !important;
    min-height: 0;
    flex-wrap: wrap;
  }

  .pdf-viewer-col {
    height: 70vh;
    min-height: 460px;
  }

  .signers-panel {
    max-height: 40vh;
  }
}

.pdf-container.placing-mode {
  cursor: crosshair;
}

.placing-status {
  position: sticky;
  top: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: rgba(25, 118, 210, 0.2);
  border: 1px solid rgba(25, 118, 210, 0.3);
  border-radius: 8px;
  color: #64b5f6;
  font-weight: 600;
  margin-bottom: 1rem;
  z-index: 100;
}

.placing-status strong {
  color: #90caf9;
  margin-left: 0.25rem;
  margin-right: 0.25rem;
}

.pages-container {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: max-content;
  min-width: 100%;
  gap: 2rem;
}

.page-wrapper {
  width: fit-content;
  max-width: none;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
}

.page-number {
  text-align: center;
  color: #b0b0b0;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.page-content {
  position: relative;
  display: inline-block;
  width: auto;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  border-radius: 4px;
}

.pdf-canvas {
  display: block;
  width: auto;
  height: auto;
  border-radius: 2px;
}

.pdf-canvas.cursor-crosshair {
  cursor: crosshair;
}

.pdf-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 600px;
  background: white;
  border: 2px dashed #ccc;
  border-radius: 8px;
  color: #999;
}

.signature-overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

/* Signature Field Styles */
.signature-field-box {
  position: absolute;
  background: rgba(255, 255, 255, 0.08);
  border: 2.5px dashed #FF6B6B;
  border-radius: 4px;
  cursor: grab;
  user-select: none;
  box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: auto;
  transition: box-shadow 0.2s ease;
  z-index: 10;
}

.signature-field-box:hover {
  box-shadow: 0 4px 16px rgba(255, 107, 107, 0.5);
}

.signature-field-box:active {
  cursor: grabbing;
}

.signature-field-content {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
  padding: 4px;
  text-align: center;
  gap: 2px;
}

.signature-sign-area {
  width: 100%;
  flex: 1;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  min-height: 0;
}

.signature-signer-name {
  font-size: 0.65rem;
  font-weight: 700;
  color: #FF6B6B;
  line-height: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 95%;
}

.signature-line {
  width: 85%;
  height: 1px;
  background: #FF6B6B;
  flex-shrink: 0;
}

.signature-label {
  font-size: 0.55rem;
  color: #FF6B6B;
  font-weight: 600;
  line-height: 1;
}

.signature-upload-hint {
  font-size: 0.48rem;
  color: rgba(255, 107, 107, 0.8);
  line-height: 1;
}

.signature-image {
  width: 96%;
  height: 100%;
  object-fit: contain;
  object-position: bottom center;
}

.signature-edit-btn {
  position: absolute;
  top: -8px;
  left: -8px;
  background: rgba(255, 255, 255, 0.95) !important;
  color: #FF6B6B !important;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  z-index: 21;
}

.signature-clone-btn {
  position: absolute;
  bottom: -8px;
  right: 14px;
  background: rgba(255, 255, 255, 0.95) !important;
  color: #FF6B6B !important;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  z-index: 21;
}

.signature-remove-btn {
  position: absolute;
  top: -8px;
  right: -8px;
  background: white !important;
  color: #FF6B6B !important;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
  z-index: 20;
}

.signature-file-input {
  display: none;
}

.signature-pad-container {
  width: 100%;
  border: 1px dashed rgba(255, 107, 107, 0.35);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.96);
  overflow: hidden;
}

.signature-pad-canvas {
  width: 100%;
  height: 210px;
  display: block;
  background: transparent;
  touch-action: none;
  cursor: crosshair;
}

@media (max-width: 768px) {
  .signature-pad-canvas {
    height: 190px;
  }
}

.signature-image-dialog-preview {
  width: 100%;
  min-height: 180px;
  border: 1px dashed rgba(212, 175, 55, 0.35);
  border-radius: 8px;
  padding: 12px;
  background: rgba(255, 255, 255, 0.03);
  display: flex;
  align-items: center;
  justify-content: center;
}

.signature-image-dialog-preview-img {
  max-width: 100%;
  max-height: 220px;
  object-fit: contain;
}

/* Signers Panel */
.signers-panel {
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  background: #1a1a1a;
  border: 1px solid rgba(212, 175, 55, 0.2);
  border-radius: 8px;
  overflow-y: auto;
}

.signers-section {
  flex: 1;
  overflow-y: auto;
}

.signers-list {
  background: transparent;
}

.all-assigned-banner {
  margin-bottom: 8px;
}

.signer-list-item {
  background: #0d0d0d;
  border-radius: 8px;
  margin-bottom: 12px;
  border: 1px solid rgba(212, 175, 55, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.signer-list-item.pending:hover {
  background: rgba(212, 175, 55, 0.1);
  border-color: #d4af37;
  transform: translateX(4px);
}

.signer-list-item.assigned {
  background: rgba(102, 187, 106, 0.1);
  border-color: #66bb6a;
}

.signer-list-item.selected {
  background: rgba(25, 118, 210, 0.15);
  border-color: #1976d2;
}

.signer-list-item.not-required {
  opacity: 1;
}

.signer-list-item .gap-1 {
  gap: 0.25rem;
}

:deep(.signers-list .v-list-item__prepend) {
  align-self: center;
  margin-inline-end: 12px;
  opacity: 1 !important;
}

.signer-avatar {
  flex-shrink: 0;
  opacity: 1 !important;
}

.signer-avatar-number {
  color: #ffffff;
  font-size: 0.85rem;
  line-height: 1;
  font-weight: 700;
}

.assign-signature-btn {
  opacity: 1 !important;
}

.help-section {
  background: rgba(25, 118, 210, 0.08);
  border: 1px solid rgba(25, 118, 210, 0.2);
  border-radius: 8px;
  padding: 1rem;
}

.help-list {
  list-style-position: inside;
  margin: 0;
  padding-left: 0;
}

.help-list li {
  margin-bottom: 0.5rem;
  color: #b0b0b0;
}

</style>
