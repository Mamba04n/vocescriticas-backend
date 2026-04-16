<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    public function request(Request $request, User $user)
    {
        $followerId = $request->user()->id;

        if ($followerId === $user->id) {
            return response()->json(['message' => 'No puedes seguirte/enviarte solicitud a ti mismo'], 400);
        }

        $follow = Follow::where('follower_id', $followerId)->where('followed_id', $user->id)->first();

        if ($follow) {
            return response()->json(['message' => 'Ya existe una conexión o solicitud pendiente con este usuario.'], 400);
        }

        Follow::create([
            'follower_id' => $followerId,
            'followed_id' => $user->id,
            'status' => 'pending' // O cambiar a 'accepted' directo si quieres estilo X/Twitter
        ]);

        return response()->json(['message' => 'Solicitud enviada exitosamente']);
    }

    public function accept(Request $request, User $user)
    {
        // En este caso, el $user es quien ENVIÓ la solicitud, y el autenticado es el seguido
        $followedId = $request->user()->id;

        $follow = Follow::where('follower_id', $user->id)
            ->where('followed_id', $followedId)
            ->where('status', 'pending')
            ->first();

        if (!$follow) {
            return response()->json(['message' => 'No hay solicitudes pendientes de este usuario'], 404);
        }

        $follow->update(['status' => 'accepted']);

        return response()->json(['message' => 'Solicitud aceptada']);
    }
}
