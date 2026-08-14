<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Log;

class AuthController
{


    public function index()
    {
        $users = User::all();


        $data = [
            'users' => $users,
            'status' => 200
        ];

        return response()->json($data, 200);
    }


    public function destroy($id)
    {
        // Encontrar el usuario por su ID
        $user = User::find($id);

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Almacenar el ID del usuario para la respuesta


        // Eliminar el usuario (esto también eliminará los sucursalautorizados debido a la eliminación en cascada)
        $user->delete();
        $users = User::all();


        return response()->json([
            'message' => 'Usuario eliminado correctamente',
            'users' => $users,
        ], 200);
    }

    public function register(Request $request)
    {
        //  Log::info('📩 Registro iniciado', $request->all()); // 🔍 Verifica lo que llega
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'in:admin,cliente',
            'activo' => 'integer|in:0,1',

            // perfil
            'telefono' => 'nullable|string',
            'calle' => 'nullable|string',
            'numero_exterior' => 'nullable|string',
            'numero_interior' => 'nullable|string',
            'colonia' => 'nullable|string',
            'entre_calles' => 'nullable|string',
            'codigo_postal' => 'nullable|string',
            'ciudad' => 'nullable|string',
            'estado' => 'nullable|string',
            'pais' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'cliente',
            'activo' => $request->activo ?? 1
        ]);

        $user->profile()->create([
            'telefono' => $request->telefono,
            'calle' => $request->calle,
            'numero_exterior' => $request->numero_exterior,
            'numero_interior' => $request->numero_interior,
            'colonia' => $request->colonia,
            'entre_calles' => $request->entre_calles,
            'codigo_postal' => $request->codigo_postal,
            'ciudad' => $request->ciudad,
            'estado' => $request->estado,
            'pais' => $request->pais ?? 'México',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user->load('profile'),
            'token' => $token,
        ], 201);
    }
}
