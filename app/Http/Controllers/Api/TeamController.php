<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Group;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Group $group)
    {
        $teams = $group->teams()->with('members')->get();
        return response()->json(['teams' => $teams]);
    }

    public function store(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $team = $group->teams()->create([
            'name' => $request->name
        ]);

        return response()->json(['team' => $team->load('members')]);
    }

    public function addMember(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if (!$team->members()->where('user_id', $request->user_id)->exists()) {
            $team->members()->attach($request->user_id);
        }

        return response()->json(['team' => $team->load('members')]);
    }

    public function removeMember(Team $team, $user)
    {
        $team->members()->detach($user);
        return response()->json(['team' => $team->load('members')]);
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(['message' => 'Team deleted successfully']);
    }
}
