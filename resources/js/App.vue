<template>
  <v-app>
    <!-- Navbar -->
    <v-app-bar
      ref="navbar"
      elevation="0"
      height="auto"
      class="py-0 main-navbar"
    >
      <v-container class="navbar-container">
        <v-app-bar-title class="pa-0">
          <router-link to="/" class="brand-logo text-decoration-none">
            <div class="brand-lockup">
              <div class="brand-mark">
                <img :src="logo" alt="Logo" />
              </div>
              <!-- <div class="brand-copy">
                <span class="brand-wordmark">Serviram</span>
                <span class="brand-note">Equipos Profesionales</span>
              </div> -->
            </div>
          </router-link>
        </v-app-bar-title>

        <v-spacer></v-spacer>

        <div class="nav-links d-none d-md-flex align-center">
          <v-btn
            :to="{ name: 'Home' }"
            variant="text"
            class="nav-link"
          >
            Inicio
          </v-btn>
          <v-btn
            :to="{ name: 'Dashboard' }"
            variant="text"
            class="nav-link"
          >
            Dashboard
          </v-btn>
          <v-btn
            :to="{ name: 'DocumentList' }"
            variant="text"
            class="nav-link"
          >
            Documentos
          </v-btn>
          <v-btn
            :to="{ name: 'Signers' }"
            variant="text"
            class="nav-link"
          >
            Firmantes
          </v-btn>
          <v-btn
            :to="{ name: 'Settings' }"
            variant="text"
            class="nav-link"
          >
            Configuración
          </v-btn>
        </div>

        <!-- Mobile Menu -->
        <v-app-bar-nav-icon
          class="d-md-none mobile-nav-toggle"
          @click="drawer = !drawer"
        ></v-app-bar-nav-icon>
      </v-container>
    </v-app-bar>

    <!-- Mobile Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      temporary
      location="right"
      class="drawerapp"
    >
      <v-list>
        <v-list-item
          :to="{ name: 'Home' }"
          prepend-icon="mdi-home"
          title="Inicio"
        ></v-list-item>
        <v-list-item
          :to="{ name: 'Dashboard' }"
          prepend-icon="mdi-view-dashboard"
          title="Dashboard"
        ></v-list-item>
        <v-list-item
          :to="{ name: 'DocumentList' }"
          prepend-icon="mdi-file-document-multiple"
          title="Documentos"
        ></v-list-item>
        <v-list-item
          :to="{ name: 'Signers' }"
          prepend-icon="mdi-account-multiple"
          title="Firmantes"
        ></v-list-item>
        <v-list-item
          :to="{ name: 'Settings' }"
          prepend-icon="mdi-cog"
          title="Configuración"
        ></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Contenido principal -->
    <v-main class="main-content">
      <router-view></router-view>
    </v-main>

    <!-- Botón flotante de WhatsApp -->
    <v-tooltip text="Chatea con nosotros" location="start">
      <template v-slot:activator="{ props }">
        <v-btn
          v-bind="props"
          class="whatsapp-float"
          color="#25D366"
          size="x-large"
          icon
          elevation="8"
          href="https://wa.me/5214445449888?text=Hola,%20estoy%20interesado%20en%20sus%20servicios"
          target="_blank"
        >
          <v-icon size="32">mdi-whatsapp</v-icon>
        </v-btn>
      </template>
    </v-tooltip>

    <!-- Footer -->
    <v-footer class="footer">
      <v-container>
        <v-row class="py-8">
          <v-col cols="12" md="4" class="mb-6 mb-md-0">
            <div class="footer-brand mb-4">
              <div class="footer-brand-block">
                <div class="footer-mark">
                  <img :src="logo" alt="VeraSign Logo" style="height: 70px; width: auto;" />
                </div>
              </div>
            </div>
            <p class="footer-desc">
              Plataforma profesional de firma electrónica. Seguridad, legalidad y eficiencia en la gestión documental digital.
            </p>
            <div class="social-links mt-4">
              <v-btn icon variant="text" class="footer-social" size="small" href="https://www.instagram.com" target="_blank">
                <v-icon>mdi-instagram</v-icon>
              </v-btn>
              <v-btn icon variant="text" class="footer-social" size="small" href="https://www.facebook.com" target="_blank">
                <v-icon>mdi-facebook</v-icon>
              </v-btn>
              <v-btn icon variant="text" class="footer-social" size="small" href="https://www.linkedin.com" target="_blank">
                <v-icon>mdi-linkedin</v-icon>
              </v-btn>
            </div>
          </v-col>
          <v-col cols="12" md="4">
            <h4 class="footer-title mb-4">Plataforma</h4>
            <div class="footer-links">
              <router-link to="/" class="footer-link">Inicio</router-link>
              <router-link :to="{ name: 'Dashboard' }" class="footer-link">Dashboard</router-link>
              <a href="#" class="footer-link">Precios</a>
              <a href="#" class="footer-link">Seguridad</a>
            </div>
          </v-col>
          <!-- <v-col cols="12" md="3">
            <h4 class="footer-title mb-4">Información</h4>
            <div class="footer-links">
              <a href="#" class="footer-link">Sobre Nosotros</a>
              <a href="#" class="footer-link">Envíos y Devoluciones</a>
              <a href="#" class="footer-link">Términos y Condiciones</a>
              <a href="#" class="footer-link">Política de Privacidad</a>
            </div>
          </v-col> -->
          <v-col cols="12" md="4">
            <h4 class="footer-title mb-4">Soporte</h4>
            <div class="footer-contact">
              <div class="contact-item">
                <v-icon size="small" class="contact-icon">mdi-whatsapp</v-icon>
                <span>+52 (1) 444 544 9888</span>
              </div>
              <div class="contact-item">
                <v-icon size="small" class="contact-icon">mdi-email</v-icon>
                <span>soporte@getverasign.mx</span>
              </div>
              <div class="contact-item">
                <v-icon size="small" class="contact-icon">mdi-map-marker</v-icon>
                <span>México</span>
              </div>
            </div>
          </v-col>
        </v-row>
        <v-divider class="footer-divider"></v-divider>
        <div class="text-center py-6">
          <p class="footer-copy text-body-2">
            © {{ new Date().getFullYear() }} GetVeraSign. Todos los derechos reservados.
          </p>
        </div>
      </v-container>
    </v-footer>
  </v-app>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useCart } from './composables/useCart';

