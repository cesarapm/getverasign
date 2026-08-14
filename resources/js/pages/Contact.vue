<template>
  <div class="contact-page">
    <section class="contact-hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="9" class="text-center">
            <span class="contact-kicker">Contacto</span>
            <h1>¿Necesitas ayuda con tus equipos?</h1>
            <p>
              En Serviram somos especialistas en soluciones de cocina profesional.
              Contáctanos para ventas, servicio técnico o consultoría.
            </p>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section class="contact-content">
      <v-container class="py-14">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="contact-grid">
              <!-- Contacto Directo -->
              <article class="contact-card contact-card--primary">
                <span class="mini-kicker">Contacto Directo</span>
                <h2>Estamos aquí para ti</h2>
                <p>
                  Nuestro equipo está listo para ayudarte con cualquier pregunta o solicitud.
                  Elige tu forma favorita de comunicarte.
                </p>

                <div class="contact-list">
                  <a class="contact-link" href="tel:4444939485">
                    <span class="contact-icon">☎</span>
                    <div>
                      <strong>Teléfono</strong>
                      <span>444 493 9485</span>
                    </div>
                  </a>

                  <a class="contact-link" href="https://wa.me/5214445449888?text=Hola,%20estoy%20interesado%20en%20sus%20servicios" target="_blank" rel="noreferrer">
                    <span class="contact-icon">WA</span>
                    <div>
                      <strong>WhatsApp</strong>
                      <span>+52 (1) 444 544 9888</span>
                    </div>
                  </a>

                  <a class="contact-link" href="mailto:ventas@serviram.com.mx">
                    <span class="contact-icon">@</span>
                    <div>
                      <strong>Email Ventas</strong>
                      <span>ventas@serviram.com.mx</span>
                    </div>
                  </a>

                  <a class="contact-link" href="mailto:serviciotecnico@serviram.com.mx">
                    <span class="contact-icon">🔧</span>
                    <div>
                      <strong>Servicio Técnico</strong>
                      <span>serviciotecnico@serviram.com.mx</span>
                    </div>
                  </a>
                </div>
              </article>

              <!-- Ubicación y Horarios -->
              <article class="contact-card">
                <span class="mini-kicker">Ubicación</span>
                <h2>Visítanos</h2>
                
                <div class="location-section">
                  <strong>📍 Dirección</strong>
                  <p>
                    Av. Salvador Nava Martínez 1016<br>
                    Col. San Luis Rey, CP 78350<br>
                    San Luis Potosí, SLP
                  </p>
                </div>

                <div class="schedule-section">
                  <strong>🕐 Horarios</strong>
                  <div class="schedule-list">
                    <div v-for="schedule in schedules" :key="schedule.day" class="schedule-item">
                      <span class="day">{{ schedule.day }}</span>
                      <span class="time">{{ schedule.time }}</span>
                    </div>
                  </div>
                </div>
              </article>
            </div>

            <!-- Redes Sociales -->
            <article class="contact-card contact-card--socials">
              <span class="mini-kicker">Síguenos</span>
              <h2>Estamos en redes</h2>
              <p>Mantente actualizado con nuestras soluciones y promociones</p>

              <div class="social-links">
                <a 
                  v-for="social in socialMedia" 
                  :key="social.name"
                  :href="social.link"
                  target="_blank"
                  rel="noreferrer"
                  class="social-link"
                  :title="social.name"
                >
                  <v-icon v-if="social.icon" :icon="social.icon"></v-icon>
                  <span v-if="social.customSvg">🎵</span>
                  <span class="social-label">{{ social.name }}</span>
                </a>
              </div>
            </article>
          </v-col>
        </v-row>
      </v-container>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Schedule information
const schedules = ref([
  { day: 'Lunes - Viernes', time: '8:00 AM - 6:00 PM' },
  { day: 'Sábado', time: '9:00 AM - 2:00 PM' },
  { day: 'Domingo', time: 'Cerrado' }
]);

// Social media links
const socialMedia = ref([
  {
    name: 'Facebook',
    icon: 'mdi-facebook',
    link: 'https://www.facebook.com/p/Serviram-soluciones-100067535923102/?paipv=0&eav=AfagGM_HfQd22lhPOuKn_gb515ZNqYfe66vywDDzK6YQbbatZ2gtFJNhL_pJLRNiTbM&_rdr',
    color: '#1877F2'
  },
  {
    name: 'Instagram',
    icon: 'mdi-instagram',
    link: 'https://www.instagram.com/serviram.com.mx?igsh=eWUwemNybWc4YnV4&utm_source=qr',
    color: '#E4405F'
  },
  {
    name: 'TikTok',
    icon: '',
    customSvg: true,
    link: 'https://www.tiktok.com/@serviram_kitchen_service',
    color: '#000000'
  }
]);
</script>

