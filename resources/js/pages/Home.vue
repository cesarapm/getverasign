<template>
  <div class="landing-home">
    <!-- Hero Section -->
    <section class="hero-section">
      <v-container class="hero-content py-16" fluid>
        <v-row no-gutters align="center" justify="center" class="fill-height">
          <v-col cols="12" lg="8" class="text-center px-4">
            <div class="hero-wrapper">
              <!-- Pre-heading -->
              <div class="hero-kicker mb-4">
                <v-chip label size="small" color="#d4af37" variant="outlined" class="gold-chip">
                  ✨ Plataforma de Firmas Digitales
                </v-chip>
              </div>

              <!-- Main Title -->
              <h1 class="hero-title mb-6">
                Firma documentos en minutos<br>desde cualquier lugar
              </h1>

              <!-- Subtitle -->
              <p class="hero-subtitle mb-12">
                Envía, firma y administra documentos digitales de forma rápida,<br>
                segura y sencilla. Sin complicaciones, sin papelería.
              </p>

              <!-- CTA Buttons -->
              <div class="hero-actions mb-8">
                <v-btn
                  color="primary"
                  size="large"
                  class="px-12 py-3 font-weight-bold"
                  @click="handleBeginNow"
                  elevation="4"
                >
                  <v-icon start>mdi-rocket-launch</v-icon>
                  Comenzar Ahora
                </v-btn>
                <!-- <v-btn
                  color="secondary"
                  variant="outlined"
                  size="large"
                  class="px-12 py-3 ml-4 font-weight-bold demo-btn"
                  @click="showDemo"
                >
                  <v-icon start>mdi-play-circle-outline</v-icon>
                  Ver Demo
                </v-btn> -->
              </div>

              <!-- Trust Text -->
              <div class="hero-trust">
                <small class="text-medium-emphasis">
                  Más de 10,000 documentos procesados • Miles de usuarios activos • 99.9% disponibilidad
                </small>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section py-20">
      <v-container>
        <v-row justify="center" class="mb-16">
          <v-col cols="12" md="8" class="text-center">
            <h2 class="section-title mb-4">¿Por qué elegir nuestra plataforma?</h2>
            <p class="section-subtitle">
              Soluciones completas para la firma digital de documentos con las características más avanzadas
            </p>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" sm="6" lg="4" v-for="benefit in benefits" :key="benefit.id" class="mb-8">
            <BenefitCard
              :icon="benefit.icon"
              :title="benefit.title"
              :description="benefit.description"
              :icon-color="benefit.color"
            />
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works-section py-20">
      <v-container>
        <v-row justify="center" class="mb-16">
          <v-col cols="12" md="8" class="text-center">
            <h2 class="section-title mb-4">¿Cómo funciona?</h2>
            <p class="section-subtitle">
              Un proceso simple y rápido en 4 pasos
            </p>
          </v-col>
        </v-row>

        <v-row justify="center">
          <v-col cols="12" lg="8">
            <div class="steps-container">
              <HowItWorksStep
                v-for="(step, idx) in howItWorks"
                :key="step.id"
                :step="step.id"
                :title="step.title"
                :description="step.description"
                :show-connector="idx < howItWorks.length - 1"
              />
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Features Section -->
    <section class="features-section py-20">
      <v-container>
        <v-row justify="center" class="mb-16">
          <v-col cols="12" md="8" class="text-center">
            <h2 class="section-title mb-4">Características Principales</h2>
            <p class="section-subtitle">
              Todo lo que necesitas para digitalizar tus procesos
            </p>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" md="6" v-for="feature in features" :key="feature.id">
            <FeatureCard
              :icon="feature.icon"
              :title="feature.title"
              :description="feature.description"
            />
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-20">
      <v-container>
        <v-row class="mb-12">
          <v-col cols="12" md="6" lg="3" v-for="stat in stats" :key="stat.id" class="mb-6">
            <div class="stat-item text-center">
              <div class="stat-value text-h4 font-weight-bold mb-2">{{ stat.value }}</div>
              <p class="text-body-2">{{ stat.label }}</p>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section py-20">
      <v-container>
        <v-row justify="center" class="mb-16">
          <v-col cols="12" md="8" class="text-center">
            <h2 class="section-title mb-4">Lo que dicen nuestros usuarios</h2>
            <p class="section-subtitle">
              Empresas y profesionales confían en nuestra plataforma
            </p>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" sm="6" lg="4" v-for="testimonial in testimonials" :key="testimonial.id" class="mb-8">
            <TestimonialCard
              :text="testimonial.text"
              :author="testimonial.author"
              :company="testimonial.company"
              :rating="testimonial.rating"
              :avatar-color="testimonial.avatarColor"
            />
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-20">
      <v-container>
        <v-row justify="center" class="mb-16">
          <v-col cols="12" md="8" class="text-center">
            <h2 class="section-title mb-4">Preguntas Frecuentes</h2>
            <p class="section-subtitle">
              Resuelve tus dudas sobre nuestra plataforma
            </p>
          </v-col>
        </v-row>

        <v-row justify="center">
          <v-col cols="12" lg="8">
            <v-expansion-panels>
              <FAQItem
                v-for="faq in faqs"
                :key="faq.id"
                :question="faq.question"
                :answer="faq.answer"
              />
            </v-expansion-panels>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Final CTA Section -->
    <section class="final-cta-section py-20">
      <v-container>
        <v-row justify="center">
          <v-col cols="12" md="8" class="text-center">
            <h2 class="text-h4 font-weight-bold mb-4">
              Comienza a firmar documentos hoy mismo
            </h2>
            <p class="text-body-1 mb-8">
              Digitaliza tus procesos y ahorra tiempo con una plataforma moderna y eficiente.
            </p>
            <v-btn
              color="white"
              size="large"
              class="px-12 py-3 font-weight-bold text-primary"
              @click="handleCreateAccount"
              elevation="4"
            >
              <v-icon start>mdi-account-plus</v-icon>
              Crear Cuenta
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Demo Dialog -->
    <v-dialog v-model="demoDialog" max-width="600">
      <v-card>
        <v-card-title class="d-flex align-center">
          Demo de la Plataforma
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" variant="text" @click="demoDialog = false"></v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-6">
          <div class="video-placeholder" style="background: #f0f0f0; border-radius: 8px; padding: 2rem; text-align: center">
            <v-icon size="80" class="text-medium-emphasis mb-4">mdi-video-outline</v-icon>
            <p class="text-body-2 text-medium-emphasis">
              Video de demostración de la plataforma
            </p>
            <p class="text-caption text-medium-emphasis mt-4">
              Aprende cómo funciona nuestra plataforma en 2 minutos
            </p>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import BenefitCard from '../components/BenefitCard.vue';