const { itemCount } = useCart();
const drawer = ref(false);
const navbar = ref(null);
let lastScrollTop = 0;
let ticking = false;
const scrollThreshold = 5;
const logo = '/images/logo.webp';


// Scroll behavior with requestAnimationFrame for smooth performance
const handleScroll = () => {
  if (!navbar.value) return;

  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const scrollDifference = Math.abs(scrollTop - lastScrollTop);

  // Only process if we've scrolled enough
  if (scrollDifference < scrollThreshold) {
    ticking = false;
    return;
  }

  // Don't hide navbar if drawer is open
  if (drawer.value) {
    navbar.value.$el.classList.remove('hidden');
    ticking = false;
    return;
  }

  // Hide/show navbar based on scroll direction
  if (scrollTop > lastScrollTop && scrollTop > 150) {
    // Scrolling down & past threshold -> hide navbar
    navbar.value.$el.classList.add('hidden');
  } else if (scrollTop < lastScrollTop || scrollTop <= 100) {
    // Scrolling up OR at top -> show navbar
    navbar.value.$el.classList.remove('hidden');
  }

  lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  ticking = false;
};

const onScroll = () => {
  if (!ticking) {
    requestAnimationFrame(handleScroll);
    ticking = true;
  }
};

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll);
});
</script>

<style>
/* Brand Logo */


html,
html {
  scroll-behavior: auto !important;
}

body,
* {
  font-family: var(--font-brand), serif;
  margin: 0;
  padding: 0;
  scroll-behavior: auto !important;
}

header {
  font-family: var(--font-brand), serif;
  margin: 0;
  padding: 0;
  font-size: 16px;
}

/* WhatsApp Float Button */
.whatsapp-float {
  position: fixed !important;
  bottom: 20px;
  right: 20px;
  z-index: 1000;
  box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4) !important;
  transition: all 0.3s ease !important;
}

.whatsapp-float:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 30px rgba(37, 211, 102, 0.6) !important;
}

/* Tooltip moderno */
:deep(.v-tooltip__content) {
  background: linear-gradient(135deg, #25D366 0%, #20ba5a 100%) !important;
  color: white !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  letter-spacing: 0.5px !important;
  padding: 10px 16px !important;
  border-radius: 8px !important;
  box-shadow: 0 8px 24px rgba(37, 211, 102, 0.35) !important;
  backdrop-filter: blur(8px) !important;
}

@media (max-width: 600px) {
  .whatsapp-float {
    bottom: 80px;
    right: 16px;
  }
}

.brand-logo {
  display: flex;
  align-items: center;
  color: #d4af37;
  transition: all 0.3s ease;
}

.brand-logo:hover {
  opacity: 0.8;
}

/* Main Content Area */
.main-content {
  background: #0a0a0a !important;
}

:deep(.v-main__wrap) {
  background: #0a0a0a !important;
}

/* Navbar hide animation */
.main-navbar {
  background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%) !important;
  border-bottom: 1px solid rgba(212, 175, 55, 0.2);
  backdrop-filter: blur(18px);
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease-in-out !important;
  will-change: transform;
}

