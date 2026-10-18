<template>
  <div class="auth-container">
    <v-container class="auth-wrapper">
      <v-row class="h-screen justify-center align-center">
        <v-col cols="12" sm="10" md="6" lg="5" xl="4">
          <!-- Card de Registro -->
          <v-card
            elevation="0"
            outlined
            class="pa-8 border-gold"
          >
            <!-- Header -->
            <div class="text-center mb-8">
              <h1 class="text-h5 font-weight-bold mb-2">
                Crear Cuenta
              </h1>
              <p class="text-body-2 text-medium-emphasis">
                Regístrate para comenzar a firmar digitalmente
              </p>
            </div>

            <!-- Error Alert -->
            <v-alert
              v-if="error"
              type="error"
              variant="tonal"
              closable
              class="mb-6"
              @click:close="error = ''"
            >
              {{ error }}
            </v-alert>

            <!-- Form -->
            <v-form @submit.prevent="handleRegister" class="form-auth">
              <!-- Name Field -->
              <v-text-field
                v-model="formData.name"
                label="Nombre Completo"
                type="text"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-account-outline"
                class="mb-4"
                :disabled="isLoading"
                :rules="[v => !!v || 'El nombre es requerido']"
              />

              <!-- Email Field -->
              <v-text-field
                v-model="formData.email"
                label="Correo Electrónico"
                type="email"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-email-outline"
                class="mb-4"
                :disabled="isLoading"
                :rules="[
                  v => !!v || 'El correo es requerido',
                  v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'El correo no es válido'
                ]"
              />

              <!-- Password Field -->
              <v-text-field
                v-model="formData.password"
                label="Contraseña"
                :type="showPassword ? 'text' : 'password'"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-lock-outline"
                :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                @click:append-inner="showPassword = !showPassword"
                class="mb-4"
                :disabled="isLoading"
                :rules="[
                  v => !!v || 'La contraseña es requerida',
                  v => v.length >= 8 || 'Mínimo 8 caracteres'
                ]"
              />

              <!-- Confirm Password Field -->
              <v-text-field
                v-model="formData.passwordConfirmation"
                label="Confirmar Contraseña"
                :type="showPasswordConfirm ? 'text' : 'password'"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-lock-check-outline"
                :append-inner-icon="showPasswordConfirm ? 'mdi-eye-off' : 'mdi-eye'"
                @click:append-inner="showPasswordConfirm = !showPasswordConfirm"
                class="mb-4"
                :disabled="isLoading"
                :rules="[
                  v => !!v || 'Debes confirmar la contraseña',
                  v => v === formData.password || 'Las contraseñas no coinciden'
                ]"
              />

              <!-- Terms Checkbox -->
              <v-checkbox
                v-model="formData.acceptTerms"
                class="mb-6"
                :disabled="isLoading"
              >
                <template #label>
                  <span class="text-body-2">
                    Acepto los
                    <a href="#" class="text-gold text-decoration-none">
                      términos y condiciones
                    </a>
                  </span>
                </template>
              </v-checkbox>

              <!-- Register Button -->
              <v-btn
                type="submit"
                size="large"
                class="w-100 mb-4 btn-gold"
                :loading="isLoading"
                :disabled="isLoading || !formData.acceptTerms"
              >
                <template #prepend>
                  <v-icon v-if="!isLoading">mdi-account-plus</v-icon>
                </template>
                Crear Cuenta
              </v-btn>

              <!-- Divider -->
              <v-divider class="my-6" />

              <!-- Login Link -->
              <p class="text-center text-body-2">
                ¿Ya tienes cuenta?
                <router-link
                  :to="{ name: 'Login' }"
                  class="text-gold font-weight-bold text-decoration-none"
                >
                  Inicia sesión aquí
                </router-link>
              </p>
            </v-form>
          </v-card>

          <!-- Footer Info -->
          <div class="text-center mt-8 text-body-2 text-medium-emphasis">
            <p>Plataforma de Firmas Electrónicas</p>
            <p>© 2024 GetVeraSign. Todos los derechos reservados.</p>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useAuth } from '../composables/useAuth';

const { register, isLoading: authLoading, error: authError } = useAuth();

// Estado del formulario
const formData = ref({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: '',
  acceptTerms: false,
});

const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const error = ref('');

// Sincronizar error del composable con el local
watch(authError, (newError) => {
  error.value = newError;
});

// Manejador del registro
const handleRegister = async () => {
  // Validar campos
  if (!formData.value.name || !formData.value.email || 
      !formData.value.password || !formData.value.passwordConfirmation) {
    error.value = 'Por favor completa todos los campos.';
    return;
  }

  if (formData.value.password.length < 8) {
    error.value = 'La contraseña debe tener mínimo 8 caracteres.';
    return;
  }

  if (formData.value.password !== formData.value.passwordConfirmation) {
    error.value = 'Las contraseñas no coinciden.';
    return;
  }

  if (!formData.value.acceptTerms) {
    error.value = 'Debes aceptar los términos y condiciones.';
    return;
  }

  const result = await register(
    formData.value.name,
    formData.value.email,
    formData.value.password,
    formData.value.passwordConfirmation
  );

  if (!result.success) {
    error.value = result.error;
  }
};

// Propiedad computada para el loading
const isLoading = authLoading;
</script>

<style scoped>
/* ============================================
   CONTENEDOR PRINCIPAL - FONDO OSCURO
   ============================================ */