import HowItWorksStep from '../components/HowItWorksStep.vue';
import FeatureCard from '../components/FeatureCard.vue';
import TestimonialCard from '../components/TestimonialCard.vue';
import FAQItem from '../components/FAQItem.vue';

const router = useRouter();
const demoDialog = ref(false);

// Benefits Data
const benefits = ref([
  {
    id: 1,
    icon: 'mdi-pen',
    title: 'Firma Digital Simplificada',
    description: 'Firma documentos desde cualquier dispositivo en segundos sin necesidad de certificados complejos.',
    color: '#d4af37'
  },
  {
    id: 2,
    icon: 'mdi-rocket-launch-outline',
    title: 'Envío de Documentos Ilimitado',
    description: 'Envía todos los documentos que necesites sin limitaciones de cantidad ni tamaño.',
    color: '#d4af37'
  },
  {
    id: 3,
    icon: 'mdi-eye-check-outline',
    title: 'Seguimiento en Tiempo Real',
    description: 'Recibe notificaciones instantáneas del estado de cada documento y firma.',
    color: '#d4af37'
  },
  {
    id: 4,
    icon: 'mdi-account-tie-outline',
    title: 'Gestión de Múltiples Firmantes',
    description: 'Agrega hasta 100 firmantes por documento con roles y permisos personalizados.',
    color: '#d4af37'
  },
  {
    id: 5,
    icon: 'mdi-lock-check-outline',
    title: 'Seguridad Empresarial',
    description: 'Encriptación de grado militar y cumplimiento normativo para proteger tus datos.',
    color: '#d4af37'
  },
  {
    id: 6,
    icon: 'mdi-cloud-sync-outline',
    title: 'Acceso Desde Cualquier Dispositivo',
    description: 'Firma desde computadora, tablet o móvil con la misma experiencia fluida.',
    color: '#d4af37'
  }
]);

