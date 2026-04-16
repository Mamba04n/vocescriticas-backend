<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        $userId = $request->user()->id;

        $like = Like::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Like removido', 'liked' => false]);
        }

        Like::create([
            'post_id' => $post->id,
            'user_id' => $userId
        ]);

        return response()->json(['message' => 'Like agregado', 'liked' => true]);
    }
}
