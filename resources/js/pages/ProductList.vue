<template>
  <v-container class="product-list">
    <section class="catalog-hero">
      <span class="catalog-kicker">Catálogo de productos</span>
      <h1 class="catalog-title">Equipos profesionales para tu cocina</h1>
      <p class="catalog-text">
        Descubre nuestro amplio catálogo de equipos de cocina de última generación. Desde marcas líderes como Rational, Unox y Winterhalter, con tecnología innovadora para optimizar tu negocio gastronómico.
      </p>
    </section>

    <section class="filters-shell mb-8">
      <div class="filters-copy">
        <span class="filters-label">Filtrar catálogo</span>
        <p class="filters-summary">
          {{ totalProducts }} {{ totalProducts === 1 ? 'producto disponible' : 'productos disponibles' }}
        </p>
      </div>

      <v-row class="filters-grid" align="center">
        <v-col cols="12" md="2">
          <v-select
            v-model="selectedCategory"
            :items="categories"
            label="Categoría"
            variant="outlined"
            density="comfortable"
            hide-details
            item-title="title"
            item-value="value"
            prepend-inner-icon="mdi-shape-outline"
            class="filter-field"
          ></v-select>
        </v-col>
        <v-col cols="12" md="2">
          <v-select
            v-model="selectedBrand"
            :items="brands"
            label="Marca"
            variant="outlined"
            density="comfortable"
            hide-details
            item-title="title"
            item-value="value"
            prepend-inner-icon="mdi-tag-multiple"
            class="filter-field"
          ></v-select>
        </v-col>
        <v-col cols="12" md="2">
          <v-select
            v-model="selectedIndustry"
            :items="industries"
            label="Industria"
            variant="outlined"
            density="comfortable"
            hide-details
            item-title="title"
            item-value="value"
            prepend-inner-icon="mdi-factory"
            class="filter-field"
          ></v-select>
        </v-col>
        <v-col cols="12" md="2">
          <v-select
            v-model="selectedType"
            :items="types"
            label="Tipo"
            variant="outlined"
            density="comfortable"
            hide-details
            item-title="title"
            item-value="value"
            prepend-inner-icon="mdi-package-variant"
            class="filter-field"
          ></v-select>
        </v-col>
        <v-col cols="12" md="1">
          <v-select
            v-model="sortBy"
            :items="sortOptions"
            label="Ordenar"
            variant="outlined"
            density="comfortable"
            hide-details
            prepend-inner-icon="mdi-tune-vertical-variant"
            class="filter-field"
          ></v-select>
        </v-col>
        <v-col cols="12" md="3">
          <v-text-field
            v-model="searchQuery"
            label="Buscar por nombre"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="comfortable"
            hide-details
            clearable
            class="filter-field"
          ></v-text-field>
        </v-col>
      </v-row>
    </section>

    <!-- Grid de productos -->
    <v-row v-if="!loading" class="products-grid-row">
      <v-col
        v-for="product in filteredProducts"
        :key="product.id"
        cols="12" sm="6" md="4" lg="3"
      >
        <v-card class="product-card" hover @click="viewProduct(product.id)">


 
          <!-- <pre >{{ product }}</pre> -->
          <div class="product-card__media">
            <div class="product-card__badges">
              <span class="product-card__category">{{ formatCategory(product.category) }}</span>
              <span v-if="product.destacado" class="product-card__featured">Destacado</span>
            </div>
            <v-img
              v-if="product.galeria_imagenes && product.galeria_imagenes.length > 0"
              :src="product.galeria_imagenes[0]"
              :alt="product.name"
              class="product-card__image"
              contain
              height="280"
            />
            <div v-else class="product-card__fallback">
              <span class="product-card__emoji">{{ getProductEmoji(product.category) }}</span>
            </div>
          </div>
          <v-card-text class="product-card__content">
            <p v-if="product.brand" class="product-card__collection">{{ typeof product.brand === 'string' ? product.brand : product.brand.nombre }}</p>
            <h3 class="product-card__title">{{ product.name }}</h3>
            <p class="product-card__description">{{ truncateDescription(product.description) }}</p>
            <div class="product-card__meta">
              <span v-if="product.modelo" class="product-card__meta-item">{{ product.modelo }}</span>
              <span class="product-card__meta-item">${{ product.price }}</span>
            </div>
          </v-card-text>
          <v-card-actions class="product-card__actions">
            <span class="product-card__price">${{ product.price }}</span>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              size="small"
              class="product-card__button"
              @click.stop="addToCart(product)"
            >
              Agregar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <v-row v-if="loading">
      <v-col cols="12" class="text-center py-12">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
        <p class="text-h6 mt-4">Cargando productos...</p>
      </v-col>
    </v-row>

    <v-row v-if="!loading && filteredProducts.length === 0">
      <v-col cols="12" class="text-center py-12">
        <v-icon size="64" color="grey">mdi-package-variant</v-icon>
        <p class="text-h6 text-grey mt-4">No se encontraron productos</p>
      </v-col>
    </v-row>

    <div v-if="!loading && totalPages > 1 && filteredProducts.length > 0" class="pagination-shell">
      <v-pagination
        v-model="currentPage"
        :length="totalPages"
        :total-visible="$vuetify.display.smAndDown ? 4 : 7"
        rounded="circle"
        active-color="primary"
      />
    </div>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar" :timeout="2000" color="success">
      <v-icon start>mdi-check-circle</v-icon>
      {{ snackbarText }}
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCart } from '../composables/useCart';

