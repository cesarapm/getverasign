<template>
  <v-container class="settings-page">
    <!-- Header -->
    <section class="settings-hero py-12">
      <v-row justify="center">
        <v-col cols="12" lg="10">
          <h1 class="text-h3 font-weight-bold mb-2">Configuración</h1>
          <p class="text-body-1 text-medium-emphasis">
            Administra tu perfil, empresa y preferencias de la plataforma.
          </p>
        </v-col>
      </v-row>
    </section>

    <!-- Settings Content -->
    <v-row justify="center">
      <v-col cols="12" lg="10">
        <!-- Tabs -->
        <v-tabs v-model="currentTab" class="settings-tabs mb-8">
          <v-tab value="profile" class="tab-title">
            <v-icon start>mdi-account</v-icon>
            Perfil
          </v-tab>
          <v-tab value="company" class="tab-title">
            <v-icon start>mdi-briefcase</v-icon>
            Empresa
          </v-tab>
          <v-tab value="preferences" class="tab-title">
            <v-icon start>mdi-cog</v-icon>
            Preferencias
          </v-tab>
          <v-tab value="security" class="tab-title">
            <v-icon start>mdi-shield-lock</v-icon>
            Seguridad
          </v-tab>
          <v-tab value="billing" class="tab-title">
            <v-icon start>mdi-credit-card</v-icon>
            Facturación
          </v-tab>
        </v-tabs>

        <!-- Tab Content -->
        <v-window v-model="currentTab">
          <!-- Profile Tab -->
          <v-window-item value="profile">
            <v-card elevation="0" outlined>
              <v-card-title class="pa-6 d-flex align-center">
                <v-icon start>mdi-account-circle</v-icon>
                Perfil Personal
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <v-row>
                  <v-col cols="12" md="4" class="text-center">
                    <div class="profile-avatar mb-4">
                      <v-avatar size="120" color="primary" class="text-white text-h4">
                        {{ profileData.name.charAt(0) }}
                      </v-avatar>
                    </div>
                    <v-btn color="secondary" variant="outlined" size="small">
                      Cambiar Foto
                    </v-btn>
                  </v-col>

                  <v-col cols="12" md="8">
                    <v-row>
                      <v-col cols="12" md="6" class="mb-4">
                        <v-text-field
                          v-model="profileData.name"
                          label="Nombre Completo *"
                          variant="outlined"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" class="mb-4">
                        <v-text-field
                          v-model="profileData.email"
                          label="Email *"
                          type="email"
                          variant="outlined"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" class="mb-4">
                        <v-text-field
                          v-model="profileData.phone"
                          label="Teléfono"
                          variant="outlined"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" class="mb-4">
                        <v-text-field
                          v-model="profileData.position"
                          label="Cargo"
                          variant="outlined"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" class="mb-4">
                        <v-textarea
                          v-model="profileData.bio"
                          label="Biografía"
                          variant="outlined"
                          rows="3"
                          placeholder="Cuéntanos sobre ti..."
                        ></v-textarea>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>

                <v-divider class="my-6"></v-divider>

                <div class="text-right">
                  <v-btn variant="outlined" class="mr-2">Cancelar</v-btn>
                  <v-btn color="primary" @click="saveChanges">Guardar Cambios</v-btn>
                </div>
              </v-card-text>
            </v-card>
          </v-window-item>

          <!-- Company Tab -->
          <v-window-item value="company">
            <v-card elevation="0" outlined>
              <v-card-title class="pa-6 d-flex align-center">
                <v-icon start>mdi-office-building</v-icon>
                Información de la Empresa
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <v-row>
                  <v-col cols="12" md="6" class="mb-4">
                    <v-text-field
                      v-model="companyData.name"
                      label="Nombre de la Empresa *"
                      variant="outlined"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="mb-4">
                    <v-text-field
                      v-model="companyData.taxId"
                      label="RFC/NIT/CIF *"
                      variant="outlined"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="mb-4">
                    <v-text-field
                      v-model="companyData.website"
                      label="Sitio Web"
                      variant="outlined"
                      type="url"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="mb-4">
                    <v-textarea
                      v-model="companyData.address"
                      label="Dirección"
                      variant="outlined"
                      rows="2"
                    ></v-textarea>
                  </v-col>
                  <v-col cols="12" md="4" class="mb-4">
                    <v-text-field
                      v-model="companyData.city"
                      label="Ciudad"
                      variant="outlined"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4" class="mb-4">
                    <v-text-field
                      v-model="companyData.state"
                      label="Estado/Provincia"
                      variant="outlined"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4" class="mb-4">
                    <v-text-field
                      v-model="companyData.zipCode"
                      label="Código Postal"
                      variant="outlined"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="mb-4">
                    <v-select
                      v-model="companyData.country"
                      :items="countryOptions"
                      label="País *"
                      variant="outlined"
                    ></v-select>
                  </v-col>
                </v-row>

                <v-divider class="my-6"></v-divider>

                <div class="text-right">
                  <v-btn variant="outlined" class="mr-2">Cancelar</v-btn>
                  <v-btn color="primary" @click="saveChanges">Guardar Cambios</v-btn>
                </div>
              </v-card-text>
            </v-card>
          </v-window-item>

          <!-- Preferences Tab -->
          <v-window-item value="preferences">
            <v-card elevation="0" outlined>
              <v-card-title class="pa-6 d-flex align-center">
                <v-icon start>mdi-palette</v-icon>
                Preferencias
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <h3 class="text-body-1 font-weight-bold mb-4">Notificaciones</h3>

                <v-checkbox
                  v-model="preferences.emailNotifications"
                  label="Recibir notificaciones por email"
                  class="mb-3"
                ></v-checkbox>

                <v-checkbox
                  v-model="preferences.notifyOnSigning"
                  label="Notificarme cuando alguien firme"
                  class="mb-3"
                ></v-checkbox>

                <v-checkbox
                  v-model="preferences.notifyOnRejection"
                  label="Notificarme cuando alguien rechace"
                  class="mb-3"
                ></v-checkbox>

                <v-checkbox
                  v-model="preferences.notifyOnExpiration"
                  label="Notificarme sobre documentos próximos a expirar"
                  class="mb-6"
                ></v-checkbox>

                <v-divider class="my-6"></v-divider>

                <h3 class="text-body-1 font-weight-bold mb-4">Apariencia</h3>

                <v-select
                  v-model="preferences.theme"
                  :items="['Claro', 'Oscuro', 'Sistema']"
                  label="Tema"
                  variant="outlined"
                  class="mb-6"
                ></v-select>

                <v-divider class="my-6"></v-divider>

                <h3 class="text-body-1 font-weight-bold mb-4">Configuración Regional</h3>

                <v-row>
                  <v-col cols="12" md="6" class="mb-4">
                    <v-select
                      v-model="preferences.language"
                      :items="languageOptions"
                      label="Idioma"
                      variant="outlined"
                      item-title="label"
                      item-value="value"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="6" class="mb-4">
                    <v-select
                      v-model="preferences.timezone"
                      :items="timezoneOptions"
                      label="Zona Horaria"
                      variant="outlined"
                    ></v-select>
                  </v-col>
                </v-row>

                <v-divider class="my-6"></v-divider>

                <div class="text-right">
                  <v-btn variant="outlined" class="mr-2">Cancelar</v-btn>
                  <v-btn color="primary" @click="saveChanges">Guardar Cambios</v-btn>
                </div>
              </v-card-text>
            </v-card>
          </v-window-item>

          <!-- Security Tab -->
          <v-window-item value="security">
            <v-card elevation="0" outlined class="mb-6">
              <v-card-title class="pa-6 d-flex align-center">
                <v-icon start>mdi-lock</v-icon>
                Contraseña
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <v-text-field
                  v-model="securityData.currentPassword"
                  label="Contraseña Actual *"
                  type="password"
                  variant="outlined"
                  class="mb-4"
                ></v-text-field>

                <v-text-field
                  v-model="securityData.newPassword"
                  label="Nueva Contraseña *"
                  type="password"
                  variant="outlined"
                  class="mb-4"
                ></v-text-field>

                <v-text-field
                  v-model="securityData.confirmPassword"
                  label="Confirmar Contraseña *"
                  type="password"
                  variant="outlined"
                  class="mb-6"
                ></v-text-field>

                <div class="text-right">
                  <v-btn variant="outlined" class="mr-2" @click="resetPassword">Cancelar</v-btn>
                  <v-btn color="primary" @click="changePassword">Cambiar Contraseña</v-btn>
                </div>
              </v-card-text>
            </v-card>

            <v-card elevation="0" outlined class="mb-6">
              <v-card-title class="pa-6 d-flex align-center">
                <v-icon start>mdi-security-lock</v-icon>
                Autenticación de Dos Factores
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <v-alert
                  v-if="!securityData.twoFactorEnabled"
                  type="info"
                  variant="tonal"
                  class="mb-4"
                >
                  Mejora la seguridad de tu cuenta habilitando autenticación de dos factores.
                </v-alert>

                <v-btn
                  :color="securityData.twoFactorEnabled ? 'error' : 'primary'"
                  :variant="securityData.twoFactorEnabled ? 'outlined' : 'tonal'"
                  @click="toggle2FA"
                >
                  {{ securityData.twoFactorEnabled ? 'Desactivar 2FA' : 'Activar 2FA' }}
                </v-btn>
              </v-card-text>
            </v-card>

            <v-card elevation="0" outlined>
              <v-card-title class="pa-6 d-flex align-center">
                <v-icon start>mdi-history</v-icon>
                Sesiones Activas
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <v-list class="pa-0">
                  <template v-for="session in activeSessions" :key="session.id">
                    <v-list-item class="mb-4">
                      <template #prepend>
                        <v-icon :color="session.current ? 'success' : 'secondary'">
                          mdi-devices
                        </v-icon>
                      </template>

                      <v-list-item-title>
                        {{ session.device }}
                        <v-chip
                          v-if="session.current"
                          size="small"
                          color="success"
                          variant="tonal"
                          label
                          class="ml-2"
                        >
                          Actual
                        </v-chip>
                      </v-list-item-title>

                      <v-list-item-subtitle>
                        {{ session.location }} • {{ session.lastActivity }}
                      </v-list-item-subtitle>

                      <template #append>
                        <v-btn
                          icon="mdi-close"
                          variant="text"
                          size="small"
                          @click="closeSession(session.id)"
                          :disabled="session.current"
                        ></v-btn>
                      </template>
                    </v-list-item>
                  </template>
                </v-list>
              </v-card-text>
            </v-card>
          </v-window-item>

          <!-- Billing Tab -->
          <v-window-item value="billing">
            <v-card elevation="0" outlined class="mb-6">
              <v-card-title class="pa-6 d-flex align-center">
                <v-icon start>mdi-credit-card</v-icon>
                Plan Actual
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <v-row>
                  <v-col cols="12" md="6">
                    <div class="plan-card">
                      <h3 class="text-h6 mb-2">Plan Profesional</h3>
                      <div class="text-h4 font-weight-bold mb-2">$99<span class="text-body-2">/mes</span></div>
                      <ul class="plan-features">
                        <li>Hasta 100 documentos/mes</li>
                        <li>5 firmantes simultáneos</li>
                        <li>Almacenamiento ilimitado</li>
                        <li>Soporte prioritario</li>
                      </ul>
                      <v-btn color="primary" variant="tonal" block class="mt-4">
                        Cambiar Plan
                      </v-btn>
                    </div>
                  </v-col>
                  <v-col cols="12" md="6">
                    <div class="plan-info">
                      <div class="info-item mb-4">
                        <span class="text-medium-emphasis">Próxima Facturación</span>
                        <div class="font-weight-bold">15 de Febrero de 2024</div>
                      </div>
                      <div class="info-item mb-4">
                        <span class="text-medium-emphasis">Estado de Pago</span>
                        <v-chip color="success" variant="tonal" label size="small">
                          Al Día
                        </v-chip>
                      </div>
                      <div class="info-item">
                        <span class="text-medium-emphasis">Método de Pago</span>
                        <div class="font-weight-bold">Tarjeta ••••4242</div>
                      </div>
                    </div>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>

            <v-card elevation="0" outlined>
              <v-card-title class="pa-6">Historial de Facturación</v-card-title>

              <v-divider></v-divider>

              <v-data-table
                :headers="billingHeaders"
                :items="billingHistory"
                class="billing-table"
              >
                <template #[`item.date`]="{ item }">
                  {{ formatDate(item.date) }}
                </template>

                <template #[`item.actions`]="{ item }">
                  <v-btn icon="mdi-download" variant="text" size="small"></v-btn>
                </template>
              </v-data-table>
            </v-card>
          </v-window-item>
        </v-window>
      </v-col>
    </v-row>

    <!-- Success Snackbar -->
    <v-snackbar v-model="showSuccessMessage" color="success" location="bottom-right">
      {{ successMessage }}
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';

