import { ref, computed, watch } from 'vue';

const CART_KEY = 'serviram_cart';

// Estado del carrito (compartido entre todos los componentes)
const cartItems = ref([]);

// Cargar carrito del localStorage al iniciar
const loadCart = () => {
  const saved = localStorage.getItem(CART_KEY);
  if (saved) {
    try {
      cartItems.value = JSON.parse(saved);
    } catch (e) {
      cartItems.value = [];
    }
  }
};

// Guardar carrito en localStorage
const saveCart = () => {
  localStorage.setItem(CART_KEY, JSON.stringify(cartItems.value));
};

// Inicializar carrito
loadCart();

// Observar cambios y guardar automáticamente
watch(cartItems, saveCart, { deep: true });

export function useCart() {
  // Agregar producto al carrito
  const addToCart = (product, quantity = 1) => {
    const existingItem = cartItems.value.find(item => item.id === product.id);

    // console.log('Adding to cart:', product, 'Quantity:', quantity);

    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      cartItems.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.galeria_imagenes.length > 0 ? product.galeria_imagenes[0] : null,
        category: product.category,
        quantity: quantity
      });
    }
  };

  // Remover producto del carrito
  const removeFromCart = (productId) => {
    const index = cartItems.value.findIndex(item => item.id === productId);
    if (index > -1) {
      cartItems.value.splice(index, 1);
    }
  };

  // Actualizar cantidad
  const updateQuantity = (productId, quantity) => {
    const item = cartItems.value.find(item => item.id === productId);
    if (item) {
      item.quantity = Math.max(1, quantity);
    }
  };

  // Limpiar carrito
  const clearCart = () => {
    cartItems.value = [];
  };

  // Total de items
  const itemCount = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
  });

  // Subtotal
  const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  });

  // Total (puedes agregar envío o impuestos aquí)
  const total = computed(() => {
    return subtotal.value;
  });

  return {
    cartItems,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    itemCount,
    subtotal,
    total
  };
}
