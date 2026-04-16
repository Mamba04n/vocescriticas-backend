<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    private function isAuthorized(Request $request): bool
    {
        $expected = (string) env('ADMIN_SETUP_SECRET', '');
        $provided = (string) $request->header('X-Setup-Secret', '');

        return $expected !== '' && hash_equals($expected, $provided);
    }

    public function deleteUser(Request $request): JsonResponse
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'carnet' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        if (empty($validated['carnet']) && empty($validated['email'])) {
            return response()->json(['message' => 'Debes enviar carnet o email.'], 422);
        }

        $user = User::query()
            ->when(!empty($validated['carnet']), fn ($q) => $q->where('carnet', $validated['carnet']))
            ->when(!empty($validated['email']), fn ($q) => $q->orWhere('email', $validated['email']))
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        $userName = $user->name;
        $userCarnet = $user->carnet;
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente.',
            'deleted_user' => [
                'name' => $userName,
                'carnet' => $userCarnet,
            ],
        ]);
    }

    public function createSuperUser(Request $request): JsonResponse
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'carnet' => 'required|string|unique:users,carnet',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'carnet' => $validated['carnet'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
            'is_admin' => true,
        ]);

        return response()->json([
            'message' => 'Superusuario creado correctamente.',
            'user' => $user,
        ], 201);
    }
}
