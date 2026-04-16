<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:5120', // 5MB máx
            'cover' => 'nullable|image|max:10240', // 10MB máx
        ]);

        if (array_key_exists('bio', $validated)) {
            $user->bio = $validated['bio'];
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $path;
        }

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $user->cover_url = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'user' => $user
        ]);
    }
}