const route = useRoute();
const router = useRouter();

const selectedCategory = ref('Todas');
const selectedBrand = ref('Todas');
const selectedIndustry = ref('Todas');
const selectedType = ref('Todas');
const sortBy = ref('name');
const search = ref('');
const searchQuery = ref('');
let searchTimeout = null;
const products = ref([]);
const categories = ref([{ title: 'Todas las piezas', value: 'Todas' }]);
const brands = ref([{ title: 'Todas las marcas', value: 'Todas' }]);
const industries = ref([
  { title: 'Todas las industrias', value: 'Todas' },
  { title: 'Restaurantes', value: 'restaurantes' },
  { title: 'Pizzería', value: 'pizzeria' },
  { title: 'Cafetería', value: 'cafetería' },
  { title: 'Carnicería', value: 'carniceria' },
  { title: 'Panadería', value: 'panadería' },
  { title: 'Pastelería', value: 'pastelería' },
  { title: 'Comedores Industrial', value: 'comedores industrial' },
  { title: 'Bares / Centro Nocturnos', value: 'bares / centro nocturnos' },
  { title: 'Hoteles', value: 'hoteles' },
]);
const types = ref([
  { title: 'Todos los tipos', value: 'Todas' },
  { title: 'Equipos', value: 'equipos' },
  { title: 'Refaccione', value: 'refaccione' },
  { title: 'Accesorios', value: 'accesorios' },
  { title: 'Consumibles', value: 'consumibles' },
]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const totalProducts = ref(0);

const sortOptions = [
  { title: 'Nombre', value: 'name' },
  { title: 'Precio: Menor a Mayor', value: 'price-asc' },
  { title: 'Precio: Mayor a Menor', value: 'price-desc' }
];

const categoryEmojis = {
  'Collares': '◌',
  'Pulseras': '⟡',
  'Aretes': '✧',
  'Anillos': '✦',
  'Accesorios': '✺',
};

// Cargar categorías desde API
const fetchCategories = async () => {
  try {
    const response = await fetch('/api/categorias');
    const data = await response.json();
    const categoryOptions = data.map(cat => ({
      title: cat.nombre,
      value: cat.nombre
    }));
    categories.value = [{ title: 'Todas las piezas', value: 'Todas' }, ...categoryOptions];
  } catch (error) {
    console.error('Error al cargar categorías:', error);
  }
};

// Cargar marcas desde API
const fetchBrands = async () => {
  try {
    const response = await fetch('/api/marcas');
    const data = await response.json();
    const brandOptions = data.map(brand => ({
      title: brand.nombre,
      value: brand.nombre
    }));
    brands.value = [{ title: 'Todas las marcas', value: 'Todas' }, ...brandOptions];
  } catch (error) {
    console.error('Error al cargar marcas:', error);
  }
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();

    if (selectedCategory.value && selectedCategory.value !== 'Todas') {
      params.append('category', selectedCategory.value);
    }

    if (selectedBrand.value && selectedBrand.value !== 'Todas') {
      params.append('brand', selectedBrand.value);
    }

    if (selectedIndustry.value && selectedIndustry.value !== 'Todas') {
      params.append('industria', selectedIndustry.value);
    }

    if (selectedType.value && selectedType.value !== 'Todas') {
      params.append('tipo', selectedType.value);
    }

    if (search.value) {
      params.append('search', search.value);
    }

    if (sortBy.value) {
      params.append('sort', sortBy.value);
    }

    params.append('page', currentPage.value);

    const response = await fetch(`/api/products?${params}`);

    if (!response.ok) throw new Error('Error en la respuesta');

    const payload = await response.json();

    products.value = payload.data ?? [];
    currentPage.value = payload.current_page ?? 1;
    totalPages.value = payload.last_page ?? 1;
    totalProducts.value = payload.total ?? products.value.length;
  } catch (error) {
    console.error('Error al cargar productos:', error);
    products.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  // Cargar opciones de filtros
  await fetchCategories();
  await fetchBrands();
  
  // Leer parámetros de query de la URL
  if (route.query.category) {
    selectedCategory.value = route.query.category;
  }
  if (route.query.brand) {
    selectedBrand.value = route.query.brand;
  }
  if (route.query.industria) {
    selectedIndustry.value = route.query.industria;
  }
  if (route.query.tipo) {
    selectedType.value = route.query.tipo;
  }
  if (route.query.search) {
    searchQuery.value = route.query.search;
    search.value = route.query.search;
  }
  
  // Cargar productos con los filtros de la URL
  fetchProducts();
});

