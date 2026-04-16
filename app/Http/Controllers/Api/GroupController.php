<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|max:10240',
        ]);

        // Generate dynamic invite code
        $inviteCode = Str::random(8);

        $group = new Group([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'created_by' => $request->user()->id,
            // 'invite_code' => $inviteCode
        ]);

        // If the table was updated with invite_code, we try to set it.
        // Using a try-catch mapping or directly:
        try { $group->invite_code = $inviteCode; } catch (\Exception $e) {}

        if ($request->hasFile('cover')) {
            $group->cover_url = $request->file('cover')->store('groups/covers', 'public');
        }

        $group->save();

        // Agregar al creador como Admin del grupo
        $group->members()->attach($request->user()->id, ['role' => 'admin', 'status' => 'accepted']);

        // Refresco para asegurar que el count y el cÃƒÆ’Ã‚Â³digo estÃƒÆ’Ã‚Â©n presentes
        $group->loadCount('members');

        return response()->json(['message' => 'Grupo creado exitosamente', 'group' => $group], 201);
    }

            public function index(Request $request)
    {
        $groups = Group::with('members')->withCount('members')->get();
        return response()->json(['success' => true, 'groups' => $groups]);
    }

    public function myGroups(Request $request)
    {
        $user = $request->user();
        // $user->groups does not exist unless we define it.
        $groups = Group::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        return response()->json(['success' => true, 'groups' => $groups]);
    }

    public function show(Request $request, Group $group)
    {
        $group->load('members');
        
        $userTeam = $group->teams()->whereHas('members', function($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->with('members')->first();

        $posts = \App\Models\Post::with(['user', 'likes', 'comments.user'])
            ->where('group_id', $group->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'group' => $group,
            'userTeam' => $userTeam,
            'posts' => $posts
        ]);
    }

    public function join(Request $request, $code)
    {
        $group = Group::where('invite_code', $code)->first();
        if (!$group) {
            return response()->json(['message' => 'CÃƒÆ’Ã‚Â³digo de invitaciÃƒÆ’Ã‚Â³n invÃƒÆ’Ã‚Â¡lido o grupo no encontrado.'], 404);
        }

        // Check if already a member
        if ($group->members()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Ya eres miembro de este grupo.', 'group' => $group], 200);
        }

        // Attach as participant
        $group->members()->attach($request->user()->id, ['role' => 'member', 'status' => 'accepted']);
        
        return response()->json(['message' => 'Te has unido al grupo exitosamente.', 'group' => $group], 200);
    }

    public function destroy(Request $request, Group $group)
    {
        // Require super admin or creator role
        if (!$request->user()->is_admin && $group->created_by !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado para eliminar.'], 403);
        }

        $group->delete();
        return response()->json(['success' => true, 'message' => 'Grupo eliminado.']);
    }
}