// How It Works Data
const howItWorks = ref([
  {
    id: 1,
    title: 'Sube tu Documento',
    description: 'Carga archivos PDF directamente desde tu computadora o arrastra y suelta. Soportamos documentos de hasta 25 MB.'
  },
  {
    id: 2,
    title: 'Agrega los Firmantes',
    description: 'Añade los correos de las personas que deben firmar, define el orden y establece plazos.'
  },
  {
    id: 3,
    title: 'Envía la Solicitud',
    description: 'Con un clic, tus firmantes recibirán un enlace para firmar de forma segura.'
  },
  {
    id: 4,
    title: 'Recibe el Documento Firmado',
    description: 'Una vez completadas todas las firmas, recibe el documento final con registro de auditoría completo.'
  }
]);

// Features Data
const features = ref([
  {
    id: 1,
    icon: 'mdi-devices',
    title: 'Firma desde Computadora o Móvil',
    description: 'Interfaz responsiva que se adapta perfectamente a cualquier pantalla y dispositivo.'
  },
  {
    id: 2,
    icon: 'mdi-bell-outline',
    title: 'Notificaciones Automáticas',
    description: 'Recibe alertas por email, SMS o push cuando hay nuevos documentos o firmas pendientes.'
  },
  {
    id: 3,
    icon: 'mdi-history',
    title: 'Historial de Actividades',
    description: 'Registro completo y auditable de todas las acciones y movimientos de tus documentos.'
  },
  {
    id: 4,
    icon: 'mdi-account-convert',
    title: 'Gestión de Usuarios',
    description: 'Administra equipos, permisos y roles para un control total de tu organización.'
  },
  {
    id: 5,
    icon: 'mdi-view-dashboard-outline',
    title: 'Panel Administrativo',
    description: 'Dashboards intuitivos con análisis y reportes en tiempo real de tu actividad.'
  },
  {
    id: 6,
    icon: 'mdi-folder-multiple-outline',
    title: 'Organización por Carpetas',
    description: 'Organiza tus documentos en carpetas y proyectos para mantener todo ordenado.'
  }
]);

// Stats Data
const stats = ref([
  {
    id: 1,
    value: '10,000+',
    label: 'Documentos Procesados'
  },
  {
    id: 2,
    value: '99.9%',
    label: 'Disponibilidad'
  },
  {
    id: 3,
    value: '5,000+',
    label: 'Usuarios Activos'
  },
  {
    id: 4,
    value: '< 5 min',
    label: 'Tiempo Promedio de Firma'
  }
]);

// Testimonials Data
const testimonials = ref([
  {
    id: 1,
    text: 'La plataforma ha transformado completamente nuestro proceso de firma. Ahora podemos cerrar contratos en días en lugar de semanas.',
    author: 'María García',
    company: 'García & Asociados',
    rating: 5,
    avatarColor: '#d4af37'
  },
  {
    id: 2,
    text: 'Increíblemente fácil de usar. Nuestros clientes lo adoraron. El soporte es responsive y siempre dispuesto a ayudar.',
    author: 'Carlos López',
    company: 'López Consulting',
    rating: 5,
    avatarColor: '#d4af37'
  },
  {
    id: 3,
    text: 'Implementamos la solución en una semana. La seguridad es de nivel empresarial. Es la herramienta que estábamos buscando.',
    author: 'Ana Rodríguez',
    company: 'TechStartup México',
    rating: 5,
    avatarColor: '#d4af37'
  }
]);

