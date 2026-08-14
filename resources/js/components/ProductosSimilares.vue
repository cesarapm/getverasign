<template>
  <v-container class="similar-products-section py-20">
    <v-row justify="center">
      <v-col cols="12" md="10">
        <!-- Header Section -->
        <div class="section-header mb-12">
          <div class="section-kicker">
            <v-icon size="18" class="me-2">mdi-package-variant</v-icon>
            Productos relacionados
          </div>
          <h2 class="section-title mb-3">
            Más artículos 
            <span class="accent-text" v-if="categoriaNombre">en {{ categoriaNombre }}</span>
            <span class="accent-text" v-else-if="industriaNombre">en {{ industriaNombre }}</span>
            <span class="accent-text" v-else-if="marcaNombre">de {{ marcaNombre }}</span>
          </h2>
          <p class="section-subtitle">
            Descubre otras piezas que podrían interesarte
          </p>
        </div>

        <!-- Estado de carga -->
        <div v-if="cargando" class="text-center py-12">
          <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
          <p class="mt-4" style="color: #7f6d5a;">Buscando productos similares...</p>
        </div>

        <!-- Sin productos similares -->
        <div v-else-if="!productos || productos.length === 0" class="text-center py-12">
          <v-icon size="64" color="grey">mdi-package-variant-closed</v-icon>
          <p class="text-h6 mt-4" style="color: #5d4a38;">No hay productos similares</p>
          <p class="text-body-2" style="color: #8f7a65;">No se encontraron otros artículos en esta categoría</p>
        </div>

        <!-- Carrusel de productos -->
        <div v-else class="carousel-wrapper">
          <!-- Botón anterior -->
          <v-btn
            v-if="productos.length > itemsPerPage"
            icon
            class="carousel-btn carousel-btn--prev"
            @click="previousSlide"
            :disabled="currentSlide === 0"
          >
            <v-icon>mdi-chevron-left</v-icon>
          </v-btn>

          <!-- Grid con overflow -->
          <div class="carousel-container">
            <div class="carousel-row" :style="{ transform: `translateX(-${currentSlide * slideWidth}%)` }">
              <div
                v-for="producto in productos"
                :key="producto.id"
                class="carousel-col"
              >
                <div class="similar-product-card">
                  <!-- Imagen -->
                  <div class="product-image-wrapper">
                    <div @click="viewProduct(producto.id)" style="cursor: pointer; height: 100%;">
                      <v-img
                        v-if="producto.galeria_imagenes && producto.galeria_imagenes.length > 0"
                        :src="producto.galeria_imagenes[0]"
                        :alt="producto.name"
                        height="280"
                        cover
                        class="product-image"
                      />
                      <div v-else class="product-image-fallback">
                        <span class="fallback-emoji">{{ producto.name.charAt(0) }}</span>
                      </div>
                    </div>
                    
                    <!-- Overlay con promoción -->
                    <div v-if="producto.promocion && esPromocionActiva(producto.promocion)" class="product-badge">
                      <v-chip color="error" size="small" variant="elevated">
                        -{{ parseInt(producto.promocion.descuento) }}% OFF
                      </v-chip>
                    </div>
                  </div>

                  <!-- Contenido -->
                  <div class="product-content">
                    <div class="product-meta">
                      <span class="product-category">{{ typeof producto.category === 'string' ? producto.category : producto.category?.nombre }}</span>
                    </div>
                    
                    <h3 class="product-title" @click="viewProduct(producto.id)" style="cursor: pointer;">
                      {{ producto.name }}
                    </h3>
                    
                    <p class="product-brand">
                      {{ typeof producto.brand === 'string' ? producto.brand : producto.brand?.nombre }}
                    </p>
                    
                    <!-- Precio -->
                    <div class="product-price-section">
                      <div v-if="esPromocionActiva(producto.promocion)" class="price-with-discount">
                        <span class="price-original">${{ formatearPrecio(producto.price) }}</span>
                        <span class="price-final">${{ formatearPrecio(precioFinal(producto)) }}</span>
                      </div>
                      <div v-else class="price-normal">
                        <span class="price-final">${{ formatearPrecio(producto.price) }}</span>
                      </div>
                    </div>

                    <!-- Botón de acción -->
                    <v-btn 
                      color="primary" 
                      variant="elevated"
                      block
                      size="large"
                      class="product-action-btn mt-3"
                      @click="viewProduct(producto.id)"
                    >
                      <v-icon start>mdi-arrow-right</v-icon>
                      Ver producto
                    </v-btn>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botón siguiente -->
          <v-btn
            v-if="productos.length > itemsPerPage"
            icon
            class="carousel-btn carousel-btn--next"
            @click="nextSlide"
            :disabled="currentSlide >= productos.length - itemsPerPage"
          >
            <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
        </div>

        <!-- Indicadores -->
        <div v-if="productos.length > itemsPerPage" class="carousel-indicators">
          <div
            v-for="(item, index) in indicatorCount"
            :key="index"
            class="indicator"
            :class="{ 'indicator--active': index === currentSlide }"
            @click="currentSlide = index"
          ></div>
        </div>

        <!-- Ver más productos de la categoría -->
        <div v-if="productos && productos.length > 0" class="text-center mt-12">
          <v-btn 
            color="primary" 
            variant="outlined"
            size="large"
            prepend-icon="mdi-view-grid"
            class="see-all-btn"
            @click="verTodosProductos"
          >
            <span v-if="categoriaNombre">Ver todos los productos de {{ categoriaNombre }}</span>
            <span v-else-if="industriaNombre">Ver todos los productos en {{ industriaNombre }}</span>
            <span v-else-if="marcaNombre">Ver todos los productos de {{ marcaNombre }}</span>
          </v-btn>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  categoriaId: {
    type: [Number, String],
    default: null
  },
  industria: {
    type: String,
    default: null
  },
  marcaId: {
    type: [Number, String],
    default: null
  },
  productoActualId: {
    type: [Number, String],
    required: true
  },
  categoriaNombre: {
    type: String,
    default: ''
  },
  industriaNombre: {
    type: String,
    default: ''
  },
  marcaNombre: {
    type: String,
    default: ''
  }
})