watch([selectedCategory, selectedBrand, selectedIndustry, selectedType, sortBy, search], () => {
  // Actualizar URL con los parámetros de query
  const query = {};
  
  if (selectedCategory.value && selectedCategory.value !== 'Todas') {
    query.category = selectedCategory.value;
  }
  if (selectedBrand.value && selectedBrand.value !== 'Todas') {
    query.brand = selectedBrand.value;
  }
  if (selectedIndustry.value && selectedIndustry.value !== 'Todas') {
    query.industria = selectedIndustry.value;
  }
  if (selectedType.value && selectedType.value !== 'Todas') {
    query.tipo = selectedType.value;
  }
  if (search.value) {
    query.search = search.value;
  }
  
  // Actualizar ruta sin recargar la página
  router.replace({ path: '/productos', query });
  
  if (currentPage.value !== 1) {
    currentPage.value = 1;
    return;
  }

  fetchProducts();
});

// Debounce para la búsqueda
watch(searchQuery, (newValue) => {
  // Cancelar timeout anterior si existe
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  
  // Establecer nuevo timeout para 500ms
  searchTimeout = setTimeout(() => {
    search.value = newValue;
    currentPage.value = 1;
  }, 500);
});

watch(currentPage, () => {
  window.scrollTo(0, 0);
  fetchProducts();
});

const filteredProducts = computed(() => products.value);

const { addToCart: addProductToCart } = useCart();

const snackbar = ref(false);
const snackbarText = ref('');

const addToCart = (product) => {
  addProductToCart(product, 1);
  snackbarText.value = `${product.name} agregado al carrito`;
  snackbar.value = true;
};

const viewProduct = (id) => {
  router.push({ name: 'ProductDetail', params: { id } });
};

const getProductEmoji = (category) => {
  const categoryName = typeof category === 'string' ? category : category?.nombre
  return categoryEmojis[categoryName] || '✺';
};

const formatCategory = (category) => {
  if (!category) {
    return 'Pieza artesanal'
  }
  const categoryName = typeof category === 'string' ? category : category.nombre
  return categoryName || 'Pieza artesanal';
};

const stripHtml = (html) => {
  const div = document.createElement('div');
  div.innerHTML = html;
  return div.textContent || div.innerText || '';
};

const truncateDescription = (description) => {
  if (!description) {
    return 'Una pieza diseñada para destacar el detalle, la textura y la memoria de su origen.';
  }

  const cleanText = stripHtml(description).trim();
  return cleanText.length > 110 ? `${cleanText.slice(0, 110)}...` : cleanText;
};
</script>

<style scoped>
.product-list {
  margin-top: 150px !important;
  padding-bottom: 4rem;
}

.catalog-hero {
  max-width: 860px;
  margin-bottom: 2rem;
}

.pagination-shell {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
}

