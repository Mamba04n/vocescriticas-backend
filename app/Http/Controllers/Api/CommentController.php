<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Notifications\PostCommented;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'body' => $validated['body']
        ]);

        // Evitar notificarse a uno mismo
        if ($post->user_id !== $request->user()->id) {
            $post->author->notify(new PostCommented($request->user(), $post));
        }

        return response()->json([
            'message' => 'Comentario publicado.',
            'comment' => $comment->load('author:id,name,avatar_url,role')
        ], 201);
    }
}