// Estados reactivos
const productos = ref([])
const cargando = ref(false)
const currentSlide = ref(0)
const itemsPerPage = ref(3)
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024)

// Calcular items por página según ancho de pantalla
const calculateItemsPerPage = () => {
  if (windowWidth.value < 640) {
    itemsPerPage.value = 1
  } else if (windowWidth.value < 1024) {
    itemsPerPage.value = 2
  } else {
    itemsPerPage.value = 3
  }
  currentSlide.value = 0
}

const slideWidth = computed(() => 100 / itemsPerPage.value)

const indicatorCount = computed(() => {
  if (productos.value.length <= itemsPerPage.value) return 1
  return productos.value.length - itemsPerPage.value + 1
})

// Funciones de navegación
const nextSlide = () => {
  if (currentSlide.value < productos.value.length - itemsPerPage.value) {
    currentSlide.value++
  }
}

const previousSlide = () => {
  if (currentSlide.value > 0) {
    currentSlide.value--
  }
}

// Event listener para resize
onMounted(async () => {
  // console.log('🔧 ProductosSimilares montado con props:', {
  //   categoriaId: props.categoriaId,
  //   industria: props.industria,
  //   marcaId: props.marcaId,
  //   productoActualId: props.productoActualId
  // })
  
  await cargarProductosSimilares()
  
  window.addEventListener('resize', () => {
    windowWidth.value = window.innerWidth
    calculateItemsPerPage()
  })
  
  calculateItemsPerPage()
})