.auth-container {
  background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.auth-wrapper {
  width: 100%;
  height: 100%;
}

.h-screen {
  height: 100vh;
}

/* ============================================
   TARJETA - FONDO OSCURO CON BORDE DORADO
   ============================================ */
.border-gold {
  border: 2px solid #d4af37 !important;
  background-color: #0a0a0a !important;
  box-shadow: 0 0 20px rgba(212, 175, 55, 0.1) !important;
}

/* ============================================
   ESTILOS DE TEXTO - MÁXIMO CONTRASTE
   ============================================ */

/* Títulos - Blanco puro */
:deep(.text-h5) {
  color: #ffffff !important;
}

/* Texto secundario - Gris claro legible */
:deep(.text-body-2) {
  color: #b0b0b0 !important;
}

:deep(.text-medium-emphasis) {
  color: #b0b0b0 !important;
}

/* ============================================
   INPUTS - FONDO OSCURO, BORDE DORADO
   ============================================ */

/* Campo de entrada predeterminado */
:deep(.v-field__outline) {
  --v-border-color: rgba(212, 175, 55, 0.3);
}

:deep(.v-field) {
  background-color: #1a1a1a !important;
}

/* Input enfocado - Borde dorado brillante */
:deep(.v-field--focused .v-field__outline) {
  --v-border-color: #d4af37;
}

/* Texto dentro del input - Blanco */
:deep(.v-field__input) {
  color: #ffffff !important;
  caret-color: #d4af37;
}

:deep(.v-field__input::placeholder) {
  color: #808080 !important;
  opacity: 1;
}

/* Labels - Gris claro, destacan al enfocar */
:deep(.v-label) {
  color: #9e9e9e !important;
  font-size: 0.875rem;
}

:deep(.v-field--focused .v-label) {
  color: #d4af37 !important;
}

:deep(.v-field--focused.v-field--variant-outlined .v-label) {
  color: #d4af37 !important;
}

/* Iconos dentro del input - Dorado */
:deep(.v-field__prepend-inner) {
  color: #d4af37 !important;
}

:deep(.v-field__append-inner) {
  color: #d4af37 !important;
}

/* Texto en inputs deshabilitados */
:deep(.v-field--disabled .v-field__input) {
  color: #606060 !important;
  opacity: 0.6;
}

/* ============================================
   BOTONES - DORADO CON TEXTO NEGRO CONTRASTE
   ============================================ */
.btn-gold {
  background-color: #d4af37 !important;
  color: #000000 !important;
  font-weight: 700;
  letter-spacing: 0.5px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-transform: uppercase;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
}

.btn-gold:hover {
  background-color: #f0d85d !important;
  box-shadow: 0 8px 24px rgba(212, 175, 55, 0.4);
  transform: translateY(-2px);
}

.btn-gold:active {
  background-color: #c9a227 !important;
  transform: translateY(0);
}

:deep(.btn-gold .v-btn__content) {
  color: #000000 !important;
}

/* ============================================
   CHECKBOXES Y OTROS CONTROLES
   ============================================ */
:deep(.v-checkbox) {
  margin: 0 !important;
}

:deep(.v-checkbox .v-label) {
  color: #b0b0b0 !important;
  font-size: 0.875rem;
}

:deep(.v-checkbox--checked .v-checkbox__checkmark) {
  color: #d4af37 !important;
}

:deep(.v-checkbox--checked .v-label) {
  color: #ffffff !important;
}

/* ============================================
   ENLACES - DORADO CON HOVER ELEGANTE
   ============================================ */
.text-gold {
  color: #d4af37;
  text-decoration: none;
  transition: all 0.3s ease;
  font-weight: 500;
}

.text-gold:hover {
  color: #f0d85d;
  text-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
}

.text-decoration-none {
  text-decoration: none !important;
}

/* ============================================
   ALERTAS DE ERROR - ROJO VISIBLE
   ============================================ */
:deep(.v-alert) {
  background-color: rgba(244, 67, 54, 0.1) !important;
  border: 1px solid rgba(244, 67, 54, 0.3) !important;
}

:deep(.v-alert--type-error) {
  background-color: rgba(244, 67, 54, 0.1) !important;
  border-left: 4px solid #f44336 !important;
}

:deep(.v-alert--type-error .v-alert__content) {
  color: #ff6b6b !important;
  font-weight: 500;
}

/* ============================================
   DIVISORES - GRIS OSCURO
   ============================================ */
:deep(.v-divider) {
  border-color: rgba(212, 175, 55, 0.1) !important;
}

/* ============================================
   INFORMACIÓN DE PIE - GRIS CLARO
   ============================================ */
.auth-footer {
  color: #808080;
  font-size: 0.875rem;
}

.auth-footer a {
  color: #d4af37;
  text-decoration: none;
}

.auth-footer a:hover {
  color: #f0d85d;
}

/* ============================================
   ELEMENTOS DESHABILITADOS
   ============================================ */
:deep(.v-btn:disabled) {
  opacity: 0.5 !important;
}

/* ============================================
   RESPONSIVO - MEJOR EN MÓVIL
   ============================================ */
@media (max-width: 600px) {
  .auth-container {
    padding: 1rem;
  }

  :deep(.text-h5) {
    font-size: 1.5rem !important;
  }

  .border-gold {
    padding: 1.5rem !important;
  }
}
</style>
