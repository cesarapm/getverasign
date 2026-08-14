<template>
  <div class="about-page">
    <section class="about-hero">
      <div class="hero-background"></div>
      <v-container class="py-16 relative z-10">
        <v-row justify="center">
          <v-col cols="12" lg="10" class="text-center">
            <div class="hero-emblem" data-aos="fade-up" data-aos-duration="800">Sobre SERVIRAM</div>
            <h1 class="hero-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Equipos para la Excelencia Gastronómica</h1>
            <p class="hero-text" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
             SERVIRAM es tu socio confiable en soluciones de equipos de cocina profesionales. 
              Con más de 45 años de experiencia, distribuimos las mejores marcas mundiales con soporte técnico completo.
            </p>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section class="story-section">
      <v-container class="py-14">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="section-heading text-center mb-10" data-aos="fade-up" data-aos-duration="800">
              <span class="section-kicker">Quiénes Somos</span>
              <h2>Nuestra Misión</h2>
            </div>
            <div class="story-grid">
              <article class="story-card story-card--wide card-hover-lift" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <p>
                  SERVIRAM es una empresa lider en la distribución de equipos gastronómicos profesionales en México. 
                  Durante casi 50 años, hemos construido relaciones sólidas con restaurantes, hoteles, colegios y empresas catering.
                </p>
                <p>
                  Nuestro compromiso es proporcionar no solo productos de la más alta calidad, sino también 
                  asesoramiento técnico experto, soporte post-venta y garantía completa para asegurar el éxito de tu negocio.
                </p>
              </article>
              <article class="story-card card-hover-lift" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <h3>Nuestro Compromiso</h3>
                <p>
                  Ofrecemos equipos de marcas internacionales reconocidas: Rational, Unox, Winterhalter, Euroquip y más. 
                  Cada producto es seleccionado cuidadosamente para garantizar durabilidad, eficiencia y máximo rendimiento.
                </p>
              </article>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section class="purpose-section">
      <v-container class="py-14">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="section-heading text-center mb-10" data-aos="fade-up" data-aos-duration="800">
              <span class="section-kicker">Misión, Visión y Valores</span>
              <h2>Lo que nos guía</h2>
            </div>

            <div class="purpose-grid mb-8">
              <article class="purpose-card card-hover-lift" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <div class="purpose-icon">📍</div>
                <h3>Misión</h3>
                <p>
                  Ser el distribuidor líder de equipos gastronómicos en México, proporcionando 
                  soluciones innovadoras que maximicen la productividad y rentabilidad de nuestros clientes.
                </p>
              </article>

              <article class="purpose-card card-hover-lift" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <div class="purpose-icon">🎯</div>
                <h3>Visión</h3>
                <p>
                  Ser el socio preferido de la industria gastronómica mexicana, reconocido por 
                  nuestra experiencia, calidad de productos y excelencia en servicio técnico.
                </p>
              </article>
            </div>

            <div class="values-card card-hover-lift" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
              <h3>Valores</h3>
              <div class="values-grid">
                <div class="value-item value-hover" data-aos="fade-up" data-aos-duration="700" data-aos-delay="100">
                  <strong>⭐ Excelencia</strong>
                  <p>Solo distribuimos marcas de clase mundial con tecnología de punta.</p>
                </div>
                <div class="value-item value-hover" data-aos="fade-up" data-aos-duration="700" data-aos-delay="150">
                  <strong>🛡️ Confiabilidad</strong>
                  <p>Soporte técnico 24/7 y garantía completa en cada producto.</p>
                </div>
                <div class="value-item value-hover" data-aos="fade-up" data-aos-duration="700" data-aos-delay="200">
                  <strong>💼 Profesionalismo</strong>
                  <p>Equipo altamente capacitado con conocimiento profundo en equipos gastronómicos.</p>
                </div>
                <div class="value-item value-hover" data-aos="fade-up" data-aos-duration="700" data-aos-delay="250">
                  <strong>🤝 Compromiso</strong>
                  <p>Dedicados a potenciar el éxito de tu negocio gastronómico.</p>
                </div>
                <div class="value-item value-hover" data-aos="fade-up" data-aos-duration="700" data-aos-delay="300">
                  <strong>🚀 Innovación</strong>
                  <p>Constantemente actualizando nuestro catálogo con las últimas tecnologías.</p>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>
  </div>
</template>

<script>
import AOS from 'aos'
import 'aos/dist/aos.css'

export default {
  name: 'About',
  mounted() {
    AOS.init({
      duration: 800,
      easing: 'ease-out-cubic',
      once: true,
      offset: 100
    })
  }
}
</script>

<style scoped>
/* Animaciones keyframes */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