// Función para cargar productos similares
const cargarProductosSimilares = async () => {
  // Verificar que al menos uno de los filtros esté disponible
  if (!props.categoriaId && !props.industria && !props.marcaId) {
    // console.log('❌ No hay filtros disponibles:', { categoriaId: props.categoriaId, industria: props.industria, marcaId: props.marcaId })
    return
  }
  
  // console.log('🚀 Props recibido:', {
  //   categoriaId: props.categoriaId,
  //   industria: props.industria,
  //   marcaId: props.marcaId,
  //   productoActualId: props.productoActualId,
  //   categoriaNombre: props.categoriaNombre,
  //   industriaNombre: props.industriaNombre,
  //   marcaNombre: props.marcaNombre
  // })
  
  try {
    cargando.value = true
    
    // Obtener todos los productos (aumentar per_page para asegurar que traemos suficientes)
    const response = await fetch('/api/products?per_page=1000')
    
    if (!response.ok) {
      throw new Error('Error al cargar productos: ' + response.status)
    }
    
    const payload = await response.json()
    const todosLosProductos = payload.data ?? []
    
    // console.log('📦 Total productos obtenidos:', todosLosProductos.length)
    // console.log('📋 Primeros 3 productos estructura:', todosLosProductos.slice(0, 3).map(p => ({
    //   id: p.id,
    //   name: p.name,
    //   category: p.category,
    //   brand: p.brand,
    //   industria: p.industria
    // })))
    // console.log('🔍 Filtros activos:', { categoriaId: props.categoriaId, industria: props.industria, marcaId: props.marcaId })
    
    // Filtrar productos según el criterio disponible (prioridad: categoría > industria > marca)
    const productosFiltrados = todosLosProductos.filter(producto => {
      // Excluir el producto actual
      if (producto.id == props.productoActualId) return false
      
      // Prioridad 1: Filtrar por categoría
      if (props.categoriaId) {
        const productoCategoriId = typeof producto.category === 'string' ? null : producto.category?.id
        const match = productoCategoriId == props.categoriaId
        // if (match) console.log('✅ Match categoría:', producto.name, 'expected:', props.categoriaId, 'got:', productoCategoriId)
        return match
      }
      
      // Prioridad 2: Filtrar por industria
      if (props.industria) {
        const industriasProducto = Array.isArray(producto.industria) ? producto.industria : []
        const match = industriasProducto.some(ind => ind?.toLowerCase() === props.industria?.toLowerCase())
        // if (match) console.log('✅ Match industria:', producto.name, 'expected:', props.industria, 'got:', industriasProducto)
        return match
      }
      
      // Prioridad 3: Filtrar por marca
      if (props.marcaId) {
        const productoMarcaId = typeof producto.brand === 'string' ? null : producto.brand?.id
        const match = productoMarcaId == props.marcaId
        // if (match) console.log('✅ Match marca:', producto.name, 'expected:', props.marcaId, 'got:', productoMarcaId)
        return match
      }
      
      return false
    })
    
    // console.log('🎯 Productos filtrados:', productosFiltrados.length)
    if (productosFiltrados.length === 0) {
      console.warn('⚠️ No se encontraron productos con los filtros:', { categoriaId: props.categoriaId, industria: props.industria, marcaId: props.marcaId })
    }
    
    // Limitar a 8 productos para carrusel
    productos.value = productosFiltrados.slice(0, 8)
    currentSlide.value = 0
    
  } catch (error) {
    console.error('❌ Error al cargar productos similares:', error)
  } finally {
    cargando.value = false
  }
}

// Recargar cuando los props cambien (navegación a otro producto)
watch(() => [props.categoriaId, props.industria, props.marcaId, props.productoActualId], () => {
  cargarProductosSimilares()
}, { immediate: false })
const hoy = new Date()

const esPromocionActiva = (promo) => {
  if (!promo) return false
  const inicio = new Date(promo.inicio)
  const fin = new Date(promo.fin)
  return hoy >= inicio && hoy <= fin
}

const precioFinal = (producto) => {
  if (!esPromocionActiva(producto.promocion)) return producto.price
  const descuento = parseFloat(producto.promocion.descuento)
  const precioOriginal = parseFloat(producto.price)
  const precioConDescuento = precioOriginal - (precioOriginal * descuento) / 100
  return precioConDescuento.toFixed(2)
}

// Función para formatear precios sin decimales innecesarios
const formatearPrecio = (precio) => {
  const numero = parseFloat(precio)
  if (isNaN(numero)) return '0'
  
  // Si el número es entero (sin decimales), mostrar sin decimales
  if (numero % 1 === 0) {
    return numero.toString()
  }
  
  // Si tiene decimales, mostrar con 2 decimales máximo
  return numero.toFixed(2).replace(/\.?0+$/, '')
}

const viewProduct = (id) => {
  router.push({ name: 'ProductDetail', params: { id } })
}