<style scoped>
.contact-page {
  margin-top: 70px;
  background: linear-gradient(180deg, #fbf8f4 0%, #f1ebe4 100%);
  color: #5f5244;
}

.contact-hero {
  background:
    radial-gradient(circle at top right, rgba(217, 200, 181, 0.42), transparent 30%),
    linear-gradient(180deg, #f8f4ef 0%, #f3ece5 100%);
}

.contact-kicker,
.mini-kicker {
  display: inline-block;
  padding: 0.45rem 1rem;
  border-radius: 999px;
  background: rgba(184, 151, 120, 0.12);
  color: #8c745f;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.78rem;
}

.contact-hero h1,
.contact-card h2 {
  color: #6b5b47;
  margin: 1rem 0;
}

.contact-hero h1 {
  font-size: clamp(2.5rem, 6vw, 4.4rem);
}

.contact-hero .text-center {
  max-width: 760px;
  margin-inline: auto;
}

.contact-hero p,
.contact-card p,
.expectation-item p,
.contact-link span {
  color: #7f6d5a;
  line-height: 1.9;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.contact-card {
  background: rgba(255, 252, 248, 0.88);
  border: 1px solid rgba(184, 151, 120, 0.16);
  border-radius: 28px;
  padding: 2rem;
  box-shadow: 0 18px 40px rgba(140, 116, 95, 0.08);
}

.contact-card--primary {
  background: linear-gradient(180deg, rgba(255, 252, 248, 0.96), rgba(245, 239, 233, 0.94));
}

.contact-list {
  display: grid;
  gap: 1rem;
  margin-top: 1.5rem;
}

.contact-link {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  padding: 1rem 1.1rem;
  border-radius: 20px;
  background: rgba(245, 239, 233, 0.74);
  text-decoration: none;
  transition: all 0.3s ease;
}

.contact-link:hover {
  background: rgba(220, 200, 181, 0.85);
  transform: translateY(-2px);
}

.contact-link > div {
  min-width: 0;
}

.contact-link strong {
  display: block;
  color: #6b5b47;
  margin-bottom: 0.35rem;
}

.contact-link span {
  overflow-wrap: anywhere;
  word-break: break-word;
}

.contact-icon {
  width: 42px;
  height: 42px;
  flex: 0 0 42px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #d9c8b5 0%, #b89778 100%);
  color: #fffaf4;
  font-size: 0.8rem;
  letter-spacing: 0.08em;
}

.location-section,
.schedule-section {
  margin-top: 1.5rem;
  padding: 1.2rem;
  border-radius: 20px;
  background: rgba(245, 239, 233, 0.74);
}

.schedule-section {
  margin-top: 1rem;
}

.location-section strong,
.schedule-section strong {
  display: block;
  color: #6b5b47;
  margin-bottom: 0.6rem;
}

.location-section p {
  margin: 0;
  overflow-wrap: anywhere;
  word-break: break-word;
}

.schedule-list {
  display: grid;
  gap: 0.6rem;
}

.schedule-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.6rem 0.8rem;
  border-radius: 12px;
  background: rgba(255, 251, 246, 0.88);
}

.schedule-item .day {
  color: #6b5b47;
  font-weight: 600;
}

.schedule-item .time {
  color: #8c745f;
  font-size: 0.9rem;
}

.contact-card--socials {
  margin-top: 1.5rem;
}

.social-links {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 1rem;
  margin-top: 1.5rem;
}

.social-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  padding: 1.3rem;
  border-radius: 20px;
  background: rgba(245, 239, 233, 0.74);
  text-decoration: none;
  color: #6b5b47;
  transition: all 0.3s ease;
  font-weight: 600;
  font-size: 0.9rem;
}

.social-link:hover {
  background: rgba(220, 200, 181, 0.85);
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(140, 116, 95, 0.15);
}

.social-link .v-icon {
  font-size: 1.8rem;
}

.social-link span {
  font-size: 1.8rem;
}

@media (max-width: 960px) {
  .contact-grid {
    grid-template-columns: 1fr;
  }

  .social-links {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 600px) {
  .contact-page {
    margin-top: 84px;
  }

  .contact-hero {
    padding-top: 0.5rem;
  }

  .contact-hero h1 {
    font-size: clamp(2rem, 11vw, 3rem);
    line-height: 1.02;
  }

  .contact-hero p {
    font-size: 0.98rem;
  }

  .contact-card {
    padding: 1.5rem;
    border-radius: 24px;
  }

  .contact-link {
    padding: 0.95rem;
  }

  .contact-link {
    align-items: center;
  }

  .social-links {
    grid-template-columns: repeat(2, 1fr);
  }

  .social-link {
    padding: 1rem;
  }
}

@media (max-width: 420px) {
  .contact-content .py-14,
  .contact-hero .py-16 {
    padding-top: 2.25rem !important;
    padding-bottom: 2.25rem !important;
  }

  .contact-card {
    padding: 1.1rem;
  }

  .contact-link {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .schedule-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.4rem;
  }

  .contact-icon {
    width: 38px;
    height: 38px;
    flex-basis: 38px;
  }

  .contact-kicker,
  .mini-kicker {
    font-size: 0.7rem;
    letter-spacing: 0.12em;
  }

  .social-links {
    grid-template-columns: 1fr;
  }
}
</style>