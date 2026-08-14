<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController
{
    public function index()
    {
        $marcas = Marca::orderBy('orden')->get();
        return response()->json($marcas);
    }

    public function show($id)
    {
        $marca = Marca::with('productos')->find($id);

        if (!$marca) {
            return response()->json(['message' => 'Marca no encontrada'], 404);
        }

        return response()->json($marca);
    }
}