const verTodosProductos = () => {
  if (props.categoriaNombre) {
    router.push({ path: '/productos', query: { category: props.categoriaNombre } })
  } else if (props.industria) {
    router.push({ path: '/productos', query: { industria: props.industria } })
  } else if (props.marcaNombre) {
    router.push({ path: '/productos', query: { brand: props.marcaNombre } })
  }
}
</script>

<style scoped>
/* Container */
.similar-products-section {
  background: linear-gradient(135deg, #faf7f4 0%, #f2efec 100%);
  position: relative;
}

/* Header Styles */
.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-kicker {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 0.95rem;
  background: rgba(184, 151, 120, 0.12);
  color: #8c745f;
  font-size: 0.78rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  border-radius: 999px;
  margin-bottom: 1rem;
}

.section-title {
  font-family: var(--font-brand), serif;
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 500;
  color: #6b5b47;
  position: relative;
  margin-bottom: 1rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -12px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 1px;
  background: linear-gradient(90deg, transparent, #8c745f, transparent);
}

.accent-text {
  color: #f3d6b6;
  font-style: italic;
  font-weight: 400;
}

.section-subtitle {
  font-family: var(--font-brand), serif;
  font-size: 1.05rem;
  color: #8f7a65;
  font-weight: 300;
  margin-top: 1rem;
  max-width: 720px;
  margin-left: auto;
  margin-right: auto;
}

/* Carrusel */
.carousel-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-top: 3rem;
}

.carousel-container {
  flex: 1;
  overflow: hidden;
  width: 100%;
}

.carousel-row {
  display: flex;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  gap: 2rem;
  width: 100%;
}

.carousel-col {
  flex: 0 0 calc(33.333% - 1.334rem);
  min-width: calc(33.333% - 1.334rem);
}

.carousel-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  width: 50px !important;
  height: 50px !important;
  background: rgba(255, 252, 248, 0.95) !important;
  border: 1px solid rgba(184, 151, 120, 0.2) !important;
  color: #8c745f !important;
  box-shadow: 0 4px 12px rgba(107, 91, 71, 0.12) !important;
  transition: all 0.3s ease !important;
}

.carousel-btn:hover:not(:disabled) {
  background: rgba(140, 116, 95, 0.12) !important;
  border-color: rgba(140, 116, 95, 0.3) !important;
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 8px 20px rgba(107, 91, 71, 0.18) !important;
}

.carousel-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.carousel-btn--prev {
  left: -65px;
}

.carousel-btn--next {
  right: -65px;
}