.about-page {
  margin-top: 70px;
  background: linear-gradient(135deg, #f5f7fa 0%, #eef2f7 50%, #f0f4f8 100%);
  color: #0b3066;
  position: relative;
  overflow: hidden;
}

.about-hero {
  position: relative;
  background: linear-gradient(180deg, rgba(11, 48, 102, 0.03) 0%, rgba(30, 90, 160, 0.06) 50%, rgba(79, 172, 254, 0.02) 100%);
  overflow: hidden;
  padding: 8rem 0;
  min-height: 85vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    linear-gradient(135deg, rgba(11, 48, 102, 0.65), rgba(30, 90, 160, 0.60)), 
    url('/images/portadas/portadanosotros.webp');
  background-size: contain;
  background-position: center;
  background-attachment: fixed;
  pointer-events: none;
}

.hero-emblem,
.section-kicker {
  display: inline-block;
  padding: 0.5rem 1.2rem;
  border-radius: 50px;
  background: linear-gradient(135deg, rgba(30, 90, 160, 0.2), rgba(100, 181, 246, 0.15));
  border: 1.5px solid rgba(30, 90, 160, 0.3);
  color: #ffffff;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.78rem;
  font-weight: 700;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  backdrop-filter: blur(10px);
  box-shadow: 0 8px 20px rgba(30, 90, 160, 0.15);
  text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
}

.section-heading:hover .section-kicker {
  background: linear-gradient(135deg, rgba(30, 90, 160, 0.15), rgba(100, 181, 246, 0.12));
  border-color: rgba(30, 90, 160, 0.3);
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(255, 255, 255, 0.12);
}

.hero-title,
.section-heading h2,
.story-card h3,
.founder-card h3,
.purpose-card h3,
.values-card h3 {
  color: #d6d6d6;
  letter-spacing: 0.04em;
  transition: all 0.4s ease;
  font-weight: 800;
}

.about-hero .hero-title {
  color: #ffffff !important;
  text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.6), 0 0 30px rgba(0, 0, 0, 0.5), 1px 1px 3px rgba(0, 0, 0, 0.8);
}

.about-hero .hero-emblem {
  color: #ffffff;
  text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.5);
}

.about-hero .hero-text {
  color: #ffffff;
  text-shadow: 1px 1px 8px rgba(0, 0, 0, 0.5), 0 0 15px rgba(0, 0, 0, 0.3);
  font-size: 1.1rem;
  letter-spacing: 0.5px;
}

.section-heading h2 {
  font-size: clamp(2rem, 5vw, 3.5rem);
  background: linear-gradient(135deg, #0b3066, #1e5aa0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0.8rem 0 0;
}

.section-heading:hover h2 {
  letter-spacing: 0.06em;
}

.story-card h3,
.founder-card h3,
.purpose-card h3,
.values-card h3 {
  font-size: 1.35rem;
  margin-bottom: 0.8rem;
  color: #0b3066;
}

.story-card:hover h3,
.founder-card:hover h3,
.purpose-card:hover h3,
.values-card:hover h3 {
  color: #1e5aa0;
}

.hero-title {
  font-size: clamp(2.8rem, 7vw, 4.8rem);
  margin: 1.5rem 0 1.25rem;
  color: #0b3066;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.about-hero .hero-title {
  color: #ffffff !important;
  text-shadow: 3px 3px 15px rgba(0, 0, 0, 0.7), 0 0 35px rgba(0, 0, 0, 0.6), 2px 2px 5px rgba(0, 0, 0, 0.9);
}

.hero-text,
.story-card p,
.founder-card p,
.purpose-card p,
.value-item p {
  font-size: 1.05rem;
  line-height: 1.95;
  color: #4a5568;
  transition: all 0.3s ease;
  font-weight: 500;
}

.hero-text {
  max-width: 760px;
  margin: 0 auto;
  color: #5a6a7a;
  font-size: 1.08rem;
}

.hero-quote {
  margin-top: 1.8rem;
  color: #4a5568;
  font-size: 1.2rem;
  font-style: italic;
}

.story-grid,
.purpose-grid,
.founders-grid,
.values-grid {
  display: grid;
  gap: 2rem;
}

.story-grid,
.purpose-grid {
  grid-template-columns: 1.5fr 1fr;
}

.founders-grid {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.values-grid {
  grid-template-columns: repeat(2, minmax(0, 1fr));
  margin-top: 2rem;
}

/* Card Hover Effects */
.card-hover-lift {
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
}

.card-hover-lift:hover {
  transform: translateY(-8px);
  box-shadow: 0 24px 56px rgba(30, 90, 160, 0.18);
}

.story-card,
.founder-card,
.purpose-card,
.values-card {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.7) 0%, rgba(232, 238, 246, 0.5) 100%);
  border: 1.5px solid rgba(30, 90, 160, 0.12);
  border-radius: 24px;
  padding: 2.2rem;
  box-shadow: 0 12px 36px rgba(30, 90, 160, 0.08);
  backdrop-filter: blur(12px);
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
}

.story-card::before,
.founder-card::before,
.purpose-card::before,
.values-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(100, 181, 246, 0.05) 0%, transparent 100%);
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.4s ease;
}

