<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'carnet' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|in:student,teacher'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'carnet' => $validated['carnet'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'student',
        ]);

        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'carnet' => 'required|string',
            'password' => 'required'
        ]);

        $user = User::where('carnet', $request->carnet)->first();

        // Para retrocompatibilidad con admin alice (opcional)
        if (!$user) {
            $user = User::where('email', $request->carnet)->first();
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'carnet' => ['El carnet o contraseña son incorrectos.'],
            ]);
        }

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user
        ]);
    }
}