// State
const currentTab = ref('profile');
const showSuccessMessage = ref(false);
const successMessage = ref('');

// Profile Data
const profileData = ref({
  name: 'Juan García Martínez',
  email: 'juan.garcia@example.com',
  phone: '+34 912 345 678',
  position: 'Director Legal',
  bio: 'Profesional en gestión de documentos con 10 años de experiencia.'
});

// Company Data
const companyData = ref({
  name: 'García & Asociados S.L.',
  taxId: 'ES12345678A',
  website: 'www.garciaasociados.com',
  address: 'Avenida Principal 123',
  city: 'Madrid',
  state: 'Madrid',
  zipCode: '28001',
  country: 'España'
});

// Preferences
const preferences = ref({
  emailNotifications: true,
  notifyOnSigning: true,
  notifyOnRejection: true,
  notifyOnExpiration: true,
  theme: 'Claro',
  language: 'es',
  timezone: 'Europe/Madrid'
});

// Security Data
const securityData = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
  twoFactorEnabled: false
});

// Mock data
const countryOptions = ['España', 'México', 'Argentina', 'Colombia', 'Perú', 'Chile'];
const languageOptions = [
  { label: 'Español', value: 'es' },
  { label: 'Inglés', value: 'en' },
  { label: 'Portugués', value: 'pt' }
];
const timezoneOptions = [
  'Europe/Madrid',
  'America/Mexico_City',
  'America/Argentina/Buenos_Aires',
  'America/Bogota',
  'America/Lima'
];