.catalog-kicker,
.filters-label,
.product-card__category,
.product-card__featured {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.catalog-kicker {
  padding: 0.5rem 0.95rem;
  background: rgba(30, 90, 160, 0.12);
  color: #1e5aa0;
  font-size: 0.78rem;
}

.catalog-title {
  margin: 1rem 0 0.85rem;
  font-family: var(--font-brand), serif;
  font-size: clamp(2.4rem, 5vw, 4rem);
  line-height: 1.05;
  color: #0b3066;
}

.catalog-text {
  max-width: 720px;
  color: #2e7ab5;
  line-height: 1.85;
  font-size: 1.05rem;
}

.filters-shell {
  padding: 1.35rem;
  border-radius: 28px;
  background: rgba(240, 244, 248, 0.92);
  border: 1px solid rgba(30, 90, 160, 0.16);
  box-shadow: 0 18px 42px rgba(30, 90, 160, 0.08);
}

.filters-copy {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.filters-label {
  padding: 0.45rem 0.85rem;
  background: rgba(30, 90, 160, 0.08);
  color: #8c745f;
  font-size: 0.76rem;
}

.filters-summary {
  color: #7f6d5a;
  font-size: 0.98rem;
  margin: 0;
}

.filter-field :deep(.v-field) {
  border-radius: 18px;
  background: rgba(232, 238, 246, 0.88);
}

.filter-field :deep(.v-field__prepend-inner i),
.filter-field :deep(.v-label.v-field-label) {
  color: #8c745f;
}

.product-card {
  cursor: pointer;
  border-radius: 24px !important;
  border: 1px solid rgba(184, 151, 120, 0.16);
  background: rgba(240, 244, 248, 0.95) !important;
  box-shadow: 0 16px 36px rgba(107, 91, 71, 0.08) !important;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 24px 48px rgba(107, 91, 71, 0.14) !important;
  border-color: rgba(30, 90, 160, 0.28);
}

.product-card__media {
  position: relative;
  background: linear-gradient(180deg, #f0f4f8 0%, #e8eef6 100%);
  padding: 1rem;
}

.product-card__badges {
  position: absolute;
  top: 1rem;
  left: 1rem;
  right: 1rem;
  z-index: 1;
  display: flex;
  justify-content: space-between;
  gap: 0.5rem;
}

.product-card__category,
.product-card__featured {
  padding: 0.38rem 0.75rem;
  font-size: 0.68rem;
  backdrop-filter: blur(8px);
}

.product-card__category {
  background: rgba(232, 238, 246, 0.86);
  color: #6b5b47;
}

.product-card__featured {
  background: rgba(30, 90, 160, 0.82);
  color: #ffffff;
}

.product-card__image {
  border-radius: 18px;
  background: linear-gradient(135deg, #f0f4f8 0%, #e8eef6 100%) !important;
  aspect-ratio: 1;
}

.product-card__image :deep(img) {
  object-fit: contain;
  object-position: center;
}

.product-card__fallback {
  min-height: 280px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 18px;
  background: linear-gradient(135deg, #f0f4f8 0%, #e8eef6 100%);
}

.product-card__emoji {
  font-size: 4.2rem;
  color: #8c745f;
}

.product-card__content {
  padding: 1.25rem 1.25rem 0.8rem !important;
}

.product-card__title {
  font-family: var(--font-brand), serif;
  font-size: 1.2rem;
  color: #5d4a38;
  margin-bottom: 0.65rem;
}

.product-card__collection {
  margin-bottom: 0.45rem;
  color: #8c745f;
  font-size: 0.78rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.product-card__description {
  color: #7f6d5a;
  line-height: 1.65;
  min-height: 4.9rem;
}

.product-card__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.95rem;
}

.product-card__meta-item {
  display: inline-flex;
  align-items: center;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  background: rgba(240, 244, 248, 0.92);
  color: #7f6d5a;
  font-size: 0.78rem;
}

.product-card__meta-item--soldout {
  background: rgba(30, 90, 160, 0.1);
  color: #1e5aa0;
}

.product-card__actions {
  padding: 0 1.25rem 1.25rem !important;
  align-items: center;
}

.product-card__price {
  font-family: var(--font-brand), serif;
  font-size: 1.35rem;
  font-weight: 700;
  color: #8c745f;
}

.product-card__button {
  border-radius: 999px !important;
  font-weight: 600;
}

@media (max-width: 600px) {
  .product-list {
    margin-top: 100px !important;
  }

  .filters-shell {
    padding: 1rem;
    border-radius: 22px;
  }

  .catalog-title {
    font-size: 2.4rem;
  }
}
</style>
