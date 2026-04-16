<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookmarkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // 2. Obtener lista de Investigaciones guardadas por el usuario, preparadas para "Leer Más Tarde"
        $bookmarks = Bookmark::where('user_id', $request->user()->id)
            ->with(['post:id,user_id,body,file_name,is_verified,created_at', 'post.author:id,name,role,avatar_url'])
            ->latest()
            ->paginate(15);
        
        return response()->json([
            'message' => 'Tus investigaciones guardadas',
            'data' => $bookmarks
        ]);
    }

    public function toggle(Request $request, Post $post): JsonResponse
    {
        $userId = $request->user()->id;

        $bookmark = Bookmark::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json(['message' => 'Post removido de tus guardados', 'bookmarked' => false]);
        }

        Bookmark::create([
            'post_id' => $post->id,
            'user_id' => $userId
        ]);

        return response()->json(['message' => 'Post guardado en tu biblioteca para leer luego', 'bookmarked' => true], 201);
    }
}
