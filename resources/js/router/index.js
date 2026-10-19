import { createRouter, createWebHistory } from 'vue-router';

// Importar las páginas/vistas - Autenticación
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';

// Importar las páginas/vistas - Ecommerce
import Home from '../pages/Home.vue';
import About from '../pages/About.vue';
import Contact from '../pages/Contact.vue';
import ProductList from '../pages/ProductList.vue';
import ProductDetail from '../pages/ProductDetail.vue';
import Cart from '../pages/Cart.vue';
import Checkout from '../pages/Checkout.vue';
import PaymentSuccess from '../pages/PaymentSuccess.vue';
import PaymentPending from '../pages/PaymentPending.vue';
import PaymentFailure from '../pages/PaymentFailure.vue';
import CustomerOrders from '../pages/CustomerOrders.vue';
import OrderTracking from '../pages/OrderTracking.vue';

// Importar las páginas/vistas - Plataforma de Firmas
import Dashboard from '../pages/Dashboard.vue';
import DocumentList from '../pages/DocumentList.vue';
import NewDocument from '../pages/NewDocument.vue';
import DocumentDetail from '../pages/DocumentDetail.vue';
import Signers from '../pages/Signers.vue';
import Settings from '../pages/Settings.vue';

// Importar composable de autenticación
import { useAuth } from '../composables/useAuth';

const routes = [
  // Rutas públicas
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresAuth: false }
  },
  {
    path: '/productos',
    name: 'ProductList',
    component: ProductList,
    meta: { requiresAuth: false }
  },
  {
    path: '/nosotros',
    name: 'About',
    component: About,
    meta: { requiresAuth: false }
  },
  {
    path: '/contacto',
    name: 'Contact',
    component: Contact,
    meta: { requiresAuth: false }
  },
  {
    path: '/producto/:id',
    name: 'ProductDetail',
    component: ProductDetail,
    meta: { requiresAuth: false }
  },
  {
    path: '/carrito',
    name: 'Cart',
    component: Cart,
    meta: { requiresAuth: false }
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: Checkout,
    meta: { requiresAuth: false }
  },
  {
    path: '/checkout/exito',
    name: 'PaymentSuccess',
    component: PaymentSuccess,
    meta: { requiresAuth: false }
  },
  {
    path: '/checkout/pendiente',
    name: 'PaymentPending',
    component: PaymentPending,
    meta: { requiresAuth: false }
  },
  {
    path: '/checkout/error',
    name: 'PaymentFailure',
    component: PaymentFailure,
    meta: { requiresAuth: false }
  },
  {
    path: '/mis-pedidos',
    name: 'CustomerOrders',
    component: CustomerOrders,
    meta: { requiresAuth: false }
  },
  {
    path: '/seguimiento-pedido/:orderNumber',
    name: 'OrderTracking',
    component: OrderTracking,
    props: true,
    meta: { requiresAuth: false }
  },

  // Rutas de autenticación (público, pero redirige si ya está logueado)
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false, isAuthPage: true }
  },
  {
    path: '/registro',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false, isAuthPage: true }
  },

  // Rutas - Plataforma de Firmas Electrónicas (protegidas)
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/documentos',
    name: 'DocumentList',
    component: DocumentList,
    meta: { requiresAuth: true }
  },
  {
    path: '/documento/nuevo',
    name: 'NewDocument',
    component: NewDocument,
    meta: { requiresAuth: true }
  },
  {
    path: '/documento/:id',
    name: 'DocumentDetail',
    component: DocumentDetail,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/firmantes',
    name: 'Signers',
    component: Signers,
    meta: { requiresAuth: true }
  },
  {
    path: '/configuracion',
    name: 'Settings',
    component: Settings,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Si hay una posición guardada (navegación hacia atrás/adelante)
    if (savedPosition) {
      return savedPosition;
    }
    // Siempre volver al inicio en navegación nueva
    return { top: 0, behavior: 'smooth' };
  }
});

/**
 * Guard global para proteger rutas
 */
// Flag para controlar si la autenticación ha sido inicializada
let authInitialized = false;

router.beforeEach(async (to, from, next) => {
  const { isAuthenticated, initAuth } = useAuth();

  // Inicializar autenticación solo una vez
  if (!authInitialized) {
    await initAuth();
    authInitialized = true;
  }

  // Si la ruta requiere autenticación
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    // Redirigir a login
    next({ name: 'Login', query: { redirect: to.fullPath } });
  }
  // Si es página de autenticación y ya está logueado
  else if (to.meta.isAuthPage && isAuthenticated.value) {
    // Redirigir al dashboard
    next({ name: 'Dashboard' });
  }
  // Permitir navegación
  else {
    next();
  }
});

export default router;