.card-hover-lift:hover .story-card::before,
.card-hover-lift:hover .founder-card::before,
.card-hover-lift:hover .purpose-card::before,
.card-hover-lift:hover .values-card::before {
  opacity: 1;
}

.founder-card {
  padding: 1.5rem;
}

.purpose-icon {
  font-size: 2.2rem;
  margin-bottom: 0.8rem;
  display: inline-block;
  transition: transform 0.5s ease-out;
}

.purpose-card:hover .purpose-icon {
  transform: scale(1.15);
}

.founder-media {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  margin-bottom: 1.6rem;
  min-height: 420px;
  background: linear-gradient(135deg, #e8eef6 0%, #d6e2f0 100%);
  box-shadow: 0 16px 40px rgba(30, 90, 160, 0.12);
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.founder-media:hover {
  box-shadow: 0 28px 60px rgba(30, 90, 160, 0.18);
  transform: translateY(-4px);
}

.founder-media::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(30, 90, 160, 0.02) 0%, rgba(30, 90, 160, 0.08) 100%);
  pointer-events: none;
}

.founder-photo {
  display: block;
  width: 100%;
  height: 100%;
  min-height: 420px;
  object-fit: cover;
  transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
}

.founder-media:hover .founder-photo {
  transform: scale(1.06);
}

.founder-media--romero .founder-photo {
  object-position: center 20%;
}

.founder-media--izaguirre .founder-photo {
  object-position: center 18%;
}

.founder-photo-badge {
  position: absolute;
  left: 1.2rem;
  bottom: 1.2rem;
  z-index: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 3.8rem;
  padding: 0.6rem 1rem;
  border-radius: 50px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(255, 250, 244, 0.88));
  border: 1.5px solid rgba(30, 90, 160, 0.15);
  color: #0b3066;
  letter-spacing: 0.12em;
  font-size: 0.78rem;
  font-weight: 700;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 24px rgba(30, 90, 160, 0.15);
  animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.4s backwards;
}

.founder-tag {
  display: block;
  margin-bottom: 0.8rem;
  color: #1e5aa0;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  font-size: 0.75rem;
  font-weight: 600;
}

.purpose-icon {
  font-size: 2.8rem;
  margin-bottom: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, rgba(30, 90, 160, 0.1), rgba(100, 181, 246, 0.08));
  border-radius: 20px;
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.purpose-card:hover .purpose-icon {
  transform: scale(1.2) rotate(5deg);
  background: linear-gradient(135deg, rgba(30, 90, 160, 0.2), rgba(100, 181, 246, 0.15));
}

.value-item {
  padding: 1.4rem 1.6rem;
  border-radius: 16px;
  background: linear-gradient(135deg, rgba(232, 238, 246, 0.5) 0%, rgba(242, 246, 252, 0.3) 100%);
  border: 1.5px solid rgba(30, 90, 160, 0.1);
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.value-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(100, 181, 246, 0.1), transparent);
  transition: left 0.6s ease;
}

.value-hover:hover::before {
  left: 100%;
}

.value-hover:hover {
  background: linear-gradient(135deg, rgba(30, 90, 160, 0.08), rgba(100, 181, 246, 0.06));
  border-color: rgba(30, 90, 160, 0.2);
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(30, 90, 160, 0.12);
}

.value-item strong {
  color: #1e5aa0;
  display: block;
  margin-bottom: 0.6rem;
  font-size: 1.1rem;
  transition: all 0.4s ease-out;
  position: relative;
  z-index: 1;
}

.value-hover:hover strong {
  color: #0b3066;
  transform: translateX(2px);
}

.value-item p {
  margin: 0;
  transition: all 0.4s ease-out;
  position: relative;
  z-index: 1;
}

.value-hover:hover p {
  color: #0b3066;
}

@media (max-width: 960px) {
  .story-grid,
  .purpose-grid,
  .founders-grid,
  .values-grid {
    grid-template-columns: 1fr;
  }
  
  .story-card,
  .founder-card,
  .purpose-card,
  .values-card {
    padding: 1.8rem;
  }
}

@media (max-width: 600px) {
  .about-page {
    margin-top: 84px;
  }

  .founder-media,
  .founder-photo {
    min-height: 360px;
  }

  .story-card,
  .founder-card,
  .purpose-card,
  .values-card {
    padding: 1.5rem;
  }

  .values-grid {
    grid-template-columns: 1fr;
  }

  .hero-title {
    font-size: clamp(2rem, 5vw, 3rem);
  }
  
  .section-heading h2 {
    font-size: clamp(1.6rem, 4vw, 2.4rem);
  }
  .hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    linear-gradient(135deg, rgba(11, 48, 102, 0.65), rgba(30, 90, 160, 0.60)), 
    url('/images/portadas/portadanosotros.webp');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  pointer-events: none;
}
}
</style>
