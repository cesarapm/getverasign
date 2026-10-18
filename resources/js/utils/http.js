import axios from 'axios';

// Crear instancia de axios configurada
const http = axios.create({
  baseURL: '/api',
  headers: {
    'Accept': 'application/json',
  },
});

// Obtener CSRF token del DOM (si existe)
const getCsrfToken = () => {
  const token = document.querySelector('meta[name="csrf-token"]');
  return token ? token.getAttribute('content') : null;
};

// Agregar CSRF token al header si existe
const csrfToken = getCsrfToken();
if (csrfToken) {
  http.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// Interceptor para agregar token Sanctum al header y manejar Content-Type
http.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  
  // Si el data es FormData, no establecer Content-Type
  // Axios y el navegador manejarán automáticamente el boundary de multipart
  if (!(config.data instanceof FormData)) {
    config.headers['Content-Type'] = 'application/json';
  }
  
  return config;
});

// Interceptor para manejar errores 401
http.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expirado o inválido
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
      
      // Redirigir a login (esto se hace desde la app)
      window.dispatchEvent(new Event('auth:logout'));
    }
    return Promise.reject(error);
  }
);

export default http;
