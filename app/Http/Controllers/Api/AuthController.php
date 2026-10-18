<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController
{
    /**
     * Iniciar sesión con correo y contraseña.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'password.required' => 'La contraseña es requerida.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no coinciden con nuestros registros.']
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Sesión iniciada correctamente.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'profile' => $user->profile,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Registrar un nuevo usuario.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'El nombre es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'cliente',
            'activo' => 1,
        ]);

        // Crear perfil asociado
        $user->profile()->create([
            'telefono' => null,
            'calle' => null,
            'numero_exterior' => null,
            'numero_interior' => null,
            'colonia' => null,
            'entre_calles' => null,
            'codigo_postal' => null,
            'ciudad' => null,
            'estado' => null,
            'pais' => 'México',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registro completado exitosamente. Sesión iniciada.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'profile' => $user->profile,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Cerrar sesión (revocar tokens).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ], 200);
    }

    /**
     * Obtener usuario autenticado actual.
     */
    public function getProfile(Request $request)
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'profile' => $user->profile,
            ],
        ], 200);
    }

    /**
     * Listar todos los usuarios (admin).
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'users' => $users,
        ], 200);
    }

    /**
     * Eliminar usuario (admin).
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente.',
        ], 200);
    }
}