// FAQ Data
const faqs = ref([
  {
    id: 1,
    question: '¿Es legal la firma electrónica?',
    answer: 'Sí, la firma electrónica es completamente legal en la mayoría de países. Cumplimos con regulaciones como la UECA (Unión Europea), CFAA (Estados Unidos) y leyes equivalentes en otros países. Proporcionamos registros de auditoría completos como evidencia legal.'
  },
  {
    id: 2,
    question: '¿Puedo firmar desde mi celular?',
    answer: 'Por supuesto. Nuestra plataforma es completamente responsive y funciona perfectamente en dispositivos móviles. Puedes firmar desde tu smartphone o tablet en cualquier momento y lugar.'
  },
  {
    id: 3,
    question: '¿Cuántos firmantes puedo agregar?',
    answer: 'Puedes agregar hasta 100 firmantes por documento. Puedes definir el orden de firma secuencial o paralelo según necesites. Cada firmante puede tener roles y permisos diferentes.'
  },
  {
    id: 4,
    question: '¿Mis documentos están seguros?',
    answer: 'Utilizamos encriptación AES-256 para todos los datos en tránsito y en reposo. Nuestros servidores están certificados ISO 27001 y cumplimos con GDPR. Realizamos auditorías de seguridad regularmente.'
  },
  {
    id: 5,
    question: '¿Cuál es el tiempo de vencimiento de los documentos?',
    answer: 'Por defecto, los documentos vencen en 30 días. Puedes personalizar este tiempo entre 1 y 365 días. Los firmantes recibirán recordatorios automáticos antes del vencimiento.'
  },
  {
    id: 6,
    question: '¿Puedo integrar esto con mis sistemas?',
    answer: 'Sí, ofrecemos APIs REST completas para integración con tus sistemas. También contamos con plugins para las plataformas más populares y documentación completa.'
  }
]);

// Methods
const handleBeginNow = () => {
  router.push({ name: 'Dashboard' });
};

const handleCreateAccount = () => {
  router.push({ name: 'Dashboard' });
};

const showDemo = () => {
  demoDialog.value = true;
};
</script>

