<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\Tag;
use App\Jobs\ProcessPostFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(StorePostRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $disk = config('filesystems.default', 'public');
        
        $post = new Post();
        $post->user_id = $request->user()->id;
        $post->body = $validated['body'] ?? '';
        
        // Asignación de grupo opcional (si sube a una comunidad)
        if ($request->has('group_id')) {
            $post->group_id = $request->input('group_id');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('investigations', $disk);
            
            $post->file_path = $path;
            $post->file_name = $file->getClientOriginalName();
            $post->file_mime = $file->getMimeType();
            $post->file_size = $file->getSize();
        }

        $post->save();

        // 1. Tags (Extraemos como CSV o Array manual para la demostración)
        if ($request->has('tags')) {
            $tagNames = explode(',', $request->input('tags'));
            $tagIds = [];
            foreach ($tagNames as $name) {
                // Guarda etiqueta, ignora blancos y obtiene su ID
                $name = trim($name);
                if (!empty($name)) {
                    $tag = Tag::firstOrCreate(['name' => $name]);
                    $tagIds[] = $tag->id;
                }
            }
            $post->tags()->sync($tagIds);
        }

        // 2. Colas: Despachamos un Job en background si trae archivo
        if ($post->file_path) {
            ProcessPostFile::dispatch($post);
        }

        return response()->json([
            'success' => true,
            'message' => 'Investigación publicada exitosamente',
            'data' => $post->load(['tags', 'author']),
            'errors' => null,
        ], 201);
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        $isOwner = $post->user_id === $request->user()->id;
        $isAdmin = (bool) ($request->user()->is_admin ?? false);

        if (!$isOwner && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para eliminar esta publicación.',
            ], 403);
        }

        if ($post->file_path) {
            Storage::disk(config('filesystems.default', 'public'))->delete($post->file_path);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Publicación eliminada correctamente.',
        ]);
    }

    public function verify(Post $post)
    {
        Gate::authorize('verify-post');

        if (!$post->is_verified) {
             $post->update(['is_verified' => true]);
        }

        return response()->json(['message' => 'Post avalado académicamente.']);
    }
}