const activeSessions = ref([
  {
    id: 1,
    device: 'Chrome en Windows 10',
    location: 'Madrid, España',
    lastActivity: 'Hace 5 minutos',
    current: true
  },
  {
    id: 2,
    device: 'Safari en MacOS',
    location: 'Madrid, España',
    lastActivity: 'Ayer',
    current: false
  },
  {
    id: 3,
    device: 'Chrome en iPhone',
    location: 'Madrid, España',
    lastActivity: 'Hace 2 días',
    current: false
  }
]);

const billingHeaders = [
  { title: 'Fecha', key: 'date', sortable: true },
  { title: 'Descripción', key: 'description', sortable: false },
  { title: 'Monto', key: 'amount', sortable: true },
  { title: 'Estado', key: 'status', sortable: true },
  { title: 'Acciones', key: 'actions', sortable: false }
];

const billingHistory = ref([
  {
    date: new Date('2024-01-15'),
    description: 'Suscripción - Plan Profesional',
    amount: '$99.00',
    status: 'Pagado',
    invoice: 'INV-2024-001'
  },
  {
    date: new Date('2023-12-15'),
    description: 'Suscripción - Plan Profesional',
    amount: '$99.00',
    status: 'Pagado',
    invoice: 'INV-2023-012'
  },
  {
    date: new Date('2023-11-15'),
    description: 'Suscripción - Plan Profesional',
    amount: '$99.00',
    status: 'Pagado',
    invoice: 'INV-2023-011'
  }
]);

