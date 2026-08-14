export const useProductos = () => {
  const config = useRuntimeConfig()
  
  // Función para obtener todos los productos
  const obtenerProductos = async () => {
    try {
      const response = await fetch(`${config.public.apiUrl}/productos`)
      if (!response.ok) {
        throw new Error('Error al obtener productos')
      }
      return await response.json()
    } catch (error) {
      console.error('Error al obtener productos:', error)
      throw error
    }
  }

  // Función para obtener productos destacados
  const obtenerProductosDestacados = async () => {
    try {
      const response = await fetch(`${config.public.apiUrl}/productos/destacados`)
      if (!response.ok) {
        throw new Error('Error al obtener productos destacados')
      }
      return await response.json()
    } catch (error) {
      console.error('Error al obtener productos destacados:', error)
      throw error
    }
  }

  // Función para buscar productos por slug
  const obtenerProductoPorSlug = async (slug) => {
    try {
      const response = await fetch(`${config.public.apiUrl}/productos/${slug}`)
      if (!response.ok) {
        throw new Error('Producto no encontrado')
      }
      return await response.json()
    } catch (error) {
      console.error('Error al obtener producto:', error)
      throw error
    }
  }

  // Función para buscar productos con filtros
  const buscarProductos = async (filtros = {}) => {
    try {
      const params = new URLSearchParams()
      
      // Agregar filtros a los parámetros
      Object.keys(filtros).forEach(key => {
        if (filtros[key] !== null && filtros[key] !== undefined && filtros[key] !== '') {
          if (Array.isArray(filtros[key])) {
            params.append(key, filtros[key].join(','))
          } else {
            params.append(key, filtros[key])
          }
        }
      })

      const response = await fetch(`${config.public.apiUrl}/buscar-productos?${params.toString()}`)
      if (!response.ok) {
        throw new Error('Error al buscar productos')
      }
      return await response.json()
    } catch (error) {
      console.error('Error al buscar productos:', error)
      throw error
    }
  }

  // Función para buscar productos por texto (nombre comercial)
  const buscarProductosPorTexto = async (texto) => {
    try {
      const params = new URLSearchParams({ q: texto })
      const response = await fetch(`${config.public.apiUrl}/buscar-texto?${params.toString()}`)
      
      if (!response.ok) {
        throw new Error('Error al buscar productos')
      }
      return await response.json()
    } catch (error) {
      console.error('Error al buscar productos por texto:', error)
      throw error
    }
  }

  return {
    obtenerProductos,
    obtenerProductosDestacados,
    obtenerProductoPorSlug,
    buscarProductos,
    buscarProductosPorTexto
  }
}