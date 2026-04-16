<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q', '');

        if (!$query) {
            return response()->json(['message' => 'Ingresa una query de bÃºsqueda.'], 422);
        }

        $users = User::where('name', 'LIKE', "%{$query}%")
                     ->orWhere('role', 'LIKE', "%{$query}%")
                     ->select('id', 'name', 'role', 'avatar_url')
                     ->take(5)
                     ->get();

        $groups = Group::where('name', 'LIKE', "%{$query}%")
                       ->select('id', 'name', 'cover_url')
                       ->take(5)
                       ->get();

        $posts = Post::where('body', 'LIKE', "%{$query}%")
                     ->orWhereHas('tags', function ($q) use ($query) {
                         $q->where('name', 'LIKE', "%{$query}%");
                     })
                     ->with(['author:id,name,role', 'group:id,name', 'tags'])
                     ->take(10)
                     ->get();

        return response()->json([
            'results' => [
                'users' => $users,
                'groups' => $groups,
                'posts' => $posts
            ]
        ]);
    }
}