<style scoped>
.landing-home {
  background: #0a0a0a;
  overflow-x: hidden;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background:
    radial-gradient(circle at 20% 50%, rgba(212, 175, 55, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(212, 175, 55, 0.06) 0%, transparent 50%);
  pointer-events: none;
}

.hero-section::after {
  content: '';
  position: absolute;
  width: 400px;
  height: 400px;
  top: -100px;
  right: -100px;
  background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
  border-radius: 50%;
  animation: float 6s ease-in-out infinite;
}

.hero-content {
  position: relative;
  z-index: 1;
}

.hero-wrapper {
  max-width: 800px;
  margin: 0 auto;
  animation: slideInUp 0.8s ease-out;
}

.hero-kicker {
  margin-bottom: 1.5rem;
  animation: fadeInDown 0.6s ease-out;
}

.text-medium-emphasis{
color: rgba(255, 255, 255, 0.7)!important;
}

.hero-kicker :deep(.v-chip) {
  background: rgba(212, 175, 55, 0.2) !important;
  color: #d4af37 !important;
  border: 1px solid rgba(212, 175, 55, 0.4) !important;
  font-weight: 600;
  letter-spacing: 1px;
}

.gold-chip {
  color: #d4af37 !important;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 900;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  background: linear-gradient(135deg, #ffffff 0%, #d4af37 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -1px;
  animation: fadeInUp 0.8s ease-out 0.1s both;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: #b0b0b0;
  line-height: 1.6;
  margin-bottom: 2rem;
  animation: fadeInUp 0.8s ease-out 0.2s both;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 2rem;
  animation: fadeInUp 0.8s ease-out 0.3s both;
}

.hero-actions :deep(.v-btn) {
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.hero-actions :deep(.v-btn::before) {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: rgba(212, 175, 55, 0.2);
  transition: left 0.3s ease;
  z-index: -1;
}

.hero-actions :deep(.v-btn:hover::before) {
  left: 0;
}

.hero-actions :deep(.v-btn):first-child {
  background: linear-gradient(135deg, #d4af37 0%, #b8941d 100%);
  color: #000 !important;
  box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
}

.hero-actions :deep(.v-btn):first-child:hover {
  box-shadow: 0 12px 35px rgba(212, 175, 55, 0.5);
  transform: translateY(-2px);
}

.hero-actions :deep(.demo-btn) {
  border: 2px solid #d4af37 !important;
  color: #d4af37 !important;
  transition: all 0.3s ease;
}

.hero-actions :deep(.demo-btn:hover) {
  background: rgba(212, 175, 55, 0.1) !important;
  box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
}

.hero-trust {
  text-align: center;
  color: #888;
  font-size: 0.9rem;
  animation: fadeInUp 0.8s ease-out 0.4s both;
}

/* Section Styles */
.section-title {
  font-size: 2.5rem;
  font-weight: 900;
  background: linear-gradient(135deg, #ffffff 0%, #d4af37 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-subtitle {
  font-size: 1.1rem;
  color: #a0a0a0;
  line-height: 1.6;
}

/* Benefits Section */
.benefits-section {
  background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
  position: relative;
  z-index: 1;
}

.benefits-section :deep(.v-col) {
  animation: fadeInUp 0.6s ease-out backwards;
}

.benefits-section :deep(.v-col:nth-child(1)) { animation-delay: 0.1s; }
.benefits-section :deep(.v-col:nth-child(2)) { animation-delay: 0.2s; }
.benefits-section :deep(.v-col:nth-child(3)) { animation-delay: 0.3s; }
.benefits-section :deep(.v-col:nth-child(4)) { animation-delay: 0.4s; }
.benefits-section :deep(.v-col:nth-child(5)) { animation-delay: 0.5s; }
.benefits-section :deep(.v-col:nth-child(6)) { animation-delay: 0.6s; }

/* How It Works */
.how-it-works-section {
  background: #1a1a1a !important;
  position: relative;
}

.how-it-works-section::before {
  content: '';
  position: absolute;
  width: 300px;
  height: 300px;
  bottom: -100px;
  left: -100px;
  background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
  border-radius: 50%;
  animation: float 8s ease-in-out infinite;
}

.steps-container {
  padding: 2rem;
  position: relative;
  z-index: 1;
  color: white;
}

.steps-container :deep(h4),
.steps-container :deep(p) {
  color: #b0b0b0 !important;
}

/* Features Section */
.features-section {
  background: #0a0a0a;
  position: relative;
}

.features-section :deep(.v-col) {
  animation: fadeInUp 0.6s ease-out backwards;
}

.features-section :deep(.v-col:nth-child(1)) { animation-delay: 0.1s; }
.features-section :deep(.v-col:nth-child(2)) { animation-delay: 0.2s; }
.features-section :deep(.v-col:nth-child(3)) { animation-delay: 0.3s; }
.features-section :deep(.v-col:nth-child(4)) { animation-delay: 0.4s; }
.features-section :deep(.v-col:nth-child(5)) { animation-delay: 0.5s; }
.features-section :deep(.v-col:nth-child(6)) { animation-delay: 0.6s; }

.features-section :deep(h4),
.features-section :deep(p) {
  color: white !important;
}

/* Stats Section */
.stats-section {
  background: linear-gradient(135deg, #1a1a1a 0%, #2a2a1a 50%, #1a1a1a 100%) !important;
  position: relative;
  overflow: hidden;
}

.stats-section::before {
  content: '';
  position: absolute;
  width: 400px;
  height: 400px;
  top: 50%;
  right: -150px;
  background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
  border-radius: 50%;
  transform: translateY(-50%);
  animation: float 7s ease-in-out infinite;
}

.stat-item {
  padding: 1rem 1rem;
  position: relative;
  z-index: 1;
  color: white;
}

.stat-item p {
  color: #d0d0d0 !important;
}

.stat-value {
  color:  #ffeb3b;
  -webkit-background-clip: text;

  background-clip: text;
  line-height: 1;
margin-bottom: 0.5rem;
  font-weight: 900;
}

/* Testimonials Section */
.testimonials-section {
  background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
}

.testimonials-section :deep(.v-col) {
  animation: fadeInUp 0.6s ease-out backwards;
}

.testimonials-section :deep(.v-col:nth-child(1)) { animation-delay: 0.1s; }
.testimonials-section :deep(.v-col:nth-child(2)) { animation-delay: 0.2s; }
.testimonials-section :deep(.v-col:nth-child(3)) { animation-delay: 0.3s; }

.testimonials-section :deep(p),
.testimonials-section :deep(h4) {
  color: white !important;
}

/* FAQ Section */
.faq-section {
  background: #1a1a1a;
  position: relative;
}

.faq-section::after {
  content: '';
  position: absolute;
  width: 300px;
  height: 300px;
  top: -100px;
  right: -100px;
  background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
  border-radius: 50%;
  animation: float 6s ease-in-out infinite;
}

.faq-section :deep(.v-expansion-panels) {
  background: transparent !important;
  position: relative;
  z-index: 1;
}

.faq-section :deep(.v-expansion-panel) {
  background: rgba(212, 175, 55, 0.05) !important;
  border: 1px solid rgba(212, 175, 55, 0.2) !important;
  border-radius: 8px !important;
  margin-bottom: 0.8rem;
  transition: all 0.3s ease;
}

.faq-section :deep(.v-expansion-panel:hover) {
  background: rgba(212, 175, 55, 0.1) !important;
  border-color: rgba(212, 175, 55, 0.4) !important;
  box-shadow: 0 4px 15px rgba(212, 175, 55, 0.15);
}

.faq-section :deep(.v-expansion-panel__header) {
  color: white !important;
}

.faq-section :deep(.v-expansion-panel__body) {
  color: #b0b0b0 !important;
}

/* Final CTA Section */
.final-cta-section {
  background: linear-gradient(135deg, #1a1a1a 0%, #2a2a1a 50%, #1a1a1a 100%) !important;
  position: relative;
  overflow: hidden;
}

.final-cta-section::before {
  content: '';
  position: absolute;
  width: 500px;
  height: 500px;
  top: -200px;
  right: -200px;
  background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
  border-radius: 50%;
  animation: float 8s ease-in-out infinite;
}

.final-cta-section :deep(.v-btn) {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #d4af37 0%, #b8941d 100%) !important;
  color: #000 !important;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
  transition: all 0.3s ease;
}

.final-cta-section :deep(.v-btn:hover) {
  box-shadow: 0 12px 35px rgba(212, 175, 55, 0.5);
  transform: translateY(-3px);
}

.final-cta-section h2,
.final-cta-section p {
  color: white !important;
}

.final-cta-section :deep(.text-h4) {
  color: #ffffff !important;
}

.final-cta-section :deep(.text-body-1) {
  color: #b0b0b0 !important;
}

/* Animations */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

/* Responsive */
@media (max-width: 960px) {
  .hero-title {
    font-size: 2.5rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .hero-actions {
    flex-direction: column;
    align-items: center;
  }

  .hero-actions :deep(.v-btn) {
    width: 100%;
    max-width: 300px;
  }

  .hero-actions :deep(.v-btn:last-child) {
    margin-left: 0 !important;
  }
}

@media (max-width: 600px) {
  .hero-section {
    min-height: auto;
    padding-top: 4rem;
    padding-bottom: 4rem;
  }

  .hero-title {
    font-size: 1.75rem;
  }

  .hero-subtitle {
    font-size: 1rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .section-subtitle {
    font-size: 0.95rem;
  }

  .stats-section :deep(.v-col) {
    padding: 1rem 0.5rem;
  }
}
</style>
