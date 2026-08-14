<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductoController 

{
    /**
     * Formatea un producto con los campos esperados por el frontend
     */
    private function formatearProducto($producto)
    {
        return [
            'id' => $producto->id,
            'name' => $producto->nombre_comercial,
            'description' => $producto->descripcion,
            'price' => (float) $producto->precio,
            'image' => $producto->imagen_slug ? Storage::url($producto->imagen_slug) : null,
            'category' => $producto->categoria ? [
                'id' => $producto->categoria->id,
                'nombre' => $producto->categoria->nombre
            ] : null,
            'brand' => $producto->marca ? [
                'id' => $producto->marca->id,
                'nombre' => $producto->marca->nombre
            ] : null,
            'destacado' => $producto->destacado,
            'modelo' => $producto->modelo,
            'item' => $producto->item,
            'medidas' => $producto->medidas,
            'peso' => $producto->peso,
            'industria' => $producto->industria,
            'tipo' => $producto->tipo,
            'ficha' => $producto->ficha ? Storage::url($producto->ficha) : null,
            'manual' => $producto->manual ? Storage::url($producto->manual) : null,
            'galeria_imagenes' => array_map(fn($img) => Storage::url($img), (array) $producto->galeria_imagenes),
            'promocion' => $producto->promocion,
        ];
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        // Limitar per_page a máximo 1000 para evitar problemas
        $perPage = min($perPage, 1000);
        $page = $request->input('page', 1);
        $sort = $request->input('sort', 'orden');
        $search = $request->input('search', '');
        $category = $request->input('category', '');
        $brand = $request->input('brand', '');
        $industria = $request->input('industria', '');
        $tipo = $request->input('tipo', '');

        $query = Producto::with(['categoria', 'marca', 'promocion'])->where('activo', true)->orderBy('orden', 'asc');

        // Filtro por búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_comercial', 'LIKE', "%$search%")
                  ->orWhere('descripcion', 'LIKE', "%$search%")
                  ->orWhere('modelo', 'LIKE', "%$search%")
                  ->orWhere('item', 'LIKE', "%$search%");
            });
        }

        // Filtro por categoría
        if ($category && $category !== 'Todas') {
            $query->whereHas('categoria', function ($q) use ($category) {
                $q->where('nombre', $category);
            });
        }

        // Filtro por marca
        if ($brand && $brand !== 'Todas') {
            $query->whereHas('marca', function ($q) use ($brand) {
                $q->where('nombre', $brand);
            });
        }

        // Filtro por industria
        if ($industria && $industria !== 'Todas') {
            $query->whereJsonContains('industria', $industria);
        }

        // Filtro por tipo
        if ($tipo && $tipo !== 'Todas') {
            $query->where('tipo', $tipo);
        }

        // Ordenamiento
        switch ($sort) {
            case 'price-asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'name':
                $query->orderBy('nombre_comercial', 'asc');
                break;
            default:
                $query->orderBy('orden', 'asc');
        }

        $productos = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => collect($productos->items())->map(fn($p) => $this->formatearProducto($p))->toArray(),
            'current_page' => $productos->currentPage(),
            'last_page' => $productos->lastPage(),
            'total' => $productos->total(),
            'per_page' => $productos->perPage(),
        ]);
    }
    public function destacado(Request $request)
    {
        $perPage = 6;
        $page = $request->input('page', 1);

        $productos = Producto::with(['categoria', 'marca', 'promocion'])
            ->where('destacado', true)
            ->where('activo', true)
            ->orderBy('orden')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => collect($productos->items())->map(fn($p) => $this->formatearProducto($p))->toArray(),
            'current_page' => $productos->currentPage(),
            'last_page' => $productos->lastPage(),
            'total' => $productos->total(),
            'per_page' => $productos->perPage(),
        ]);
    }
    public function show($id)
    {
        $producto = Producto::with(['categoria', 'marca', 'promocion'])->find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Log::info('Producto encontrado', $producto->toArray());

        return response()->json($this->formatearProducto($producto));
    }

    public function buscarPorSlug($slug)
    {
        $producto = Producto::with(['categoria', 'marca', 'promocion'])->where('slug', $slug)->first();

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($this->formatearProducto($producto));
    }
    public function buscar(Request $request)
    {
        try {
            $query = Producto::with(['categoria', 'marca', 'promocion']);

            if ($request->filled('precio_min') && $request->filled('precio_max')) {
                $query->whereBetween('precio', [
                    floatval($request->precio_min),
                    floatval($request->precio_max),
                ]);
            }

            if ($request->filled('categoria')) {
                $query->whereHas('categoria', function ($q) use ($request) {
                    $q->where('nombre', $request->categoria);
                });
            }

            if ($request->filled('marca')) {
                $query->whereHas('marca', function ($q) use ($request) {
                    $q->where('nombre', $request->marca);
                });
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->filled('industria')) {
                $query->where('industria', $request->industria);
            }

            if ($request->boolean('promocion')) {
                $query->whereHas('promocion');
            }

            $productos = $query->where('activo', true)->orderBy('orden')->get();

            return response()->json($productos->map(fn($p) => $this->formatearProducto($p)));
        } catch (\Exception $e) {
            Log::error('Error al buscar productos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Error en la búsqueda'], 500);
        }
    }
    public function buscarPorTexto(Request $request)
    {
        // Log::info('Búsqueda de productos por texto', [
        //     'request' => $request->all()
        // ]);
        try {
            $textoBusqueda = $request->input('q', '');

            if (empty(trim($textoBusqueda))) {
                return response()->json(['error' => 'Texto de búsqueda requerido'], 400);
            }

            $productos = Producto::with(['categoria', 'marca', 'promocion'])
                ->where('activo', true)
                ->where(function ($query) use ($textoBusqueda) {
                    $query->where('nombre_comercial', 'LIKE', '%' . $textoBusqueda . '%')
                          ->orWhere('modelo', 'LIKE', '%' . $textoBusqueda . '%')
                          ->orWhere('descripcion', 'LIKE', '%' . $textoBusqueda . '%')
                          ->orWhere('item', 'LIKE', '%' . $textoBusqueda . '%')
                          ->orWhereHas('categoria', function ($q) use ($textoBusqueda) {
                              $q->where('nombre', 'LIKE', '%' . $textoBusqueda . '%');
                          })
                          ->orWhereHas('marca', function ($q) use ($textoBusqueda) {
                              $q->where('nombre', 'LIKE', '%' . $textoBusqueda . '%');
                          });
                })
                ->orderBy('orden')
                ->get();

            return response()->json($productos->map(fn($p) => $this->formatearProducto($p)));
        } catch (\Exception $e) {
            Log::error('Error al buscar productos por texto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Error en la búsqueda'], 500);
        }
    }
}