// Methods
const formatDate = (date) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return date.toLocaleDateString('es-ES', options);
};

const saveChanges = () => {
  showSuccessMessage.value = true;
  successMessage.value = 'Cambios guardados correctamente';
  setTimeout(() => {
    showSuccessMessage.value = false;
  }, 3000);
};

const changePassword = () => {
  if (securityData.value.newPassword === securityData.value.confirmPassword) {
    showSuccessMessage.value = true;
    successMessage.value = 'Contraseña cambiada exitosamente';
    resetPassword();
    setTimeout(() => {
      showSuccessMessage.value = false;
    }, 3000);
  }
};

const resetPassword = () => {
  securityData.value = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: '',
    twoFactorEnabled: securityData.value.twoFactorEnabled
  };
};

const toggle2FA = () => {
  securityData.value.twoFactorEnabled = !securityData.value.twoFactorEnabled;
  showSuccessMessage.value = true;
  successMessage.value = securityData.value.twoFactorEnabled 
    ? 'Autenticación de dos factores activada' 
    : 'Autenticación de dos factores desactivada';
  setTimeout(() => {
    showSuccessMessage.value = false;
  }, 3000);
};

const closeSession = (id) => {
  const index = activeSessions.value.findIndex(s => s.id === id);
  if (index > -1) {
    activeSessions.value.splice(index, 1);
  }
};
</script>

<style scoped>
.settings-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0a 0%, #0d0d0d 100%);
  padding: 2rem 1rem;
  margin: 0;
  width: 100%;
  max-width: 100%;
}

@media (min-width: 960px) {
  .settings-page {
    padding: 3rem 2rem 4rem 2rem;
  }
}

.settings-hero {
  background: #1a1a1a;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.2);
}

:deep(.settings-tabs) {
  background: #1a1a1a;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.tab-title {
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.05em;
  font-weight: 600;
}

.profile-avatar {
  position: relative;
}

.plan-card {
  border: 2px solid rgba(212, 175, 55, 0.3);
  border-radius: 12px;
  padding: 2rem;
  background: #1a1a1a;
}

.plan-features {
  list-style: none;
  padding: 0;
  margin: 0;
}

.plan-features li {
  padding: 0.5rem 0;
  padding-left: 1.5rem;
  position: relative;
}

.plan-features li:before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #d4af37;
  font-weight: bold;
}

.plan-info {
  padding: 1rem;
  background: #0d0d0d;
  border-radius: 8px;
  border: 1px solid rgba(212, 175, 55, 0.15);
}

.info-item {
  padding: 0.75rem 0;
  color: #b0b0b0;
}

:deep(.billing-table) {
  background: #1a1a1a;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

:deep(.billing-table .v-data-table__thead) {
  background: #2a2a2a !important;
}

:deep(.billing-table .v-data-table__thead .v-data-table__th) {
  color: #ffffff !important;
  font-weight: 600 !important;
}

:deep(.billing-table .v-data-table__tbody td) {
  color: #b0b0b0 !important;
}

:deep(.billing-table .v-data-table__tr:hover) {
  background-color: #0d0d0d !important;
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