/* Indicadores */
.carousel-indicators {
  display: flex;
  justify-content: center;
  gap: 0.75rem;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.indicator {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: rgba(184, 151, 120, 0.3);
  cursor: pointer;
  transition: all 0.3s ease;
}

.indicator:hover {
  background: rgba(184, 151, 120, 0.6);
}

.indicator--active {
  background: #8c745f;
  width: 28px;
  border-radius: 999px;
}

/* Product Card */
.similar-product-card {
  background: rgba(255, 252, 248, 0.95);
  border: 1px solid rgba(184, 151, 120, 0.16);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 14px 36px rgba(107, 91, 71, 0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  height: 100%;
  display: flex;
  flex-direction: column;
}

.similar-product-card:hover {
  transform: translateY(-10px);
  border-color: rgba(184, 151, 120, 0.28);
  background: rgba(255, 252, 248, 0.98);
  box-shadow: 0 22px 46px rgba(107, 91, 71, 0.14);
}

/* Image Container */
.product-image-wrapper {
  position: relative;
  height: 280px;
  overflow: hidden;
  background: linear-gradient(180deg, #f7f0e9 0%, #ecdfd2 100%);
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.similar-product-card:hover .product-image {
  transform: scale(1.05);
}

.product-image-fallback {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #d4c4b0, #b8977866);
  color: #ffffff;
  font-size: 4rem;
  font-weight: 500;
}

.fallback-emoji {
  font-family: var(--font-brand), serif;
  font-size: 3.5rem;
  color: #8c745f;
}

.product-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  z-index: 10;
}

/* Content */
.product-content {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-meta {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.product-category {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 0.35rem 0.75rem;
  background: rgba(184, 151, 120, 0.12);
  color: #8c745f;
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.product-title {
  font-family: var(--font-brand), serif;
  font-size: 1.15rem;
  font-weight: 500;
  color: #5d4a38;
  margin-bottom: 0.5rem;
  line-height: 1.3;
  transition: color 0.3s ease;
}

.product-title:hover {
  color: #8c745f;
}

.product-brand {
  font-family: var(--font-brand), serif;
  font-size: 0.85rem;
  color: #8f7a65;
  margin-bottom: 0.95rem;
  font-weight: 300;
}

/* Price Styles */
.product-price-section {
  margin-bottom: 1rem;
}

.price-with-discount,
.price-normal {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.price-original {
  font-family: var(--font-brand), serif;
  font-size: 0.95rem;
  color: #9b8874;
  text-decoration: line-through;
}

.price-final {
  font-family: var(--font-brand), serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: #8c745f;
}

/* Button */
.product-action-btn {
  border-radius: 18px !important;
  font-weight: 600;
  letter-spacing: 0.04em;
  margin-top: auto !important;
  background: linear-gradient(135deg, #8c745f, #a68b73) !important;
  transition: all 0.3s ease;
}

.product-action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(140, 116, 95, 0.25) !important;
}

.see-all-btn {
  border-color: rgba(140, 116, 95, 0.3) !important;
  color: #8c745f !important;
  border-radius: 20px !important;
  font-weight: 600;
  letter-spacing: 0.05em;
  transition: all 0.3s ease;
}

.see-all-btn:hover {
  background: #8c745f !important;
  color: #fffaf4 !important;
  border-color: #8c745f !important;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 1200px) {
  .carousel-btn--prev {
    left: -55px;
  }

  .carousel-btn--next {
    right: -55px;
  }

  .carousel-col {
    flex: 0 0 calc(50% - 1rem);
    min-width: calc(50% - 1rem);
  }
}

@media (max-width: 1024px) {
  .carousel-container {
    overflow: hidden;
  }

  .carousel-row {
    gap: 1.5rem;
  }

  .carousel-col {
    flex: 0 0 calc(50% - 0.75rem);
    min-width: calc(50% - 0.75rem);
  }
}

@media (max-width: 960px) {
  .section-title {
    font-size: 2rem;
  }

  .section-subtitle {
    font-size: 1rem;
  }

  .carousel-wrapper {
    gap: 0;
  }

  .carousel-btn--prev {
    left: 0;
  }

  .carousel-btn--next {
    right: 0;
  }

  .carousel-btn {
    width: 44px !important;
    height: 44px !important;
  }

  .product-image-wrapper {
    height: 240px;
  }

  .product-title {
    font-size: 1rem;
  }

  .price-final {
    font-size: 1.35rem;
  }
}

@media (max-width: 768px) {
  .carousel-row {
    gap: 1rem;
  }

  .product-content {
    padding: 1rem;
  }

  .carousel-btn {
    width: 40px !important;
    height: 40px !important;
  }

  .carousel-btn :deep(i) {
    font-size: 18px !important;
  }
}

@media (max-width: 480px) {
  .similar-products-section {
    padding-top: 2rem !important;
    padding-bottom: 2rem !important;
  }

  .section-header {
    margin-bottom: 2rem;
  }

  .section-title {
    font-size: 1.6rem;
  }

  .section-subtitle {
    font-size: 0.95rem;
  }

  .carousel-wrapper {
    gap: 0.5rem;
  }

  .carousel-btn {
    width: 36px !important;
    height: 36px !important;
  }

  .carousel-btn--prev {
    left: 0;
  }

  .carousel-btn--next {
    right: 0;
  }

  .carousel-col {
    flex: 0 0 100%;
    min-width: 100%;
  }

  .similar-product-card {
    border-radius: 18px;
  }

  .product-image-wrapper {
    height: 200px;
  }

  .product-content {
    padding: 0.75rem;
  }

  .product-title {
    font-size: 0.95rem;
  }

  .product-action-btn {
    font-size: 0.9rem;
  }

  .carousel-indicators {
    gap: 0.5rem;
    margin-top: 1.5rem;
  }

  .indicator {
    width: 8px;
    height: 8px;
  }

  .indicator--active {
    width: 24px;
  }
}
</style>
