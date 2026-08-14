<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController 
{
    public function index()
    {
        $categorias = Categoria::orderBy('orden')->get();
        return response()->json($categorias);
    }

    public function show($id)
    {
        $categoria = Categoria::with('productos')->find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        return response()->json($categoria);
    }
}