.main-navbar :deep(.v-app-bar-title) {
  color: #d4af37;
}

.navbar-container {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
}

.main-navbar.hidden {
  transform: translateY(-100%) !important;
}

.drawerapp {
  background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%) !important;
  font-family: var(--font-brand), serif;
  padding-top: 100px;
}

.drawerapp :deep(.v-list-item) {
  color: #b0b0b0 !important;
}

.drawerapp :deep(.v-list-item:hover) {
  background: rgba(212, 175, 55, 0.1) !important;
  color: #d4af37 !important;
}

.drawerapp :deep(.v-icon) {
  color: #d4af37 !important;
}

.brand-lockup {
  display: flex;
  align-items: center;
  gap: 14px;
}

.brand-mark,
.footer-mark {
  width: 100%;
  height: 86px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 10%;
  margin: auto;

  font-size: 1rem;
  letter-spacing: 0.12em;
 
  object-fit: contain;
  overflow: hidden;
}

.brand-mark img,
.footer-mark img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.brand-copy,
.footer-brand-copy {
  display: flex;
  flex-direction: column;
  padding-top: 10px;
}

.brand-wordmark {
  color: #d4af37;
  font-size: 1.45rem;
  letter-spacing: 0.06em;
  line-height: 1;
}

.brand-note {
  color: #888;
  font-size: 0.72rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  margin-top: 0.3rem;
}

@media (min-width: 600px) and (max-width: 959px) {
  .drawerapp {
    padding-top: 70px;
  }
}

@media (min-width: 960px) {
  .drawerapp {
    padding-top: 80px;
  }
}
@media (max-width: 600px) {
  .brand-lockup {
    gap: 8px;
  }

  .brand-mark,
  .footer-mark {
    width: 100%;
    height: 52px;
    min-width: 42px;
    min-height: 42px;
    flex-shrink: 0;
  }

  .brand-wordmark {
    font-size: 1.05rem;
  }

  .brand-note {
    font-size: 0.58rem;
  }

  .v-toolbar__content {
    padding: 8px 16px !important;
  }

  .navbar-container {
    flex-wrap: nowrap !important;
  }

  .v-app-bar-title {
    flex: 0 1 auto !important;
    max-width: calc(100% - 60px) !important;
    overflow: hidden;
  }

  .brand-logo {
    display: block;
    width: 100%;
  }

  .nav-links {
    display: none !important;
  }
}

.brand-icon {
  font-size: 32px;
  line-height: 1;
}

.brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1;
}

.brand-name {
  color: white;
  font-size: 18px;
  font-weight: 900;
  letter-spacing: 2px;
}

.brand-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 3px;
}

.nav-link {
  color: #b0b0b0 !important;
  font-weight: 600;
  letter-spacing: 0.08em;
  margin: 0 4px;
  border-radius: 999px !important;
  padding-inline: 1rem !important;
  transition: all 0.3s ease;
}

.nav-link:hover {
  background: rgba(212, 175, 55, 0.2) !important;
  color: #d4af37 !important;
}

.nav-cart,
.mobile-nav-toggle {
  color: #b0b0b0 !important;
  transition: all 0.3s ease;
}

.nav-cart:hover,
.mobile-nav-toggle:hover {
  color: #d4af37 !important;
}

/* Footer */
.footer {
  color: #b0b0b0;
  background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%) !important;
  border-top: 1px solid rgba(212, 175, 55, 0.2);
}

.footer-brand {
  display: flex;
  align-items: center;
}

.footer-brand-block {
  display: flex;
  align-items: center;
  gap: 14px;
}

.brand-icon-footer {
  font-size: 40px;
}

.footer-brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.footer-brand-name {
  font-size: 20px;
  font-weight: 900;
  letter-spacing: 2px;
  color: #d4af37;
}

.footer-brand-subtitle {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 3px;
  color: #888;
}

.footer-desc {
  font-size: 14px;
  line-height: 1.6;
  max-width: 300px;
  color: #888;
}

.footer-title {
  color: #d4af37;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.footer-links {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-link {
  color: #888;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: #d4af37;
}

.footer-contact {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #888;
  font-size: 14px;
}

.contact-icon {
  color: #d4af37 !important;
}

.social-links {
  display: flex;
  gap: 8px;
}

.footer-social {
  color: #d4af37 !important;
  background: rgba(212, 175, 55, 0.15) !important;
  transition: all 0.3s ease;
}

.footer-social:hover {
  background: rgba(212, 175, 55, 0.3) !important;
  transform: translateY(-2px);
}

.footer-social svg {
  width: 18px;
  height: 18px;
  color: #d4af37;
}

.footer-divider {
  border-color: rgba(212, 175, 55, 0.2) !important;
}

.footer-copy {
  color: #888;
}
</style>
